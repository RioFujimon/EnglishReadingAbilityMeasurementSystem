<?php
// セット編集ページ
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\ORM\Table;

class EsetEditController extends AppAdminController{

    private $eset;
    
    public function initialize(){
        // 親クラスをロードする
        parent::initialize();
        // ページのタイトルセット
        $this->Erams->setTitle('セット編集ページ');
        // get 変数を取得
        $eid = h(trim($this->request->getQuery('eid')));
        if ( empty($eid) ) {
            $eid = h(trim($this->request->getData('eid')));
        }
        // テストセットを取得
        $esets = $this->EramsDB->get('esets', null, [ 'id' => $eid ] );
        // セットがなければセット一覧ページに戻る
        if ( $esets->count() != 1 ) {
            $this->Flash->set("操作対象となるテストセットが見つかりません。");
            $this->redirect('/Eset');
            return;
        }
        $this->eset = $esets->first();
    }

    public function index() {
        // 戻るページを設定
        $this->Erams->setBackPage('/Eset');
        // テストセットに含まれるセクションを確保
        $sections = $this->EramsDB->get('sections', 'subseq', [ 'eid' => $this->eset->id ] );
        // ビューに渡す情報をセット
        $this->set('EramsEset', $this->eset);
        $this->set('EramsSections', $sections);
    }

    // セクションの新規作成
    public function createSec() {
        // 編集モードか確認する
        if ( $this->eset->mode != "E" ) {
            $this->Flash->set("テスト提供中にテストセットの操作はできません。");
            $this->redirect('/EsetEdit?eid='.$this->eset->id);
            return;
        }
        // eset に含まれているセクションの数を確認
        $sections = $this->EramsDB->get('sections', null, [ 'eid' => $this->eset->id ]);
        $count = $sections->count();
        // セクションテーブルを確保
        $sections = $this->getTableLocator()->get('sections');
        // entity を取得
        $entity = $sections->newEntity();
        $entity->eid = $this->eset->id;
        $entity->subseq = $count + 1;
        $entity->title = "新規セクション";
        $entity->property = "作成日時".date("Y/m/d H:i:s");
        $entity->text = "ここに本文（英文）を挿入します";
        $entity->tlimit = "10";
        // entity の保存
        if ( $sections->save($entity) ) {
            $this->Flash->set('新たなセクションを追加しました。');
        } else {
            $this->Flash->set('セクションの新規追加に失敗しました。');
        }
        // ページを表示
        $this->redirect('/EsetEdit?eid='.$this->eset->id);
    }

    // セットを保存する
    public function saveEset(){
        // 編集モードか確認する
        if ( $this->eset->mode != "E" ) {
            $this->Flash->set("テスト提供中にテストセットの操作はできません。");
            $this->redirect('/EsetEdit?eid='.$this->eset->id);
            return;
        }
    	// post 変数を取得
        $title = h(trim($this->request->getData('title')));
        $property = h(trim($this->request->getData('property')));
        // セットテーブルを確保
        $esets = $this->getTableLocator()->get('esets');
        $eset = $esets->get($this->eset->id);
        // title がなければ
        if( empty($title) ) {
            $title = "タイトル未設定";
        }
        // entity の内容変更
        $eset->title = $title;
        $eset->property = $property;
        // entity の保存
        if( $esets->save($eset) ) {
            // 保存できた場合
            $this->Flash->set("テストセットを上書き保存しました（".date("Y/m/d H:i:s")."）。");
        } else {
            // 保存できなかった場合
            $this->Flash->set("テストセットの保存に失敗しました。");
        }
        // ページを表示
        $this->redirect('/EsetEdit?eid='.$this->eset->id);
        return;
    }

