<?php
// 問題編集ページ
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\ORM\Table;

class QuestionController extends AppAdminController {

    private $flag;
    
    private $eset;
    private $section;
    private $question;
    private $choices;
    private $question_pair;
    private $action;
    private $action_opt;
    
    /*
     * 初期化メソッド
     * このクラスを読む際には Question の親データまで判っている
     * eid, sid, qid までは定義された状態で読み込まれる
     */
    public function initialize() {
        // 親クラスをロードする
        parent::initialize();
        // ページのタイトルセット
        $this->Erams->setTitle('問題編集ページ');
        // 必要なデータを確保
        if (
            ! ($this->eset = $this->EramsData->getEsetByForm('/Eset')) ||
            ! ($this->section = $this->EramsData->
            getSectionByForm($this->eset->id, "/Eset?eid=".$this->eset->id)) ||
            ! ($this->question = $this->EramsData->
            getQuestionByForm($this->section->id, "/Section?sid=".$this->section->id)) ) {            
            return;
        }
        // 問題に含まれる選択肢の確保
        $this->choices = 
            $this->EramsDB->get("choices", "subseq", [ 'qid' => $this->question->id ] );
        // 前と次の問題を確保
        $this->question_pair = array(-1, -1);
        $questions = $this->EramsDB->get("questions", "subseq",
        [ 'sid' => $this->section->id ]);
        foreach ( $questions as $q ) {
            if ( $q->subseq == $this->question->subseq - 1 ) {
                $this->question_pair[0] = ($q->id);
            }
            if ( $q->subseq == $this->question->subseq + 1 ) {
                $this->question_pair[1] = ($q->id);
            }
        }        
        // 戻るページを設定
        $this->Erams->setBackPage('/Section',
        [ 'eid' => $this->eset->id, 'sid' => $this->section->id ]);
        // View に表示するデータをセットする
        $this->set('Erams.Eset', $this->eset);
        $this->set('Erams.Section', $this->section);
        $this->set('Erams.Question', $this->question);
        $this->set('Erams.Choices', $this->choices);
        $this->set('Erams.QuestionPair', $this->question_pair);
        // セッションを生成しておく
        $session = $this->request->getSession();
        if ( empty($session->read("Erams.QuestionKey")) ) {
            $session->write("Erams.QuestionKey", md5(rand(100000, 999999)));
        }
        $this->set('Erams.QuestionKey', $session->read('Erams.QuestionKey'));
    }

    public function index() {
        // セッションを確保
        $session = $this->request->getSession();
        // ログイン名を取得
        $tname = $session->read('Erams.uname');
        // ログ出力
        $this->log( $tname.' Question - index  eid: '.$this->eset->id.' mode: '.$this->eset->mode.
        ' sid: '.$this->section->id.' qid: '.$this->question->id, 'info');
    }

