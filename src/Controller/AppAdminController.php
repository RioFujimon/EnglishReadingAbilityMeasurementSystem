<?php
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;

class AppAdminController extends AppController {

    public function initialize() {
        // スーパクラスを初期化
        parent::initialize();
        // 管理者用の Css を読み込む
        $this->Erams->setCss('erams_admin.css');
        // 管理者としてログイン済みかチェックする
        $this->Erams->checkAdmin();
        // これで呼び出し元のクラスの情報を取得
        $trace = debug_backtrace();
        $ref = new \ReflectionClass($trace[0]['object']);
        // 呼び出し元のクラスが AdminTop/Logout で無ければ
        // トップページに戻るボタン表示用のデータを作成
        $m = array();
        preg_match('/([^\\\]+)$/', get_called_class(), $m);
        if ( count($m) == 2 ) {
            $class = $m[1];
        } else {
            $class = "";
        }
        if ( $class != "AdminTopController" && $class != "LogoutController" ) {
            $this->set("EramsTop", "/AdminTop");
        }
    }
}
