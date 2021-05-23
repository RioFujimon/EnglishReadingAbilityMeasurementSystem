<?php
// 問題編集ページ
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\ORM\Table;

class QuestionEditController extends AppAdminController{

    public function initialize(){
        // 親クラスをロードする
        parent::initialize();
        // ページのタイトルセット
        $this->Erams->setTitle('問題編集ページ');
    }

    public function index() {
        // get 変数を取得
        $eid = h(trim($this->request->getQuery('eid')));
        $sid = h(trim($this->request->getQuery('sid')));
        $qid = h(trim($this->request->getQuery('qid')));

        // eidがなければ
        if( empty($eid) ) {
            $this->Erams->error("テストセットのidが指定されていません。");
            $this->redirect('/EsetList');
            return;
        }
        // sidがなければ
        if( empty($sid) ) {
            $this->Erams->error("セクションのidが指定されていません。");
            $this->redirect('/EsetEdit?'.'eid='.$eid);
            return;
        }
        $esets = $this->EramsDB->get( 'esets', null, [ 'id' => $eid ]);
        if ( $esets->count() == 1 ) {
            $eset = $esets->first();
            $mode = $eset->mode;
        }
        
        // 必要なデータをView変数に確保
        $questions = $this->EramsDB->get( 'questions', null, [ 'id' => $qid ] );
        if( $qid != 0 ) {
            // 問題がなければ問題編集ページに戻る
            if ( $questions->count() != 1 ) {
                $this->Erams->error("指定した問題がありません。");
                $this->redirect('/SectionEdit?'.'eid='.$eid.'&sid='.$sid);
                return;
            }
            $result = $questions->first();
            $qtext = preg_replace('/<br>/', '', $result->text);
            $quesInfo = [
                'subseq' => $result->subseq,
                'text' => $qtext ];
//                'text' => html_entity_decode($result->text, ENT_QUOTES) ];
        } else {
            $quesInfo = [ 'subseq' => '', 'text' => '' ];
        }
        
        $choices = $this->EramsDB->get( 'choices', 'subseq', [ 'qid' => $qid ]);
        // 問題情報を集計する
        $choiceInfo = array();
        // 選択肢があれば集計する
        if( count($choices) != 0 ) {
            foreach ( $choices as $choice ) {
                $choiceInfo[$choice->id] = array();
                $choiceInfo[$choice->id]['cid'] = $choice->id;
                $choiceInfo[$choice->id]['text'] =
                html_entity_decode($choice->text, ENT_QUOTES);
                $choiceInfo[$choice->id]['correct'] = $choice->correct;
                $choiceInfo[$choice->id]['subseq'] = $choice->subseq;
                if( $choice->correct == 1 ) {
                    $right = $choice->subseq;
                }
            }
        }
        // correct がなければ
        if ( empty($right) ) {
            $right = 1;
        }
        
        $this->set('EramsQuesInfo', $quesInfo);
        $this->set('EramsChoiceInfo', $choiceInfo);
        $this->set('correct', $right);
        $this->set('EramsEsetMode', $mode);
        $this->set('eid', $eid);
        $this->set('sid', $sid);
        $this->set('qid', $qid);
        $this->Erams->setBackPage('/SectionEdit', [ 'eid' => $eid, 'sid' => $sid ]);
    }

