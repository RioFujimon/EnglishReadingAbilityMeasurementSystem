<?php
// 集計結果一覧ページ
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\ORM\Table;

class StatTopController extends AppAdminController {

    private $esets;
    
    public function initialize() {
        // 親クラスをロードする
        parent::initialize();
        // ページのタイトルセット
        $this->Erams->setTitle('集計結果管理ページ');
        /*
        // 下部ページで使うセッションを消去
        $session = $this->request->getSession();
        $session->delete('Erams.QuestionKey');
        */
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
        $this->log( $tname.' StatTop - index', 'info');
    }

}

?>