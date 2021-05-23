<?php
// ログアウトページ
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\ORM\Table;

class LogoutController extends AppController{

    public function initialize(){
        // 親クラスをロードする
        parent::initialize();
        // ページのタイトルセット
        $this->Erams->setTitle('ログアウトページ');
        // Css だけは基本用を利用する
        $this->Erams->setCss('erams');
    }
    
    public function index() {
        // セッションを取得
        $session = $this->request->getSession();
        // ログイン名を取得
        $tname = $session->read('Erams.uname');
        // ログ出力
        $this->log( $tname.' Logout - index', 'info');
        
        // Erams で使うセッションを強制的に消去
        $session->delete('Erams');
        $this->Flash->set("ログアウトしました。");
        // ポータルページに移動かな
        $this->redirect('/Portal');
        return;
    }
}
?>
