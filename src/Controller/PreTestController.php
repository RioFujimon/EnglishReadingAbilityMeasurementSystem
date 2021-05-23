<?php
// 学生トップページ
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\ORM\Table;

class PreTestController extends AppUserController{

    public function initialize(){
        // 親クラスをロードする
        parent::initialize();
        // ページのタイトルセット
        $this->Erams->setTitle('テスト受験前注意ページ');
        // セッションを確保
        $session = $this->request->getSession();
    }
    
    public function index() {
        // セッションを確保
        $session = $this->request->getSession();
        // ログイン名を取得
        $tname = $session->read('Erams.uname');
                
        // 戻るボタンの設定
        $this->Erams->setBackPage('/UserTop');
        // eid を取得
        $eid = h(trim($this->request->getQuery('eid')));

        // セッションを確保
        //$session = $this->request->getSession();
        $session->delete('Erams.Test');
        if ( empty($eid) ) {
            // ログ出力
            $this->log( $tname.' PreTest - index  eid: empty', 'info');
            
            $this->Flash->set("テストセットのidが指定されていません。");
            $this->redirect('/UserTop');
            return;
        }
        // ログ出力
        $this->log( $tname.' PreTest - index  eid: '.$eid, 'info');
        
        // eset を確認
        $esets = $this->EramsDB->get('esets', null, [ 'id' => $eid ]);
        if ( $esets->count() != 1 ) {
            $session->delete('Erams.Test');
            $this->Flash->set("指定されたテストセットが見つかりません。");
            $this->redirect('/UserTop');
            return;
        }
        // eid をセット
        $session->write('Erams.Test.Eid', $eid);
        $this->set('Erams.Test.Eid', $eid);
    }

    public function phase1() {
        // セッションを確保
        $session = $this->request->getSession();
        // ログイン名を取得
        $tname = $session->read('Erams.uname');
        
        // eid を取得
        $eid = $session->read('Erams.Test.Eid');

        if ( empty($eid) ) {
            // ログ出力
            $this->log( $tname.' PreTest - phase1  eid: empty', 'info');
            
            $this->Flash->set("テストセットのidが指定されていません。");
            $this->redirect('/UserTop');
            return;
        }
        // ログ出力
        $this->log( $tname.' PreTest - phase1  eid: '.$eid, 'info');
        
        // eset を確認
        $esets = $this->EramsDB->get('esets', null, [ 'id' => $eid ]);
        if ( $esets->count() != 1 ) {
            $session->delete('Erams.Test');
            $this->Flash->set("指定されたテストセットが見つかりません。");
            $this->redirect('/UserTop');
            return;
        }
        // 戻るボタンの設定
        $this->Erams->setBackPage('/PreTest', [ 'eid' => $eid ]);
        $this->set('Erams.Test.Eid', $eid);
    }
}
?>
