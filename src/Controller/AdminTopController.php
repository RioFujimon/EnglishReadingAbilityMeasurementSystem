<?php
// 教員ログインページ
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\ORM\Table;

class AdminTopController extends AppAdminController{

    public function initialize(){
        // 親クラスをロードする
        parent::initialize();
        // ページのタイトルセット
        $this->Erams->setTitle('教員トップページ');
    }
    
    public function index() {
        // セッションを確保
        $session = $this->request->getSession();
        // ログイン名を取得
        $tname = $session->read('Erams.uname');
        // ログ出力
        $this->log( $tname.' AdminTop - index', 'info');
    }
}
?>
