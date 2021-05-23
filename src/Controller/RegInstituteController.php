<?php
// 機関登録ページ
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\ORM\Table;

class RegInstituteController extends AppAdminController{

    public function initialize(){
        // 親クラスをロードする
        parent::initialize();
        // ページのタイトルセット
        $this->Erams->setTitle('グループ登録ページ');
    }

    public function index() {
        // セッションを確保
        $session = $this->request->getSession();
        // ログイン名を取得
        $tname = $session->read('Erams.uname');
        // ログ出力
        $this->log( $tname.' RegInstitute - index', 'info');
        
        // 必要なデータをVeiw 変数に保存
        $EramsGroups = $this->EramsDB->get('Groups', 'gname', [ 'id !=' => '1' ] );
        $this->set('EramsGroups', $EramsGroups);
    }

    public function add() {
        // セッションを確保
        $session = $this->request->getSession();
        // ログイン名を取得
        $tname = $session->read('Erams.uname');
        
        // post 変数を取得
        $institute =  trim(h($this->request->getData('institute')));

        // ログ出力
        $this->log( $tname.' RegInstitute - add  gname: '.$institute, 'info');
        
        // グループテーブのレコードを確保
        $EramsGroups = $this->EramsDB->get('Groups', 'gname', [ 'gname' => $institute ] );
        // 所属機関が既に存在していたら登録せずに終了
        if ( $EramsGroups->count() != 0 ) {
            $this->Flash->set("機関「".$institute."」は既に存在しています。");
            $this->redirect('/RegInstitute/index');
            return;
        } else if ( empty($institute) ) {
            $this->Flash->set("登録する機関の名前が指定されていません。");
            $this->redirect('/RegInstitute/index');
            return;            
        }
        // 英字または数字以外が含まれていたら登録せずに終了
        if( preg_match("/^[a-zA-Z0-9]+$/", $institute) != 1 ) {
            $this->Flash->set("使用できない文字が含まれているため登録できません。");
            $this->redirect('/RegInstitute/index');
            return;
        }
        // グループテーブルを確保
        $groups = $this->getTableLocator()->get('Groups');
        // 挿入するレコードを作成
        $record = [ 'gname' => $institute ];
        // 挿入するレコードを Entity に入れて
        $entity = $groups->newEntity($record);
        // 保存する
        $groups->save($entity);
        // メッセージを設定してページを表示
        $this->Flash->set("新規に機関「".$institute."」を保存しました。");
        $this->redirect('/RegInstitute/index');
    }    

    public function del() {
        // セッションを確保
        $session = $this->request->getSession();
        // ログイン名を取得
        $tname = $session->read('Erams.uname');
        
        // get 変数を取得
        $gid = h(trim($this->request->getQuery('gid')));
        
        // 指定されたグループテーブのレコードを確保
        $EramsGroups =  $this->EramsDB->get('Groups', 'gname',
        [ 'id' => $gid, 'id !=' => '1' ] );
        // レコードがあれば削除
        if ( $EramsGroups->count() == 1 ) {
            // グループ名を確保して
            $gname = $EramsGroups->first()->gname;

            // ログ出力
            $this->log( $tname.' RegInstitute - del  gid: '.$gid.
            ' gname: '.$gname, 'info');
            
            // グループテーブルを確保
            $groups = $this->getTableLocator()->get('Groups');
            $entity = $groups->get($gid);
            // 削除実行
            if ( $groups->delete($entity) ) {
                $this->Flash->set('機関「'.$gname.'」を削除しました。');
            } else {
                $this->Flash->set('機関「'.$gname.'」の削除に失敗しました。');
            }
        }
        // レコードがなければメッセージ設定してページを表示するだけ
        else {
            // ログ出力
            $this->log( $tname.' RegInstitute - del  gid: '.$gid, 'info');
            
            $this->Flash->set('削除対象の機関が見つかりません。');
        }
        $this->redirect('/RegInstitute/index');
    }
}
?>