    // 問題と選択肢の保存
    public function save() {
        // セッションを確保
        $session = $this->request->getSession();
        // ログイン名を取得
        $tname = $session->read('Erams.uname');
        // ログ出力
        $this->log( $tname.' Question - save  eid: '.$this->eset->id.' mode: '.$this->eset->mode.
        ' sid: '.$this->section->id.' qid: '.$this->question->id, 'info');
        
        // 編集モードか確認する
        if ( $this->eset->mode == 1 ) {
            $this->Flash->set("テスト提供中には問題に対する操作はできません。");
            $this->redirect('/Question/index?eid='.$this->eset->id.
            '&sid='.$this->section->id.'&qid='.$this->question->id);
            return;
        }
        // 問題文をpostから収集
        $posted_text = $this->request->getData('text');
        // $text だけは少し編集しておく
        $posted_text = preg_replace("/\n/", "<br>", $posted_text);
        $posted_text = preg_replace('/"/', "&#034;", $posted_text);
        $posted_text = preg_replace("/'/", "&#039;", $posted_text);
        $posted_text = preg_replace("/\r/", "", $posted_text);
        // 問題の選択肢をpostから収集
        $posted_choices =  $this->request->getData('choices');
        // 正解番号をpostから収集
        $posted_answer =  $this->request->getData('answer');        
        // 問題テーブルを取得してエンティテーを取る
        $questions = $this->getTableLocator()->get('questions');
        $q_ent = $questions->get($this->question->id);
        // 問題文をentiry にセットする
        $q_ent->text = $posted_text;
        // 選択肢テーブルを取得してエンティテーを取る
        $choices = $this->getTableLocator()->get('choices');
        $c_ents = array();
        foreach ( $this->choices as $choice ) {
            $c_ents[] = $choices->get($choice->id);
        }

        
        //問題の選択肢が追加されずに保存ボタンを押された時のエラー処理(2020/12/9追加)
        if( empty($posted_choices) || empty($this->choices->toArray()) ){
                $this->Flash->set("問題の選択肢が存在しません");
                $this->redirect('/Question/index?eid='.$this->eset->id.
                                '&sid='.$this->section->id.'&qid='.$this->question->id);
        }
        
        // データベース内の選択肢数とポストされてきた選択肢数を確認(2020/12/9 ifの条件を追加と変更)
        //if ( count($posted_choices) != $this->choices->count() ) { 
        if( !empty($posted_choices) && !empty($this->choices->toArray()) ){
            if ( count($posted_choices) != count($this->choices->toArray()) ) {
                $this->Flash->set("保存に際して予期せぬエラーが発生しました。");
                $this->redirect('/Question/index?eid='.$this->eset->id.
                                '&sid='.$this->section->id.'&qid='.$this->question->id);
            }
        }
        
        
        // 選択肢をentitiesにセットする
        $count = 0;
        foreach ( $c_ents as $ent ) {
            $ent->subseq = $count + 1;
            $ent->text = $posted_choices[$count];
            if ( $count + 1 == $posted_answer ) {
                $ent->correct = 1;
            } else {
                $ent->correct = 0;
            }
            $count++;
        }
        // 問題と選択肢をトランザクションで保存
        $ret = $choices->getConnection()->transactional(
            function () use ($questions, $choices, $q_ent, $c_ents) {
                if ( ! $questions->save($q_ent, [ 'atmic' => false]) ) {
                    return false;
                }
                foreach ( $c_ents as $ent ) {
                    if ( ! $choices->save($ent, [ 'atmic' => false]) ) {
                        return false;
                    }
                }
                return true;
            });
        // 判断するよ
        //( 2020/12/9 if文の条件を変更 $rset -> $rset && !empty($posted_choices) )
        if( $ret && !empty($posted_choices) ) {
            $this->Flash->set("問題を上書き保存しました（".
            date("Y/m/d H:i:s")."）: 選択肢数 ".count($posted_choices));
        } else {
            $this->Flash->set("問題の保存に失敗しました。");
        }
        $this->redirect('/Question/index?eid='.$this->eset->id.
        '&sid='.$this->section->id.'&qid='.$this->question->id);
    }

    // 選択肢の新規追加
    public function createChoice() {
        // セッションを確保
        $session = $this->request->getSession();
        // ログイン名を取得
        $tname = $session->read('Erams.uname');
        // ログ出力
        $this->log( $tname.' Question - createChoice  eid: '.$this->eset->id.' mode: '.$this->eset->mode.
        ' sid: '.$this->section->id.' qid: '.$this->question->id, 'info');
        
        // 編集モードか確認する
        if ( $this->eset->mode == 1 ) {
            $this->Flash->set("テスト提供中には問題に対する操作はできません。");
            $this->redirect('/Question/index?eid='.$this->eset->id.
            '&sid='.$this->section->id.'&qid='.$this->question->id);
            return;
        }
        // 選択肢テーブルを取得
        $choices = $this->getTableLocator()->get('choices');
        // Entity を作成する
        $entity = $choices->newEntity();
        $entity->qid = $this->question->id;
        $entity->subseq = count($this->choices) + 1;
        $entity->text = "選択肢（".date("Y/m/d H:i:s")." 作成）";
        $entity->correct = 0;
        // Entity を保存する
        if ( $choices->save($entity) ) {
            $this->Flash->set("".$entity->subseq." 番目の選択肢を新規作成しました。");
            $this->redirect('/Question/index?eid='.$this->eset->id.
            '&sid='.$this->section->id.'&qid='.$this->question->id);   
        } else {
            $this->Flash->set("選択肢の新規作成に失敗しました。");
        }
    }

