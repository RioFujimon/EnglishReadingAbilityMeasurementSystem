<?php
// 初期設定ページ
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\ORM\Table;

class InitController extends AppController {

    public function initialize() {
        // 親クラスをロードする
        parent::initialize();
        // ページのタイトルをセットする
        $this->Erams->setTitle('管理者登録');
        // セッションを取得
        $session = $this->request->getSession();
        // Erams で使うセッションを強制的に消去
        $session->delete('Erams');
    }
    
   public function index() {
       // ログ出力
       $this->log('unknown Init - index', 'info');
       
       // Groups/Users テーブルを確保
       $groups = $this->getTableLocator()->get('Groups'); 
       $users = $this->getTableLocator()->get('Users'); 
       // Users テーブルの中にレコードがあるか
       $query = $groups->find('all');
       // groups にレコードがあれば不正アクセス処理へ
       if ( $query->count() != 0 ) {
           $this->Erams->error("すでに管理者が登録されていますので、".
           "このページは利用することができません。");
           return;
       }
       // post 変数を取得
       $uname = h(trim($this->request->getData('uname')));
       $passwd = $this->request->getData('passwd');
       // post 変数のどちらかがセットされていればその内容をチェック 
       if ( ! empty($uname) || ! empty($uname) ) {
           $mess = "";
           if ( strlen($uname) < 4 ) {
               $mess .= "ユーザ名は4文字以上にする必要があります。\n";
           }
           if ( ! preg_match("/^[a-zA-Z_]+$/", $uname) ) {
               $mess .= "ユーザ名には a-zA-Z, _ ".
                   "以外の文字は利用することが出来ません。\n";
           }
           if ( strlen($passwd) < 4 ) {
               $mess .= "パスワードは4文字以上にする必要があります。\n";
           }
           // エラーメッセージがあればそれをセットして再度フォームを表示
           if ( $mess != "" ) {
               $this->Flash->set($mess);
               return;
           }
       }
       // post 変数のどちらもセットされていなければフォームを表示
       else {
           return;
       }
       // パスワードをハッシュ化
       $hashed_pass = password_hash($passwd, PASSWORD_BCRYPT);
       // 挿入するレコードを作成
       $g_record = [ 'gname' => 'admin' ];
       $u_record = [ 'uname' => $uname, 'passwd' => $hashed_pass ];
       // トランザクション処理で登録を行う
       $ret = $groups->getConnection()->transactional(
           function () use ($groups, $users, $g_record, $u_record) {
               $g_entity = $groups->newEntity($g_record);
               $u_entity = $users->newEntity($u_record);
               if ( ! $groups->save($g_entity, ['atomic' => false]) ) {
                   return false;
               }
               $u_entity->gid = $g_entity->id;
               if ( ! $users->save($u_entity, ['atomic' => false]) ) {
                   return false;
               }
               return true;
           });
       // 結果を判断して表示を変える。
       if ( $ret ) {
           $this->render("saved");
        } else {
           $this->Erams->error("データベースのレコード登録に失敗しました。");
       }
   }
}
?>
