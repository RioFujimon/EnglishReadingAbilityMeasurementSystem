<?php
// 学生トップページ
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\ORM\Table;

class TestController extends AppUserController {

    public function initialize(){
        // 親クラスをロードする
        parent::initialize();
        // ページのタイトルセット
        $this->Erams->setTitle('テストページ');
        // セッションを確保
        $session = $this->request->getSession();
        // 統計情報（stat）をセッションから取得
        $stat = $session->read('Erams.Test.Stat');
        // stat が無ければ作成する
        if ( empty($stat) ) {
            $eid = $session->read('Erams.Test.Eid');
            if ( empty($eid) || ! preg_match('/^[0-9]+$/', $eid) ) {
                $session->delete('Erams.Test');
                $this->Flash->set("テストセットのidが指定されていません。");
                $this->redirect('/UserTop');
                return;
            }
            $session->delete('Erams.Test.Eid');
        }
        // stat があれば、この後の処理を抜けてしまう。
        else {
            // 情報をセット
            $this->set('EramsTestStat', $stat);
            return;
        }
        // 生徒の結果を保存する配列のフォーマット
        // $info['eid'] = eset の id
        // $info['rid'] = rset の id
        // $info['loading_time'] この関数が呼ばれた時間
        // $info['gid'], $info['gname'], $info['uid'], $info['uname'],
        // $info['r_seq'] : 現在実施してる section の番号
        // $info['a_seq'] : 現在実施してる question の番号
        // $sids[0-N:seq] = eset に含まれる sd の配列
        // $qids[0-N:seq][0-N:seq] = section に含まれる qid の 2 次元配列
        // $r_res[0-N:r_seq]
        // [ 'valid', 'start' => 開始, 'end' => 終了 'time' => 時間 ]
        // $a_res[0-N:r_seq][0-N:a_seq]
        // [ 'valid', 'start' => 開始, 'end' => 終了 'time' => 時間,
        //   'corrct' => 正解番号, 'answer' => 解答番号 'iscorrect' => 正解か ]
        // このページで必要とされる最低限の情報をDBから構築
        $now = date("Y-m-d H:i:s");
        $info = [
            'eid' => $eid,
            'rid' => -1,
            'loading_time' => $now,
            'gid' => $session->read('Erams.gid'),
            'gname' => $session->read('Erams.gname'),
            'uid' => $session->read('Erams.uid'),
            'uname' => $session->read('Erams.uname'),
            'r_seq' => -1,
            'a_seq' => -1
        ];
        $sids = array();
        $qids = array();
        $r_res_element = [
            'valid' => 0,
            'start' => "",
            'end' => ""
        ];
        $a_res_element = [
            'valid' => 0,
            'start' => "",
            'end' => "",
            'correct' => -1,
            'answer' => -1,
            'iscorrect' => "f"
        ];
        $r_res = array();
        $a_res = array();
        // eset に関するレコードを取得
        $row = $this->EramsDB->get('esets', null, [ 'id' => $eid, 'mode' => 1 ]);
        if ( $row->count() != 1 ) {
            $session->delete('Erams.Test');
            $this->Flash->set("テストセットが見つかりません。");
            $this->redirect('/PreTest/index?eid='.$eid);
            return;
        }
        // section に関する情報
        $row = $this->EramsDB->get('sections', 'subseq', [ 'eid' => $eid ]);
        if ( $row->count() == 0 ) {
            $session->delete('Erams.Test');
            $this->Flash->set("テストセットにセクションが含まれていません。");
            $this->redirect('/PreTest/index?eid='.$eid);
            return;
        }
        // $sids を構築
        $count = 0;
        foreach ( $row as $r ) {
            $sids[$count++] = $r->id;
        }
        // $sids から $r_res と $a_res の一部を構築
        $r_res = array_pad(array(), count($sids), $r_res_element);
        $a_res = array_pad(array(), count($sids), array());        
        // $gids を構築
        $count = 0;
        // $qids と $a_res の残りを構築
        for ( $c = 0 ; $c < count($sids) ; $c++ ) {
            $row = $this->EramsDB->get('questions', 'subseq', [ 'sid' => $sids[$c] ]);
            if ( $row->count() == 0 ) {
                $this->Flash->set("テストセットのセクションに問題が含まれていません。");
                $this->redirect('/PreTest/index?eid='.$eid);
                return;
            }
            $count = 0;
            $a_res[$c] = array();
            foreach ( $row as $r ) {
                // 問題
                $qids[$c][$count] = $r->id;
                $a_res[$c][$count] = $a_res_element;
                // 選択肢から解答を拾う
                $choices = $this->EramsDB->get('choices', 'subseq',
                [ 'qid' =>$r->id, 'correct' => 1 ]);
                if ( $choices->count() != 1 ) {
                    $this->Flash->set("問題の正解番号が取得できません。");
                    $this->redirect('/PreTest/index?eid='.$eid);
                    return;
                }
                // 解答番号をセットしておく
                $a_res[$c][$count]['correct'] = $choices->first()->subseq;
                $count++;
            }
        }
        // 結果データを保存するレコードを確保
        $rsets = $this->getTableLocator()->get('rsets');
        $r_ent = $rsets->newEntity();
        $r_ent->eid = $info['eid'];
        $r_ent->uid = $info['uid'];
        $r_ent->starttime = $now;
        $r_ent->endtime = date("Y-m-d H:i:s", 0);
        $r_ent->valid = 0;
        if ( ! $rsets->save($r_ent) ) {
            $this->Flash->set("結果データを保存するレコードの確保に失敗しました。");
            $this->redirect('/PreTest/index?eid='.$eid);
            return;
        }
        $info['rid'] = $r_ent->id;
        // 情報をセッションに保存
        $stat = [ 'info' => $info, 'sids' => $sids, 'qids' => $qids,
        'r_res' => $r_res, 'a_res' => $a_res ];
        $session->write('Erams.Test.Stat', $stat);
        // 情報をセット
        $this->set('EramsTestStat', $stat);
    }
    