    // 選択肢の削除
    public function deleteChoice() {
        // セッションを確保
        $session = $this->request->getSession();
        // ログイン名を取得
        $tname = $session->read('Erams.uname');
        
        // 編集モードか確認する
        if ( $this->eset->mode == 1 ) {
            // ログ出力
            $this->log( $tname.' Question - deleteChoice  eid: '.$this->eset->id.' mode: '.$this->eset->mode.
            ' sid: '.$this->section->id.' qid: '.$this->question->id, 'info');
            
            $this->Flash->set("テスト提供中には問題に対する操作はできません。");
            $this->redirect('/Question/index?eid='.$this->eset->id.
            '&sid='.$this->section->id.'&qid='.$this->question->id);
            return;
        }
        // 埋め込みキーを確認
        $key = $this->request->getQuery('key');
        if ( $key != $this->request->getSession()->read("Erams.QuestionKey") ) {
            // ログ出力
            $this->log( $tname.' Question - deleteChoice  eid: '.$this->eset->id.' mode: '.$this->eset->mode.
            ' sid: '.$this->section->id.' qid: '.$this->question->id, 'info');
            $this->Flash->set("不正なアクセスです。");
            $this->redirect('/Question/index?eid='.$this->eset->id.
            '&sid='.$this->section->id.'&qid='.$this->question->id);
            return;
        }        
        // 選択肢idをQueryデータから確保
        $cid = $this->request->getQuery('cid');

        // ログ出力
        $this->log( $tname.' Question - deleteChoice  eid: '.$this->eset->id.' mode: '.$this->eset->mode.
        ' sid: '.$this->section->id.' qid: '.$this->question->id.' cid: '.$cid, 'info');
        
        // 問題に含まれる選択肢 id を取得
        $cids = array();
        foreach ( $this->choices as $choice ) {
            $cids[] = $choice->id;
        }
        $choices = $this->getTableLocator()->get('choices');
        // 削除と入れ替え操作
        $ret = $choices->getConnection()->transactional(
            function () use ($choices, $cid, $cids) {
                $count = 0;
                foreach ( $cids as $c ) {
                    $row = $choices->get($c);
                    if ( $row->id == $cid ) {
                        if ( ! $choices->delete($row, [ 'atmic' => false ] ) ) {
                            return false;
                        }
                    } else {
                        $row->subseq = ++$count;
                        if ( ! $choices->save($row, [ 'atmic' => false ] ) ) {
                            return false;
                        }
                    }
                }
                return true;
            });
        // 判断するよ
        if ( $ret ) {
            $this->Flash->set("指定された選択肢".$cid."を削除しました。");
        } else {
            $this->Flash->set("指定された選択肢".$cid."の削除に失敗しました。");
        }
        $this->redirect('/Question/index?eid='.$this->eset->id.
        '&sid='.$this->section->id.'&qid='.$this->question->id);
    }

