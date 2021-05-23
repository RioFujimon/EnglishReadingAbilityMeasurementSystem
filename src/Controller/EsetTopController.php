<?php
// テストセット管理ページ
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\ORM\Table;

class EsetTopController extends AppAdminController {

    private $esets;
    
    public function initialize() {
        // 親クラスをロードする
        parent::initialize();
        // ページのタイトルセット
        $this->Erams->setTitle('テストセット管理ページ');
        // 下部ページで使うセッションを消去
        $session = $this->request->getSession();
        $session->delete('Erams.QuestionKey');
        // 必要なデータを確保
        $this->esets = $this->EramsDB->get('esets', 'id');
        // view に表示するデータをセットする
        $this->set('Erams.Esets', $this->esets);
    }

    public function index() {
        // セッションを確保
        $session = $this->request->getSession();
        // ログイン名を取得
        $tname = $session->read('Erams.uname');
        // ログ出力
        $this->log( $tname.' EsetTop - index', 'info');
    }

    // テストセットの新規作成
    public function createEset() {
        // セッションを確保
        $session = $this->request->getSession();
        // ログイン名を取得
        $tname = $session->read('Erams.uname');
        // ログ出力
        $this->log( $tname.' EsetTop - createEset', 'info');
        
        // post 変数を取得
        $eid = h(trim($this->request->getData('eid')));
        // 簡易チェック
        if ( $eid != -1 ) {
            $this->Flash->set('テストセットの新規作成に失敗しました。');
            $this->render('index');
            return;
        }
        // テストセットテーブルを確保
        $esets = $this->getTableLocator()->get('esets');
        // entity を取得
        $entity = $esets->newEntity();
        $entity->title = "新規テストセット";
        $entity->property = "作成日時".date("Y/m/d H:i:s");
        $entity->version = 1;
        // 
        if ( $esets->save($entity) ) {
            $this->Flash->set('テストセットを新規作成しました。');
        } else {
            $this->Flash->set('テストセットを新規作成に失敗しました。');
        }
        // ページを表示
        $this->redirect('/EsetTop');
    }

    // テストセットを削除する
    public function deleteEset() {
        // セッションを確保
        $session = $this->request->getSession();
        // ログイン名を取得
        $tname = $session->read('Erams.uname');
        
        // post 変数を取得
        $eid = h(trim($this->request->getData('eid')));
        // post 変数のチェック
        if( empty($eid) ) {
            // ログ出力
            $this->log( $tname.' EsetTop - deleteEset  eid : empty', 'info');
            
            $this->Flash->set('削除するテストセットが指定されていません。');
            $this->render('index');
            return;
        }

        // ログ出力
        $this->log( $tname.' EsetTop - deleteEset  eid : '.$eid, 'info');
        
        // テストセットテーブルを確保
        $esets = $this->getTableLocator()->get('esets');
        // 指定された id のレコードを取得
        $row = $esets->find()->where([ 'id' => $eid ]);
        // レコードがあれば削除
        if( $row->count() == 1 ) {
            $eset_title = $row->first()->title;
            $entity = $esets->get($eid);
            if ( $esets->delete($entity) ) {
                $this->Flash->set('指定されたテストセット「'.
                $eset_title.'」を削除しました。');
            } else {
                $this->Flash->set('指定されたテストセット「'.
                $eset_title.'」の削除に失敗しました。');
            }
        }
        // レコードがなければメッセージを指定して表示
        else {
            $this->Flash->set('削除するテストセットが見つかりません。');
        }
        // ページを表示
        $this->redirect('/EsetTop');
    }

