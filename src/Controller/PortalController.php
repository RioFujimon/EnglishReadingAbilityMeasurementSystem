<?php
// ポータルページ
namespace App\Controller;

use Cake\Controller\Controller;

class PortalController extends AppController {

    public function initialize() {
        // 親クラスを初期化する
        parent::initialize();
        // ページのタイトルをセットする
        $this->Erams->setTitle('ポータルページ');
    }
    
    public function index() {
        // ログ出力
        $this->log('unknown Portal - index', 'info');
        
        // セッションを取得
        $session = $this->request->getSession();
        // Erams で使うセッションを強制的に消去
        $session->delete('Erams');
        // Users テーブルの中にレコードがあるか
        $users = $this->getTableLocator()->get('Users');
        $query = $users->find('all');
        // admin がセットされていなければ初期化用コントロールにリダイレクトする。
        if ( $query->count() == 0 ) {
            $this->redirect('/Init');
            return;
        }
    }
}
?>
