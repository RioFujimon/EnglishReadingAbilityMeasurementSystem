<?php
// セクション編集ページ
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\ORM\Table;

class SectionController extends AppAdminController {

    private $eset;
    private $section;
    private $questions;
    private $choices;
    private $section_pair;

    /*
     * 初期化メソッド
     * このクラスを読む際には Section の親データまで判っている
     * eid, sid までは定義された状態で読み込まれる
     */
    public function initialize() {
        // 親クラスをロードする
        parent::initialize();
        // ページのタイトルセット
        $this->Erams->setTitle('セクション編集ページ');
        // 下部ページで使うセッションを消去
        $session = $this->request->getSession();
        $session->delete('Erams.QuestionKey');
        // 必要なデータを確保
        if (
            ! ($this->eset = $this->EramsData->getEsetByForm('/EsetTop')) ||
            ! ($this->section = $this->EramsData->
            getSectionByForm($this->eset->id, "/Eset?eid=".$this->eset->id)) ) {
            return;
        }
        // 問題の確保
        $this->questions =
            $this->EramsDB->get("questions", "subseq", [ 'sid' => $this->section->id ] );
        // 問題に含まれる選択肢の確保
        $this->choices = array();
        foreach ( $this->questions as $q ) {
            $this->choices[] =
                $this->EramsDB->get("choices", "subseq", [ 'qid' => $q->id ]);
        }
        // 前と次のセクションを確保
        $this->section_pair = array(-1, -1);
        $sects = $this->EramsDB->get("sections", "subseq",
        [ 'eid' => $this->eset->id ]);
        foreach ( $sects as $s ) {
            if ( $s->subseq == $this->section->subseq - 1 ) {
                $this->section_pair[0] = $s->id;
            }
            if ( $s->subseq == $this->section->subseq + 1 ) {
                $this->section_pair[1] = $s->id;
            }
        }
        // 戻るページを設定
        $this->Erams->setBackPage('/Eset', [ 'eid' => $this->eset->id ]);
        // View に表示するデータをセットする
        $this->set('Erams.Eset', $this->eset);
        $this->set('Erams.Section', $this->section);
        $this->set('Erams.Questions', $this->questions);
        $this->set('Erams.Choices', $this->choices);
        $this->set('Erams.SectionPair', $this->section_pair);
    }
    
    public function index() {
        // セッションを確保
        $session = $this->request->getSession();
        // ログイン名を取得
        $tname = $session->read('Erams.uname');
        // ログ出力
        $this->log( $tname.' Section - index  eid: '.$this->eset->id.' mode: '.$this->eset->mode.' sid: '.$this->section->id, 'info');
    }

    // セクションを保存する
    public function save() {
        // セッションを確保
        $session = $this->request->getSession();
        // ログイン名を取得
        $tname = $session->read('Erams.uname');
        // ログ出力
        $this->log( $tname.' Section - save  eid: '.$this->eset->id.' mode: '.$this->eset->mode.' sid: '.$this->section->id, 'info');
        
        if ( $this->eset->mode == 1 ) {
            $this->Flash->set("テスト提供中にセクションに対する操作はできません。");
            $this->redirect('/Section?eid='.$this->eset->id.'&sid='.$this->section->id);
            return;
        }
        // post 変数を取得
        $title = h(trim($this->request->getData('title')));
        $property = h(trim($this->request->getData('property')));
        $text = $this->request->getData('text');
        $tlimit = h(trim($this->request->getData('tlimit')));
        // $text だけは少し編集しておく
        $text = preg_replace("/\r/", "", $text);
        $text = preg_replace("/\n/", "<br>", $text);
        $text = preg_replace('/"/', "&#034;", $text);
        $text = preg_replace("/'/", "&#039;", $text);
        // セットテーブルを確保
        $sections = $this->getTableLocator()->get('sections');
        $entity = $sections->get($this->section->id);
        // title がなければ
        if( empty($title) ) {
            $title = "タイトル未設定";
        }
        // tlimit がなければ60を代入
        if( empty($tlimit) || $tlimit < 10 ) {
            $tlimit = 60;
        }
        // entity の内容変更
        $entity->title = $title;
        $entity->property = $property;
        $entity->text = $text;        
        $entity->tlimit = $tlimit;
        $entity->text = $text;
        // entity の保存が必要か？
        if( $sections->save($entity) ) {
            $this->Flash->set("セクションを上書き保存しました（".
            date("Y/m/d H:i:s")."）");
        } else {
            // 保存できなかった場合
            $this->Flash->set("セクションの保存に失敗しました。");
        }
        // ページを表示
        $this->redirect('/Section?eid='.$this->eset->id.'&sid='.$this->section->id);
    }