    // 問題の順番を入れ替える（上移動）
    public function moveUp() {
        // セッションを確保
        $session = $this->request->getSession();
        // ログイン名を取得
        $tname = $session->read('Erams.uname');
        
        // 編集モードか確認する
        if ( $this->eset->mode == 1 ) {
            // ログ出力
            $this->log( $tname.' Question - moveUp  eid: '.$this->eset->id.' mode: '.$this->eset->mode.
            ' sid: '.$this->section->id.' qid: '.$this->question->id, 'info');

            $this->Flash->set("テスト提供中には問題に対する操作はできません。");
            $this->redirect('/Question/index?eid='.$this->eset->id.
            '&sid='.$this->section->id.'&qid='.$this->question->id);
            return;
        }
        // 埋め込みキーを確認
        $key = $this->request->getQuery('key');
        if ( $key != $this->request->getSession()->read("Erams.QuestionKey") ) {
            // ログ出力
            $this->log( $tname.' Question - moveUp  eid: '.$this->eset->id.' mode: '.$this->eset->mode.
            ' sid: '.$this->section->id.' qid: '.$this->question->id, 'info');
            
            $this->Flash->set("不正なアクセスです。");
            $this->redirect('/Question/index?eid='.$this->eset->id.
            '&sid='.$this->section->id.'&qid='.$this->question->id);
            return;
        }        
        // 選択肢idをQueryデータから確保
        $cid = $this->request->getQuery('cid');

        // ログ出力
        $this->log( $tname.' Question - moveUp  eid: '.$this->eset->id.' mode: '.$this->eset->mode.
        ' sid: '.$this->section->id.' qid: '.$this->question->id.' cid: '.$cid, 'info');
        
        // eid, sid を用意
        $eid = $this->eset->id;
        $sid = $this->section->id;
        // 対象の問題のsubseq
        $src_cid = $cid;
        $src_subseq = $this->choices->first()->subseq;
        // 問題に含まれる選択肢を取得
        $choice_array = array();
        foreach ( $this->choices as $choice ) {
            $choice_array[] = $choice;
        }
        // subseq を入れ替える
        for ( $c = 0 ; $c < count($choice_array) ; $c++ ) {
            if ( $choice_array[$c]->id == $cid ) {
                if ( $c == 0 ) {
                    $dest_subseq = -1;
                    $dest_cid = -1;
                } else {
                    $dest_subseq = $choice_array[$c-1]->subseq;
                    $dest_cid = $choice_array[$c-1]->id;
                }
            }
        }
        // 一番上の選択肢を上にに操作したとき
        if ( $dest_cid == -1 ) {
            $this->redirect('/Question/index?eid='.$this->eset->id.
            '&sid='.$this->section->id.'&qid='.$this->question->id);
            return;
        }
        // 選択肢テーブルを確保
        $choices = $this->getTableLocator()->get('choices');
        $entities = array();
        $entities[0] = $choices->get($src_cid);
        $entities[1] = $choices->get($dest_cid);
        // トランザクション処理で登録を行う
        $ret = $choices->getConnection()->transactional(
            function () use ($choices, $entities) {
                $tmp = $entities[0]->subseq;
                $entities[0]->subseq = $entities[1]->subseq;
                $entities[1]->subseq = $tmp;
                foreach ($entities as $entity) {
                    if ( ! $choices->save($entity, ['atomic' => false]) ) {
                        return false;
                    }
                }
                return true;
            });
        // 結果を判断して表示を変える
        if ( ! $ret ) {
            $this->Flash->set("問題順序の入れ替えに失敗しました。");
        }
        $this->redirect('/Question/index?eid='.$this->eset->id.
        '&sid='.$this->section->id.'&qid='.$this->question->id);
    }

