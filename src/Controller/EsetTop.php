<?php
// テストセット一覧ページ
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\ORM\Table;

class EsetTopController extends AppAdminController {

    private $esets;
    
    public function initialize() {
        // 親クラスをロードする
        parent::initialize();
        // ページのタイトルセット
        $this->Erams->setTitle('テストセット管理ページ');
        // 必要なデータを確保
        $this->esets = $this->EramsDB->get('esets', 'id');
        // view に表示するデータをセットする
        $this->set('Erams.Esets', $this->esets);
    }

    public function index() {
    }
}
?>