    // 問題の新規作成
    public function createQuestion() {
        // セッションを確保
        $session = $this->request->getSession();
        // ログイン名を取得
        $tname = $session->read('Erams.uname');
        // ログ出力
        $this->log( $tname.' Section - createQuestion  eid: '.$this->eset->id.' mode: '.$this->eset->mode.' sid: '.$this->section->id, 'info');
        
        // 編集モードか確認する
        if ( $this->eset->mode == 1 ) {
            $this->Flash->set("テスト提供中にセクションに対する操作はできません。");
            $this->redirect('/Section?eid='.$this->eset->id.'&sid='.$this->section->id);
            return;
        }
        // section に含まれている問題の数を確認
        $count = count($this->questions);
        $count = count($this->questions);
        // 問題テーブルを確保
        $questions = $this->getTableLocator()->get('questions');
        // entity を取得
        $entity = $questions->newEntity();
        $entity->sid = $this->section->id;
        $entity->subseq = $count + 1;
        $entity->text = date("Y/m/d H:i:s")." 作成";
        // entity の保存
        if ( $questions->save($entity) ) {
            $this->Flash->set('新たな問題を新規追加しました。');
        } else {
            $this->Flash->set('問題の新規追加に失敗しました。');
        }
        // ページを表示
        $this->redirect('/Section?eid='.$this->eset->id.'&sid='.$this->section->id);
    }

    // 問題を削除する
    public function delQuestion() {
        // セッションを確保
        $session = $this->request->getSession();
        // ログイン名を取得
        $tname = $session->read('Erams.uname');
        
        // 編集モードか確認する
        if ( $this->eset->mode == 1 ) {
            // ログ出力
            $this->log( $tname.' Section - delQuestion  eid: '.$this->eset->id.
            ' mode: '.$this->eset->mode.' sid: '.$this->section->id, 'info');

            $this->Flash->set("テスト提供中にセクションに対する操作はできません。");
            $this->redirect('/Section?eid='.$this->eset->id.'&sid='.$this->section->id);
            return;
        }
        // post 変数を取得
        $qid = h(trim($this->request->getData('qid')));

        // ログ出力
        $this->log( $tname.' Section - delQuestion  eid: '.$this->eset->id.
        ' mode: '.$this->eset->mode.' sid: '.$this->section->id.' qid: '.$qid, 'info');
        
        // セクションに含まれる問題を取得
        $questions = $this->EramsDB->get('questions', null, [ 'sid' => $this->section->id ]);
        $qids = array();
        foreach ( $questions as $question ) {
            $qids[] = $question->id;
        }
        $questions = $this->getTableLocator()->get('questions');
        // 削除と入れ替え操作
        $ret = $questions->getConnection()->transactional(
            function () use ($questions, $qid, $qids) {
                $count = 0;
                foreach ( $qids as $i ) {
                    $row = $questions->get($i);
                    if ( $row->id == $qid ) {
                        if ( ! $questions->delete($row, [ 'atmic' => false ] ) ) {
                            return false;
                        }
                    } else {
                        $row->subseq = ++$count;
                        if ( ! $questions->save($row, [ 'atmic' => false ] ) ) {
                            return false;
                        }
                    }
                }
                return true;
            });
        // 判断するよ
        if( $ret ) {
            $this->Flash->set('指定された問題を削除しました。');
        } else {
            $this->Flash->set("指定された問題の削除に失敗しました。");
        }
        // ページを表示
        $this->redirect('/Section?eid='.$this->eset->id.'&sid='.$this->section->id);
    }