    public function index() {
        // セッションを確保
        $session = $this->request->getSession();
        // 統計情報を取得
        $stat = $session->read('Erams.Test.Stat');
        if ( empty($stat) ) {
            // ログ出力
            $this->log( $stat['info']['uname'].' Test - index  eid: '.$stat['info']['eid'], 'info');
            
            $session->delete('Erams.Test');
            $this->Flash->set("予期せぬエラーが発生したのでテストを終了します。");
            $this->redirect('/UserTop');
            return;
        }
        // リダイレクトするべき場所を検査するためのカウンタデータを取得
        $r_seq = $stat['info']['r_seq'];
        $a_seq = $stat['info']['a_seq'];
        // リダイレクトするべき場所を検査するためにカウンタを更新
        if ( $r_seq < 0 ) {
            $r_seq = 0;
            $a_seq = -1;
        } else {
            $a_seq++;
            if ( count($stat['qids'][$r_seq]) <= $a_seq ) {
                $r_seq ++;
                $a_seq = -1;
            }
            if ( count($stat['sids']) <= $r_seq ) {
                $r_seq = 10000;
                $a_seq = 10000;                
            }
        }
        // 統計情報を書き戻す
        $stat['info']['r_seq'] = $r_seq;
        $stat['info']['a_seq'] = $a_seq;
        $session->write('Erams.Test.Stat', $stat);
        $this->set('Erams.Test.Stat', $stat); // View での debug 用
        // ページの描画を実施
        if ( $a_seq == -1 ) {
            // 読解用の情報を収集
            $sid = $stat['sids'][$r_seq];
            $row = $this->EramsDB->get('sections', null, [ 'id' => $sid ]);
            // ログ出力
            $this->log( $stat['info']['uname'].' Test - index(reading)  eid: '.
            $stat['info']['eid'].' sid: '.$sid, 'info');

            if ( $row->count() != 1 ) {
                $session->delete('Erams.Test');
                $this->Flash->set("予期せぬエラーが発生したので処理を中断します。1");
                $this->redirect('/UserTop');
                return;
            }
            $this->set('Erams.Test.Section', $row->first());
            $this->render("reading");            
            return;
        } else if ( $r_seq < 10000 ) {
            // 問題を収集
            $sid = $stat['sids'][$r_seq];
            $rows = $this->EramsDB->
                get('questions', null, [ 'sid' => $sid, 'subseq' => ($a_seq + 1)]);
            // ログ出力
            $aq = $a_seq + 1;
            $this->log( $stat['info']['uname'].' Test - index(asking)  eid: '.
            $stat['info']['eid'].' sid: '.$sid.' subseq: '.$aq, 'info');

            if ( $rows->count() != 1 ) {
                $session->delete('Erams.Test');
                $this->Flash->set("予期せぬエラーが発生したので処理を中断します。2");
                $this->redirect('/UserTop');
                return;
            }
            $this->set('Erams.Test.Question', $rows->first());
            $qid = $rows->first()->id;
            // 選択肢を収集
            $rows = $this->EramsDB->get('choices', 'subseq', [ 'qid' => $qid ]);
            if ( $rows->count() == 0 ) {
                $session->delete('Erams.Test');
                $this->Flash->set("予期せぬエラーが発生したので処理を中断します。3".$qid);
                $this->redirect('/UserTop');
                return;
            }
            $this->set('Erams.Test.Choices', $rows);
            $this->render("asking");
            return;
        }
        // ログ出力
        $this->log( $stat['info']['uname'].' Test - index(collect)  eid: '.$stat['info']['eid'], 'info');
        // 情報収集ページに移動
        $this->redirect("/Test/collect");
        return;        
    }

