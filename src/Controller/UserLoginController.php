<?php
// 学生ログインページ
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\ORM\Table;

class UserLoginController extends AppController {

    public function initialize(){
        // 親クラスをロードする
        parent::initialize();
        // ページのタイトルセット
        $this->Erams->setTitle('学生ログインページ');
        // Css だけは学生用を利用する
        $this->Erams->setCss('erams_user');
    }
    
    public function index() {
        // ログ出力
        $this->log('unknown UserLogin - index', 'info');
        
        // セッションを取得
        $session = $this->request->getSession();
        // Erams で使うセッションを強制的に消去
        $session->delete('Erams');
        // ポスト変数を確保
        $gid = h(trim($this->request->getData('gid')));
        $uname = h(trim($this->request->getData('uname')));
        $passwd = h(trim($this->request->getData('passwd')));
        // グループ名を取得
        $groups =  $this->getTableLocator()->get('Groups');
        $groupList = $groups->find('all')->where([ 'id !=' => '1' ]);
        $groupInfo = array();
        foreach ( $groupList as $group ) {
            $groupInfo[] = [ 'gname' => $group->gname, 'gid' => $group->id];
            if ( $group->id == $gid ) {
                $gname = $group->gname;
            }
        }
        $this->set('EramsGroups', $groupInfo);
        // ユーザ名またはパスワードが入力されていた場合
        if( ! empty($gid) || ! empty($uname) || ! empty($passwd) ) {
            // ユーザテーブルを確保
            $users = $this->getTableLocator()->get('Users');
            // user の名前を取得            
            $uname = sprintf("_%d_%s", $gid, $uname);
            $results = $users->find()->
                where(['gid' => $gid, 'uname' => $uname, 'passwd' => $passwd]);
            if ( $results->count() == 1 ) {
                // レコードの結果を取得
                $result = $results->first();
                // セッションをセットする
                $session->write('Erams.uid', $result->id);
                $session->write('Erams.gid', $result->gid);
                $session->write('Erams.uname', $result->uname);
                $session->write('Erams.gname', $group->gname);
                // トップページに遷移する
                $this->redirect('/UserTop');
                return;
            } else {
                // パスワードの認証に失敗したら
                $this->Flash->set("ログインに失敗しました。");
            }
        }
    }
}
?>