    // 問題の順番を入れ替える（下移動）
    public function moveDown() {
        // セッションを確保
        $session = $this->request->getSession();
        // ログイン名を取得
        $tname = $session->read('Erams.uname');
        
        // 編集モードか確認する
        if ( $this->eset->mode == 1 ) {
            // ログ出力
            $this->log( $tname.' Question - moveDown  eid: '.$this->eset->id.' mode: '.$this->eset->mode.
            ' sid: '.$this->section->id.' qid: '.$this->question->id, 'info');
            
            $this->Flash->set("テスト提供中には問題に対する操作はできません。");
            $this->redirect('/Question/index?eid='.$this->eset->id.
            '&sid='.$this->section->id.'&qid='.$this->question->id);
            return;
        }
        // 埋め込みキーを確認
        $key = $this->request->getQuery('key');
        if ( $key != $this->request->getSession()->read("Erams.QuestionKey") ) {
            // ログ出力
            $this->log( $tname.' Question - moveDown  eid: '.$this->eset->id.' mode: '.$this->eset->mode.
            ' sid: '.$this->section->id.' qid: '.$this->question->id, 'info');
            
            $this->Flash->set("不正なアクセスです。");
            $this->redirect('/Question/index?eid='.$this->eset->id.
            '&sid='.$this->section->id.'&qid='.$this->question->id);
            return;
        }        
        // 選択肢idをQueryデータから確保
        $cid = $this->request->getQuery('cid');
        
        // ログ出力
        $this->log( $tname.' Question - moveDown  eid: '.$this->eset->id.' mode: '.$this->eset->mode.
        ' sid: '.$this->section->id.' qid: '.$this->question->id.' cid: '.$cid, 'info');

        // eid, sid を用意
        $eid = $this->eset->id;
        $sid = $this->section->id;
        // 対象の問題のsubseq
        $src_cid = $cid;
        $src_subseq = $this->choices->first()->subseq;
        // 問題に含まれる選択肢を取得
        $choice_array = array();
        foreach ( $this->choices as $choice ) {
            $choice_array[] = $choice;
        }
        // subseq を入れ替える
        for ( $c = 0 ; $c < count($choice_array) ; $c++ ) {
            if ( $choice_array[$c]->id == $cid ) {
                if ( $c == count($choice_array) -1 ) {
                    $dest_subseq = -1;
                    $dest_cid = -1;
                } else {
                    $dest_subseq = $choice_array[$c+1]->subseq;
                    $dest_cid = $choice_array[$c+1]->id;
                }
            }
        } 
        // 一番下の選択肢を下に操作したとき
       if ( $dest_cid == -1 ) {
            $this->redirect('/Question/index?eid='.$this->eset->id.
            '&sid='.$this->section->id.'&qid='.$this->question->id);
            return;
        }
        // 選択肢テーブルを確保
        $choices = $this->getTableLocator()->get('choices');
        $entities = array();
        $entities[0] = $choices->get($src_cid);
        $entities[1] = $choices->get($dest_cid);
        // トランザクション処理で登録を行う
        $ret = $choices->getConnection()->transactional(
            function () use ($choices, $entities) {
                $tmp = $entities[0]->subseq;
                $entities[0]->subseq = $entities[1]->subseq;
                $entities[1]->subseq = $tmp;
                foreach ($entities as $entity) {
                    if ( ! $choices->save($entity, ['atomic' => false]) ) {
                        return false;
                    }
                }
                return true;
            });
        // 結果を判断して表示を変える
        if ( ! $ret ) {
            $this->Flash->set("問題順序の入れ替えに失敗しました。");
        }
        $this->redirect('/Question/index?eid='.$this->eset->id.
        '&sid='.$this->section->id.'&qid='.$this->question->id);
    }
    
    // 問題の順番を入れ替える
    public function swap() {
        // セッションを確保
        $session = $this->request->getSession();
        // ログイン名を取得
        $tname = $session->read('Erams.uname');
        
        // ファンクションを決める
        $qid = h(trim($this->request->getData('qid')));
        $moveto = h(trim($this->request->getData('moveto')));

        // ログ出力
        $this->log( $tname.' Question - swap  eid: '.$this->eset->id.' mode: '.$this->eset->mode.
        ' sid: '.$this->section->id.' qid: '.$qid.' moveto: '.$moveto, 'info');
        
        // moveto が u, d でなければ上の階層にリダイレクト
        if( $moveto != "u" && $moveto != 'd' ) {
            $this->Flash->set("予期せぬエラーが発生しました。");
            $this->redirect('/Section?eid='.$this->eset->id.'&sid='.$this->section->id);
            return;
        }
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