    public function readend() {
        // セッションを確保
        $session = $this->request->getSession();
        // 統計情報を取得
        $stat = $session->read('Erams.Test.Stat');        
        // リーデイング番号を取得する
        $r_seq = $stat['info']['r_seq'];
        // POST 情報から読み始めの時間と読み終わりの時間を取得する
        $st = $this->request->getData('stime');
        $et = $this->request->getData('etime');
        // stat 情報の変更
        $stat['r_res'][$r_seq]['valid'] = 1;
        $stat['r_res'][$r_seq]['start'] = $st;
        $stat['r_res'][$r_seq]['end'] = $et;

        // ログ出力
        $this->log( $stat['info']['uname'].' Test - readend  eid: '.
        $stat['info']['eid'].' sid: '.$stat['sids'][$r_seq], 'info');
        
        // stat 情報の書き戻し
        $session->write('Erams.Test.Stat', $stat);
        // ページを更新
        $this->redirect("/Test/index");
    }

    public function askend() {
        // セッションを確保
        $session = $this->request->getSession();
        // 統計情報を取得
        $stat = $session->read('Erams.Test.Stat');
        // リーデイングシーケンスを取得する
        $r_seq = $stat['info']['r_seq'];
        // 解答シーケーンスを取得する
        $a_seq = $stat['info']['a_seq'];
        // POST 情報から読み始めの時間と読み終わりの時間を取得する
        $st = $this->request->getData('stime');
        $et = $this->request->getData('etime');
        $answer = $this->request->getData('answer');
        // stat 情報の変更
        $stat['a_res'][$r_seq][$a_seq]['valid'] = 1;
        $stat['a_res'][$r_seq][$a_seq]['start'] = $st;
        $stat['a_res'][$r_seq][$a_seq]['end'] = $et;
        $stat['a_res'][$r_seq][$a_seq]['answer'] = $answer;

        // ログ出力
        $this->log( $stat['info']['uname'].' Test - askend  eid: '.$stat['info']['eid'].
        ' sid: '.$stat['sids'][$r_seq].' qid: '.$stat['qids'][$r_seq][$a_seq], 'info');
        
        // stat 情報の書き戻し
        $session->write('Erams.Test.Stat', $stat);
        // ページを更新
        $this->redirect("/Test/index");
    }