    // 問題の順番を入れ替える
    public function swap() {
        // セッションを確保
        $session = $this->request->getSession();
        // ログイン名を取得
        $tname = $session->read('Erams.uname');
        
        // post 変数を取得
        $qid = h(trim($this->request->getData('qid')));
        $moveto = h(trim($this->request->getData('moveto')));
        // moveto が u, d でなければ上の階層にリダイレクト
        if( $moveto != "u" && $moveto != 'd' ) {
            // ログ出力
            $this->log( $tname.' Section - swap  eid: '.$this->eset->id.
            ' mode: '.$this->eset->mode.' sid: '.$this->section->id.' qid: '.$qid, 'info');
            
            $this->Flash->set("予期せぬエラーが発生しました。");
            $this->redirect('/Section?eid='.$this->eset->id.'&sid='.$this->section->id);
            return;
        }
        // ログ出力
        $this->log( $tname.' Section - swap  eid: '.$this->eset->id.
        ' mode: '.$this->eset->mode.' sid: '.$this->section->id.' qid: '.$qid.' moveto: '.$moveto, 'info');
        
        // eid, sid を用意
        $eid = $this->eset->id;
        $sid = $this->section->id;        
        // 問題を取得
        $question = $this->EramsDB->get('questions', null, [ 'id' => $qid, 'sid' => $sid ]);
        // セクションがないならば
        if ( $question->count() != 1 ) {
            $this->Flash->set("操作対象の問題が見つかりません。");
            $this->redirect('/Section?eid='.$this->eset->id.'&sid='.$this->section->id);
            return;
        }
        // 対象の問題のsubseq
        $src_qid = $qid;
        $src_subseq = $question->first()->subseq;
        // セクションに含まれる問題を取得
        $questions = $this->EramsDB->get('questions', 'subseq', [ 'sid' => $this->section->id ]);
        $question_array = array();
        foreach ( $questions as $questions ) {
            $question_array[] = $questions;
        }
        // subseq を入れ替える
        for ( $c = 0 ; $c < count($question_array) ; $c++ ) {
            if ( $question_array[$c]->id == $qid ) {
                // UP ボタンを押したとき
                if ( strcmp($moveto, 'u') == 0 ) {
                    if ( $c == 0 ) {
                        $dest_subseq = -1;
                        $dest_qid = -1;
                    } else {
                        $dest_subseq = $question_array[$c-1]->subseq;
                        $dest_qid = $question_array[$c-1]->id;
                    }
                }
                // DOWN ボタンを押したとき
                else {
                    if ( $c == count($question_array) - 1 ) {
                        $dest_subseq = -1;
                        $dest_qid = -1;
                    } else {
                        $dest_subseq = $question_array[$c+1]->subseq;
                        $dest_qid = $question_array[$c+1]->id;
                    }
                }
            }
        }
        // 一番上でUPまたは一番下でDOWNを押したとき
        if ( $dest_qid == -1 ) {
            $this->redirect('/Section?eid='.$this->eset->id.'&sid='.$this->section->id);
            return;
        }
        // 問題テーブルを確保
        $questions = $this->getTableLocator()->get('questions');
        $entities = array();
        $entities[0] = $questions->get($src_qid);
        $entities[1] = $questions->get($dest_qid);
        // トランザクション処理で登録を行う
        $ret = $questions->getConnection()->transactional(
            function () use ($questions, $entities) {
                $tmp = $entities[0]->subseq;
                $entities[0]->subseq = $entities[1]->subseq;
                $entities[1]->subseq = $tmp;
                foreach ($entities as $entity) {
                    if ( ! $questions->save($entity, ['atomic' => false]) ) {
                        return false;
                    }
                }
                return true;
            });
        // 結果を判断して表示を変える
        if ( ! $ret ) {
            $this->Flash->set("問題順序の入れ替えに失敗しました。");
        }
        $this->redirect('/Section?eid='.$this->eset->id.'&sid='.$this->section->id);
    }

    public function view() {
    }
}
