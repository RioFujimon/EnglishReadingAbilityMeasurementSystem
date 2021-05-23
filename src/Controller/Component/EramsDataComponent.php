<?php

namespace App\Controller\Component;

use Cake\Controller\Component;

class EramsDataComponent extends Component {

    public function initialize(array $config) {
        // コントローラを取得
        $this->controller = $this->_registry->getController();
    }

    public function getEsetByForm($redirect) {
        $eid = null;
        // get / post から eid を取得
        if ( $this->controller->request->is('get') ) {          
            $eid = $this->controller->request->getQuery('eid');
        } else if ($this->controller->request->is('post') ) {
            $eid = $this->controller->request->getData('eid');
        }
        $eid = h(trim($eid));
        // eid が回復できなければ
        if ( empty ($eid) ) {
            $this->controller->Flash->set("操作するテストセットが指定されていません。");
            $this->controller->redirect($redirect);
            return;
        }
        // テストセットの確保
        $esets = $this->controller->EramsDB->get("esets", null, [ 'id' => $eid ] );
        if ( $esets->count() != 1 ) {
            $this->controller->Flash->set("テストセットの探索に失敗しました。");
            $this->controller->redirect($redirect);
            return;
        }
        return $esets->first();
    }

    public function getSectionByForm($eid, $redirect) {
        $sid = null;
        // get / post から eid を取得
        if ( $this->controller->request->is('get') ) {
            $sid = $this->controller->request->getQuery('sid');
        } else if ($this->controller->request->is('post') ) {
            $sid = $this->controller->request->getData('sid');
        }
        $sid = h(trim($sid));
        // sid が回復できなければ
        if ( empty ($sid) ) {
            $this->controller->Flash->set("操作するセクションが指定されていません。");
            $this->controller->redirect($redirect);
            return;
        }
        // テストセットの確保
        $sections = $this->controller->EramsDB->get("sections", null,
        [ 'id' => $sid, 'eid' => $eid ] );
        if ( $sections->count() != 1 ) {
            $this->controller->Flash->set("テストセットに含まれるセクションの探索に失敗しました。");
            $this->controller->redirect($redirect);
            return;
        }
        return $sections->first();
    }

    public function getQuestionByForm($sid, $redirect) {
        $qid = null;
        // get / post から eid を取得
        if ( $this->controller->request->is('get') ) {
            $qid = $this->controller->request->getQuery('qid');
        } else if ($this->controller->request->is('post') ) {
            $qid = $this->controller->request->getData('qid');
        }
        $qid = h(trim($qid));        
        // qid が回復できなければ
        if ( empty ($qid) ) {
            $this->controller->Flash->set("操作する問題が指定されていません。");
            $this->controller->redirect($redirect);
            return;
        }
        // テストセットの確保
        $questions = $this->controller->EramsDB->get("questions", null, [ 'id' => $qid, 'sid' => $sid ] );
        if ( $questions->count() != 1 ) {
            $this->controller->Flash->set("セクションに含まれる問題の探索に失敗しました。");
            $this->controller->redirect($redirect);
            return;
        }
        return $questions->first();
    }    
}
?>