    public function collect() {
        // セッションを確保
        $session = $this->request->getSession();
        // 統計情報を取得
        $stat = $session->read('Erams.Test.Stat');
        
        // ログ出力
        $this->log( $stat['info']['uname'].' Test - collect  eid: '.$stat['info']['eid'], 'info');

        if ( empty($stat) ) {
            $this->Flash->set('再読み込み等のボタンは使用しないでください。');
            $this->redirect('/UserTop');
            return;
        }
        // 基本的な情報を構築
        $info = $stat['info'];
        $rid = $info['rid'];
        $uid = $info['uid'];
        $e_time = date("Y-m-d H:i:s");
        $old_date = date("Y-m-d H:i:s", 0);
        // リーディング結果の確認と挿入すべきレコードの構築
        // uid, sid, starttime, endtime, readtime, valid
        $r_records = array();
        for ( $r = 0 ; $r < count($stat['sids']) ; $r++ ) {
            // 下準備
            $data = array();
            $res = $stat['r_res'][$r];
            // レコードデータの構築
            $data['rid'] = $rid;
            $data['sid'] = $stat['sids'][$r];
            if ( ! empty($res['start']) ) {
                $data['starttime'] = $res['start'];
            } else {
                $data['starttime'] = $old_date;
            }
            if ( ! empty($res['end']) ) {
                $data['endtime'] = $res['end'];
            } else {
                $data['endtime'] = $old_date;
            }
            $d1 = strtotime($data['starttime']);
            $d2 = strtotime($data['endtime']);
            $tdiff = $d2 - $d1;
            $data['readtime'] = $tdiff;
            $data['valid'] = $res['valid'];
            // レコードにデータを追加
            $r_records[] = $data;
        }
        // 解答結果の確認と挿入すべきレコードの構築
        // uid, qid, correct, answer, iscorrect,
        // starttime, endtime, thinktime, valid
        $a_records = array();
        for ( $r = 0 ; $r < count($stat['sids']) ; $r++ ) {
            for ( $a = 0 ; $a < count($stat['qids'][$r]) ; $a++ ) {
                // 下準備
                $data = array();
                $sid = $stat['sids'][$r];
                $res = $stat['a_res'][$r][$a];
                // レコードデータの構築
                $data['rid'] = $rid;
                $data['qid'] = $stat['qids'][$r][$a];
                $data['correct'] = $res['correct'];
                $data['answer'] = $res['answer'];
                $data['iscorrect'] =
                    ( $data['correct'] == $data['answer'] ? 1 : 0 );
                if ( ! empty($res['start']) ) {
                    $data['starttime'] = $res['start'];
                } else {
                    $data['starttime'] = $old_date;
                }
                if ( ! empty($res['end']) ) {
                    $data['endtime'] = $res['end'];
                } else {
                    $data['endtime'] = $old_date;
                }
                $d1 = strtotime($data['starttime']);
                $d2 = strtotime($data['endtime']);
                $tdiff = $d2 - $d1;
                if($data['starttime'] == $old_date || $data['endtime'] == $old_date){
                    $tdiff = 36000;
                }
                $data['thinktime'] =$tdiff;
                $data['valid'] = $res['valid'];
                // レコードにデータを追加
                $a_records[] = $data;
            }
        }
        $rsets = $this->getTableLocator()->get('rsets');
        $r_ent = $rsets->get($rid);
        $r_ent->valid = 1;
        $r_ent->endtime = $e_time;
        $rresults = $this->getTableLocator()->get('rresults');
        $rentities = $rresults->newEntities($r_records);
        $aresults = $this->getTableLocator()->get('aresults');
        $aentities = $aresults->newEntities($a_records);
        // トランザクションで登録する
        $rresults->getConnection()->transactional(            
            function () use ($rsets, $r_ent,
            $rresults, $rentities, $aresults, $aentities) {
                if ( ! $rsets->save($r_ent) ) {
                    return false;
                }        
                foreach ($rentities as $entity) {
                    if ( ! $rresults->save($entity, ['atomic' => false]) ) {
                        return false;
                    }
                }
                foreach ($aentities as $entity) {
                    if ( ! $aresults->save($entity, ['atomic' => false]) ) {
                        return false;
                    }
                }
                return true;
            });
        // セッションを消す
        $session->delete("Erams.Test");
    }
}
?>
