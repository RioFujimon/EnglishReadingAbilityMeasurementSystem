<?php
// 学生ログインページ
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\ORM\Table;

class UserController extends AppController{

    public function initialize(){
        // 親クラスをロードする
        parent::initialize();
        // ページのタイトルセット
        $this->Erams->setTitle('学生ログインページ');
        // Css だけは学生用を利用する
        $this->Erams->setCss('erams_user');
    }
    
    public function index() {
        // セッションを取得
        $session = $this->request->getSession();
        // Erams で使うセッションを強制的に消去
        $session->delete('Erams');
        // ポスト変数を確保
        $uname = $this->request->getData('uname');
        $uname = trim(htmlspecialchars($uname));
        $passwd = $this->request->getData('passwd');
        // ユーザ名またはパスワードが入力されていた場合
        if( ! empty($uname) || ! empty($password) ) {
            // テーブルオブジェクトを取得
            $settings = $this->getTableLocator()->get('Settings');
            // uname に対応するレコードを取得
            $results = $settings->find()->where(['uname' => $uname]);
            $row = $results->first();            
            // パスワードをチェック
            if ( password_verify($passwd, $row->passwd) ) {
                // gname 以外を確保
                $uid = $row->uid;
                $gid = $row->gid;
                // gname を取得しないと
                $groups = $this->getTableLocator()->get('Groups'); 
                $results = $groups->find->where(['gid' => $row->gid]);
                $row = $results->first();
                $gname = $row->gname;
                // セッションをセットする
                $session->write('Erams.uid', $uid);
                $session->write('Erams.gid', $gid);
                $session->write('Erams.uname', $uname);
                $session->write('Erams.gname', $gname);
                // トップページに遷移する
                $this->redirect('/UserTop');
            } else {
                // パスワードの認証に失敗したら
                $this->Flash->set("ログインに失敗しました。");
            }
        }
    }
}
?>