    // テストセットのモードを変更する
    public function changeMode() {
        // セッションを確保
        $session = $this->request->getSession();
        // ログイン名を取得
        $tname = $session->read('Erams.uname');
        
        // post 変数を取得
        $eid = h(trim($this->request->getData('eid')));
        // post 変数のチェック
        if( empty($eid) ) {
            // ログ出力
            $this->log( $tname.' EsetTop - changeMode  eid : empty', 'info');
            
            $this->Flash->set("操作するテストセットが指定されていません");
            $this->render('index');
            return;
        }
        
        // テストセットテーブルを確保
        $esets = $this->EramsDB->get('esets', null, [ 'id' => $eid ]);
        // レコードが無ければ終わり
        if( $esets->count() != 1 ) {
            // ログ出力
            $this->log( $tname.' EsetTop - changeMode  eid : '.$eid, 'info');
            
            $this->Flash->set('指定されたテストセットが見つかりません。');
            $this->render('index');
            return;
        }
        // Eset のモードを確認
        $eset = $esets->first();
        $mess = "";

        // ログ出力
        $this->log( $tname.' EsetTop - changeMode  eid: '.$eid.' mode: '.$eset->mode, 'info');
        
        // 編集中の場合の確認
        if ( $eset->mode != 1 ) {
            // テストセットに含まれるセクションの取得
            $sections = $this->EramsDB->get('sections', 'subseq',
            [ 'eid' => $eset->id ]);
            if ( $sections->count() == 0 ) {
                $mess .= "テストセットにセクションが含まれていません。\n";
            }
            // セクションに含まれる問題の取得
            foreach ( $sections as $s ) {
                $questions = $this->EramsDB->get('questions', 'subseq',
                [ 'sid' => $s->id ]);
                if ( $questions->count() == 0 ) {
                    $mess .= "セクション ".$s->subseq." に問題が含まれていません。\n";
                }
                // 問題に含まれる選択肢と解答のチェック
                foreach ( $questions as $q ) {
                    $flag = false;
                    $choices = $this->EramsDB->get('choices', 'subseq',
                    [ 'qid' => $q->id ]);
                    if ( $choices->count() == 0 ) {
                        $mess .= "セクション ".$s->subseq." の問題 ".$q->subseq." に".
                            "選択肢がありません。\n";
                    }
                    foreach ( $choices as $c ) {
                        if ( $c->correct == 1 ) {
                            $flag = true;
                        }
                    }
                    if ( ! $flag ) {
                        $mess .= "セクション ".$s->subseq." の問題 ".$q->subseq.
                            " には正解が設定されていません。\n";
                    }
                }
                
            }            
        }
        // テスト中の場合の処理
        else {
            // テストセットに含まれる結果セットの取得
            $rids = array();
            $rsets = $this->EramsDB->get('rsets', null, [ 'eid' => $eset->id ]);
            foreach ( $rsets as $r ) {
                $rids[] = $r->id;
            }
            // テスト結果のエンティティーを確保
            $ents = array();
            $rsets = $this->getTableLocator()->get('rsets');
            foreach ( $rids as $id ) {
                $ents[] = $rsets->get($id);
            }
            // テスト結果をトランザクションで消去
            $ret = $rsets->getConnection()->transactional(
                function () use ( $rsets, $ents ) {
                    foreach ($ents as $ent) {
                        if ( ! $rsets->delete($ent, ['atomic' => false]) ) {
                            return false;
                        }
                    }
                    return true;
                });
            // 結果を判断
            if ( ! $ret ) {
                $mess .= "学生のテスト結果の消去で問題が発生しました。\n";
            }
        }
        // 問題がない場合の処理
        if ( empty($mess) ) {
            $esets = $this->getTableLocator()->get('esets');
            $entity = $esets->get($eid);
            if ( $eset->mode == 1 ) {
                $entity->mode = 0;
            } else {
                $entity->mode = 1;
            }
            if ( ! $esets->save($entity ) ) {
                $this->Flash->set("テストセット「".$eset->title."」のモード変更に失敗しました。");
                $this->render("index");
                return;
            } else {
                $this->Flash->set("テストセット「".$eset->title."」のモードを変更しました。");
                $this->redirect("/EsetTop");
                return;
            }
        }
        // 問題があった場合には
        $mess .= "テストセット「".$eset->title."」のモード変更をキャンセルしました。";
        $this->Flash->set($mess);
        $this->render("index");
        return;
    }
    
    // テストセットのモードを変更する（バックアップ）
    public function changeMode_backup() {
        
        // post 変数を取得
        $eid = h(trim($this->request->getData('eid')));
        // post 変数のチェック
        if( empty($eid) ) {
            $this->Flash->set("操作するテストセットが指定されていません");
            $this->render('index');
            return;
        }
        // テストセットテーブルを確保
        $esets = $this->EramsDB->get('esets', null, [ 'id' => $eid ]);
        // レコードが無ければ終わり
        if( $esets->count() != 1 ) {
            $this->Flash->set('指定されたテストセットが見つかりません。');
            $this->render('index');
            return;
        }
        // テストセットの状況確認のための前準備
        $eset = $esets->first();
        $mess = "";
        
        $mess = "";
        // テスト中の場合の確認
        $esets = $this->getTableLocator()->get('esets');
        $entity = $esets->get($eid);
        if ( $eset->mode == 1 ) {
            $entity->mode = 0;
            if ( ! $esets->save($entity ) ) {
                $this->Flash->set( "テストセット「".$eset->title."」のモード変更に失敗しました。");
                $this->render("index");
                return;
            } else {
                $this->Flash->set( "テストセット「".$eset->title."」のモードを変更しました。");
                $this->redirect("/EsetTop");
                return;
            }
        }
        // 編集中の場合処理


        
        // セクションが含まれているかチェック
        $sections = $this->EramsDB->get('sections', null, [ 'eid' => $eid ]);
        // セクションが無ければ
        if( $sections->count() == 0 ) {
            $this->Flash->set("問題にセクションが含まれていません。");
            $this->render("index");
            return;            
        }
        // 問題が含まれているかチェック
        $qids = array();
        foreach( $sections as $section ) {
            $questions = $this->EramsDB->get('questions', null, [ 'sid' => $section->id ]);
            // 問題が無ければ
            if( $questions->count() == 0 ) {
                $this->Flash->set("問題が含まれていないセクションがあります。");
                $this->render("index");
                return;            
            }
            // 有れば qid を保存
            foreach ( $questions as $question ) {
                $qids[] = $question->id;
            }
        }
        // 選択肢と正答が含まれているかチェック
        foreach( $qids as $qid ) {
            $choices = $this->EramsDB->get('choices', null, [ 'qid' => $qid ]);
            // 選択肢がなければ
            if( $choices->count() == 0 ) {
                $this->Flash->set("選択肢が含まれていない問題があります。");
                $this->render("index");
                return;            
            }
            // 正答があるか
            $flag = false;
            foreach ( $choices as $choice ) {
                if ( $choice->correct == 1 ) {
                    $flag = true;
                }
            }
            if ( ! $flag ) {
                $this->Flash->set("正答が含まれていない問題があります。");
                $this->render('index');
                return;
            }
        }
        // 問題が無ければモードを変更
        $entity->mode = 1;
        if ( ! $esets->save($entity ) ) {
            $this->Flash->set( "テストセット「".$eset->title."」のモード変更に失敗しました。");
            $this->render("index");
            return;
        } else {
            $this->Flash->set( "テストセット「".$eset->title."」のモードを変更しました。");
            $this->redirect("/EsetTop");
            return;
        }
    }
}
?>
