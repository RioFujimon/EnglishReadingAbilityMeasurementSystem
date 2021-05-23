<?php
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;

class AppController extends Controller {

    public function initialize() {
        // スーパクラスを初期化
        parent::initialize();
        // セッションのデフォルト設定
        /*
        Session::write('Session', [
            'defaults' => 'php',
            'timeout' => '7200',
            'cookie' => 'ERAMS',
            'ini' => [
                // サイト上のページに訪問せず 120 分経つとクッキーを無効
                'session.cookie_lifetime' => 7200
            ]
        ]);
        */
        // Cake デフォルトのコンポーネントの読み込み
        $this->loadComponent('RequestHandler', [
            'enableBeforeRedirect' => false,
        ]);
        $this->loadComponent('Flash');
        // Erams に必要なコンポーネントの読み込み
        $this->loadComponent('Erams');
        $this->loadComponent('EramsDB');
        $this->loadComponent('EramsData');
        // Erams の共通レイアウトをセットする
        $this->viewBuilder()->setLayout('erams');
        // Css をセットする
        $this->Erams->setCss('erams.css');
        // セッションを取得して必要な viewVars をセットする
        $session = $this->request->getSession();
        if ( ($uid = $session->read('Erams.uid')) != null ) {
            $info = array();
            $gname =  $session->read('Erams.gname');
            $uname =  $session->read('Erams.uname');
            $gid =  $session->read('Erams.gid');
            $uid =  $session->read('Erams.uid');
            $uname = preg_replace('/^_[0-9]+_/', '', $uname);
            $info = [
                'gname' => $gname, 'uname' => $uname,
                'gid' => $gid, 'uid' => $uid
            ];
            $this->set('EramsLoginInfo', $info);
        }
        /*
         * Enable the following component for recommended CakePHP security settings.
         * see https://book.cakephp.org/3.0/en/controllers/components/security.html
         */
        //$this->loadComponent('Security');
    }

    public function index() {
        $this->Erams->setTitle("エラーページ");
        $this->Erams->error("このページを直接呼び出すことはできません。");
    }
}
