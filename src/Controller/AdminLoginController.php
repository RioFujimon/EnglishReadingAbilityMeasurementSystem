<?php
// 教員ログインページ
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\ORM\Table;

class AdminLoginController extends AppController {

    public function initialize(){
        // 親クラスをロードする
        parent::initialize();
        // ページのタイトルセット
        $this->Erams->setTitle('教員ログインページ');
        // Css だけは管理者用を利用する
        $this->Erams->setCss('erams_admin');
    }
    
    public function index() {
        // ログ出力
        $this->log('unknown AdminLogin - index', 'info');

        // セッションを取得
        $session = $this->request->getSession();
        // Erams で使うセッションを強制的に消去
        $session->delete('Erams');
        // ポスト変数を確保
        $uname = h(trim($this->request->getData('uname')));
        $passwd = $this->request->getData('passwd');
        // ユーザ名またはパスワードが入力されていた場合
        if( ! empty($uname) || ! empty($passwd) ) {
            // ユーザテーブルを確保
            $users = $this->getTableLocator()->get('Users');
            // admin の名前を取得
            $results = $users->find()->where(['uname' => $uname]);
            $row = $results->first();
            // admin と passwd の名前をチェック
            if ( $row != null || count($row) != 0 && password_verify($passwd, $row->passwd) ) {
                // セッションをセットする
                $session->write('Erams.uid', '1');
                $session->write('Erams.gid', '1');
                $session->write('Erams.uname', $uname);
                $session->write('Erams.gname', 'admin');
                // トップページに遷移する
                $this->redirect('/AdminTop');
            } else {
                // パスワードの認証に失敗したら
                $this->Flash->set("ログインに失敗しました。");
            }
        }
    }
}
?>