    // セクションを削除する
    public function delSec() {
        // 編集モードか確認する
        if ( $this->eset->mode != "E" ) {
            $this->Flash->set("テスト提供中にテストセットの操作はできません。");
            $this->redirect('/EsetEdit?eid='.$this->eset->id);
            return;
        }
        // post 変数を取得
        $sid = h(trim($this->request->getData('sid')));       
        // テストセットに含まれるセクションを取得
        $sections = $this->EramsDB->get('sections', null, [ 'eid' => $this->eset->id ]);
        $sids = array();
        foreach ( $sections as $section ) {
            $sids[] = $section->id;
        }
        $sections = $this->getTableLocator()->get('sections');
        // 削除と入れ替え操作
        $ret = $sections->getConnection()->transactional(
            function () use ($sections, $sid, $sids) {
                $count = 0;
                foreach ( $sids as $i ) {
                    $row = $sections->get($i);
                    if ( $row->id == $sid ) {
                        if ( ! $sections->delete($row, [ 'atmic' => false ] ) ) {
                            return false;
                        }
                    } else {
                        $row->subseq = ++$count;
                        if ( ! $sections->save($row, [ 'atmic' => false ] ) ) {
                            return false;
                        }
                    }
                }
                return true;
            });
        // 判断するよ
        if( $ret ) {
            $this->Flash->set('指定されたセクションを削除しました。');
        } else {
            $this->Flash->set("指定されたセクションの削除に失敗しました。");
        }
        // ページを表示
        $this->redirect('/EsetEdit?'.'eid='.$this->eset->id);
    }
    
    // セクションの順番を入れ替える
    public function swapSec() {
        // post 変数を取得
        $sid = h(trim($this->request->getData('sid')));
        $moveto = h(trim($this->request->getData('moveto')));
        // moveto が u, d でなければ上の階層にリダイレクト
        if( $moveto != "u" && $moveto != 'd' ) {
            $this->Flash->set("予期せぬエラーが発生しました。");
            $this->redirect('/EsetEdit?'.'eid='.$eid);
            return;
        }
        // eid を用意
        $eid = $this->eset->id;
        // セクションを取得
        $sections = $this->EramsDB->get('sections', null, [ 'id' => $sid, 'eid' => $this->eset->id ]);
        // セクションがないならば
        if ( $sections->count() != 1 ) {
            $this->Flash->set("操作対象のセクションが見つかりません。");
            $this->redirect('/EsetEdit?'.'eid='.$this->eset->id);
            return;
        }
        // 対象のセクションのsubseq
        $src_sid = $sid;
        $src_subseq = $sections->first()->subseq;        
        // セットに含まれるセクションを取得
        $sections = $this->EramsDB->get('sections', 'subseq', [ 'eid' => $this->eset->id ]);
        $section_array = array();
        foreach ( $sections as $section ) {
            $section_array[] = $section;
        }
        // subseq を入れ替える
        for ( $c = 0 ; $c < count($section_array) ; $c++ ) {
            if ( $section_array[$c]->id == $sid ) {
                // UP ボタンを押したとき
                if ( strcmp($moveto, 'u') == 0 ) {
                    if ( $c == 0 ) {
                        $dest_subseq = -1;
                        $dest_sid = -1;
                    } else {
                        $dest_subseq = $section_array[$c-1]->subseq;
                        $dest_sid = $section_array[$c-1]->id;
                    }
                }
                // DOWN ボタンを押したとき
                else {
                    if ( $c == count($section_array) - 1 ) {
                        $dest_subseq = -1;
                        $dest_sid = -1;
                    } else {
                        $dest_subseq = $section_array[$c+1]->subseq;
                        $dest_sid = $section_array[$c+1]->id;
                    }
                }
            }
        }
        // 一番上でUPまたは一番下でDOWNを押したとき
        if ( $dest_sid == -1 ) {
            $this->redirect('/EsetEdit?'.'eid='.$this->eset->id);
            return;
        }
        // セクションテーブルを確保
        $sections = $this->getTableLocator()->get('sections');
        $entities = array();
        $entities[0] = $sections->get($src_sid);
        $entities[1] = $sections->get($dest_sid);
        // トランザクション処理で登録を行う
        $ret = $sections->getConnection()->transactional(
            function () use ($sections, $entities) {               
                $tmp = $entities[0]->subseq;
                $entities[0]->subseq = $entities[1]->subseq;
                $entities[1]->subseq = $tmp;
                foreach ($entities as $entity) {
                    if ( ! $sections->save($entity, ['atomic' => false]) ) {
                        return false;
                    }
                }
                return true;
            });        
        // 結果を判断して表示を変える
        if ( ! $ret ) {
            $this->Flash->set("セクション順序の入れ替えに失敗しました。");
        }        
        $this->redirect('/EsetEdit?'.'eid='.$this->eset->id);
    }
}
?>