    // 問題を保存する
    public function saveQues() {
        // post 変数を取得
        $qtext = $this->request->getData('qtext');
        $ctext = $this->request->getData('ctext');
        $right = $this->request->getData('correct');
        $eid = h(trim($this->request->getData('eid')));
        $sid = h(trim($this->request->getData('sid')));
        $qid = h(trim($this->request->getData('qid')));

        // post 変数のチェック
        // eid が無ければセット編集ページのトップへリダイレクト
        if( empty($eid) ) {
            $this->Erams->error("テストセットのidが指定されていません。");
            $this->redirect('/EsetList');
            return;
        }
        // sid が無ければ2つ上の階層にリダイレクト
        if( empty($sid) ) {
            $this->Erams->error("セクションのidが指定されていません。。");
            $this->redirect('/EsetEdit?'.'eid='.$eid);
            return;
        }
        // qid が無ければ上の階層にリダイレクト
        if( !isset($qid) ) {
            $this->Erams->error("問題のidが指定されていません。");
            $this->redirect('/SectionEdit?'.'eid='.$eid.'&sid='.$sid);
            return;
        }
        // qtext がなければ
        if( !isset($qtext) || strcmp($qtext, '') == 0 ) {
            $this->Flash->set("問題文が入力されていないため、保存できませんでした。");
            $this->redirect('/QuestionEdit?'.'eid='.$eid.'&sid='.$sid.'&qid='.$qid);
            return;
        }
        // right が無ければ 1 を格納する
        if( empty($right) ) {
            $right = 1;
        }

        // 文字を変換する
        $qtext = preg_replace("/'/", '&#039;',$qtext);
        $qtext = preg_replace("/\"/", '&quot;',$qtext);
        $qtext = preg_replace("/_/", '&#095;',$qtext);
        $qtext= preg_replace("/\//", '&#047;',$qtext);
        $qtext = preg_replace("/\r/", '', $qtext);
        $qtext = preg_replace("/\n/", '<br>',$qtext);
        $qtext= preg_replace("/\\\/", '&#092;',$qtext);
        
        // 問題テーブルと選択肢テーブルを確保
        $ques = $this->EramsDB->get('questions', null, [ 'id' => $qid ]);
        // 保存する
        if( $qid != 0 ) {
            // 問題がなければ
            if( $ques->count() != 1 ) {
                $this->Erams->error("指定した問題がありません。");
                $this->redirect('/SectionEdit?'.'eid='.$eid.'&sid='.$sid);
                return;
            }
            // sid と 取得したセクションの sid が異なるならば
            if ( $sid != $ques->first()->sid ) {
                // error
                $this->Erams->error(
                    "問題が含まれているセクションのidと指定されたセクションのidが不一致です。");
                $this->redirect('/EsetEdit?'.'eid='.$eid);
                return;
            }
            // 問題が存在していたら保存
            $questions = $this->getTableLocator()->get('questions');
            $question = $ques->first();
            $choices = $this->getTableLocator()->get('choices');
            $chos = $this->EramsDB->get('choices', 'subseq', [ 'qid' => $qid ]);
            $choice_array = array();
            
            foreach( $chos as $choice ) {
                $choice_array[] = $choice;
            }
            $ctext_array = array();
            // 空でない選択肢を文字を変換してから格納する
            foreach( $ctext as $text ) {
                if( strcmp($text, '') != 0 ){
                    $text = preg_replace("/'/", '&#039;', $text);
                    $text = preg_replace("/\"/", '&quot;', $text);
                    $text = preg_replace("/_/", '&#095;', $text);
                    $text = preg_replace("/\//", '&#047;', $text);
                    $text = preg_replace("/\r/", '', $text);
                    $text = preg_replace("/\\\/", '&#092;', $text);
                    $ctext_array[] = $text;
//                    echo 'yyyyyyyy'.$text;
                }
            }
            
            // トランザクション処理で登録を行う
            $ret = $questions->getConnection()->transactional(
                function () use ($questions, $question, $qtext,
                $choices, $choice_array, $qid, $ctext_array, $right) {
                    $question->text = $qtext;
                    if ( ! $questions->save($question, ['atomic' => false]) ) {
                        return false;
                    }
                    $choices->getConnection()->transactional(
                        function () use ($choices, $choice_array,
                        $qid, $ctext_array, $right) {
                            for($c = 0; $c < count($ctext_array); $c++){
                                // 上書き
                                if( $c < count($choice_array) ) {
                                    $entity = $choice_array[$c];
                                }
                                // 新規作成
                                else {
                                    $entity = $choices->newEntity();
                                    $entity->qid = $qid;
                                }
                                $entity->text = $ctext_array[$c];
                                $entity->subseq = $c + 1;
                                // 正誤
                                if( $entity->subseq == $right ) {
                                    $entity->correct = 1;
                                }
                                else {
                                    $entity->correct = 0;
                                }
                                // 更新に失敗したら保存しない
                                if ( ! $choices->save($entity, ['atomic' => false]) ) {
                                    return false;
                                }
                            }

                            $cnt = count($ctext_array);
                            echo count($ctext_array).count($choice_array);
                            // 削除されたなら
                            if( $cnt < count($choice_array) ) {
                                $del_seq = array();
                                
                                for( $d = $cnt; $d < count($choice_array); $d++ ) { 
                                    $del_seq[] = $d;
                                }
                                for($e = 0; $e < count($del_seq); $e++ ){
                                    $choices->delete($choice_array[$del_seq[$e]]);
                                }
                            }
                            return true;
                        });
                    return true;
                });
        }
        // 新規登録する
        else {
            // subseq を求める
            $queses =  $this->EramsDB->get('questions', null, [ 'sid' => $sid ]);
            $subseq = count($queses)+1;
            
            $questions = $this->getTableLocator()->get('questions');
            $choices = $this->getTableLocator()->get('choices');
            $chos = $this->EramsDB->get('choices', 'subseq', [ 'qid' => $qid ]);
            $choice_array = array();

            foreach( $chos as $choice ) {
                $choice_array[] = $choice;
            }
            
            // トランザクション処理で登録を行う
            $ret = $questions->getConnection()->transactional(
                function () use ($questions, $sid, $subseq, $qtext,
                $choices, $qid, $ctext) {
                    $entity = $questions->newEntity();
                    $entity->sid = $sid;
                    $entity->subseq = $subseq;
                    $entity->text = $qtext;
                    if ( ! $result = $questions->save($entity, ['atomic' => false])) {
                        return false;
                    }else{
                        $qid = $result->id;
                    }
                    $qid = $result->id;
                    // 選択肢を保存する
                    $choices->getConnection()->transactional(
                        function () use ($choices, $qid, $ctext) {
                            $entity = $choices->newEntity();
                            $entity->qid = $qid;
                            $entity->text = htmlentities($ctext[0],  ENT_QUOTES);
                            $entity->subseq = 1;
                            $entity->correct = 1;
                            // 選択肢が空白でなければ
                            if( strcmp( $ctext[0], '' ) != 0 ) {
                                if ( ! $choices->save($entity, ['atomic' => false]) ) {
                                    return false;
                                }
                            }
                            return true;
                        });
                    return $qid;
                });
        }

        // 結果を判断して表示を変える
        if( $ret ) {
            if( $qid == 0 ){
                $qid = $ret;
            }
            $this->Flash->set("問題を保存しました。");
        }
        else{
            $this->Erams->error("問題の保存に失敗しました。");
        }
        // ページを表示
        $this->redirect('/QuestionEdit?'.'eid='.$eid.'&sid='.$sid.'&qid='.$qid);
        return;
        
    }
}

?>