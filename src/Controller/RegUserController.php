<?php
// 学生登録ページ
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\ORM\Table;

class RegUserController extends AppAdminController{

    public function initialize(){
        // 親クラスをロードする
        parent::initialize();
        // ページのタイトルセット
        $this->Erams->setTitle('学生情報編集ページ');
    }

    public function index() {
        // セッションを確保
        $session = $this->request->getSession();
        // ログイン名を取得
        $tname = $session->read('Erams.uname');
        // ログ出力
        $this->log( $tname.' RegUser - index', 'info');
        
        // グループのリストを取得
        $groups = $this->EramsDB->get('Groups', 'gname', [ 'id != ' => '1' ] );
        // ユーザのリストを取得
        $users = $this->EramsDB->get('Users', 'uname', [ 'id != ' => '1' ] );
        // グループのユーザ情報を集計してView変数に確保
        $groupInfo = array();
        foreach ( $groups as $group ) {
            $gname = $group->gname;
            $groupInfo[$group->id] = array();
            $groupInfo[$group->id]['gid'] = $group->id;
            $groupInfo[$group->id]['gname'] = $group->gname;
            $groupInfo[$group->id]['count'] = 0;
        }
        foreach ( $users as $user ) {
            $groupInfo[$user->gid]['count']++;
        }
        $this->set('EramsGroupInfo', $groupInfo);
    }

    public function groupIndex() {
        // セッションを確保
        $session = $this->request->getSession();
        // ログイン名を取得
        $tname = $session->read('Erams.uname');
        
        // get 変数を取得
        $gid = trim(h($this->request->getQuery('gid')));

        // ログ出力
        $this->log( $tname.' RegUser - groupIndex  gid: '.$gid, 'info');
        
        // gid のチェック
        if ( empty($gid) ) {
            $this->Flash->set("表示対象とする機関が見つかりません。");
            $this->redirect('/RegUser/index');
        } else if ( $gid == '1' ) {
            $this->Flash->set("admin グループの内容を編集することはできません。");
            $this->redirect('/RegUser/index');
            return;
        }
        // グループのリストを取得
        $group = $this->EramsDB->get('Groups', 'id', [ 'id' => $gid ]);
        // ユーザのリストを取得
        $users = $this->EramsDB->get('Users', 'uname', [ 'gid' => $gid ]);
        // ユーザのリストをView変数にセット
        $this->set('EramsGid', $group->first()->id);
        $this->set('EramsGname', $group->first()->gname);
        $this->set('EramsUsers', $users);
        // このページから戻るための処理
        $this->Erams->setBackPage('/RegUser');
    }
    
    public function edit() {
        // セッションを確保
        $session = $this->request->getSession();
        // ログイン名を取得
        $tname = $session->read('Erams.uname');
        
        // get 変数を取得
        $gid = trim(h($this->request->getQuery('gid')));

        // ログ出力
        $this->log( $tname.' RegUser - edit  gid: '.$gid, 'info');
        
        // gid のチェック
        if ( empty($gid) ) {
            $this->Flash->set("学生を登録する機関が見つかりません。");
            $this->redirect('/RegUser/index');
        } else if ( $gid == '1' ) {
            $this->Flash->set("admin グループの内容を編集することはできません。");
            $this->redirect('/RegUser/index');
            return;
        }
        // グループのリストを取得
        $group = $this->EramsDB->get('Groups', 'id', [ 'id' => $gid ]);
        // ユーザのリストを取得
        $users = $this->EramsDB->get('Users', 'uname', [ 'gid' => $gid ]);
        // ユーザのリストをView変数にセット
        $this->set('EramsGid', $group->first()->id);
        $this->set('EramsGname', $group->first()->gname);
        $this->set('EramsUsers', $users);
        // このページから戻るための処理
        $this->Erams->setBackPage('/RegUser');
    }
    
    public function add() {
        // セッションを確保
        $session = $this->request->getSession();
        // ログイン名を取得
        $tname = $session->read('Erams.uname');
        
        // post 変数を取得
        $gid = h(trim($this->request->getData('gid')));

        // ログ出力
        $this->log( $tname.' RegUser - add  gid: '.$gid, 'info');
        
        // gid のチェック
        if ( empty($gid) ) {
            $this->Flash->set("学生を登録する機関が見つかりません。");
            $this->redirect('/RegUser/index');
            return;
        } else if ( $gid == '1' ) {
            $this->Flash->set("admin グループの内容を編集することはできません。");
            $this->redirect('/RegUser/index');
            return;
        }
        $prefix = h(trim($this->request->getData('prefix')));
        $start = h(trim($this->request->getData('start')));
        if ( empty($start) ) {
            $start = '0';
        }
        $count = h(trim($this->request->getData('count')));
        // グループテーブルを確保
        $groups = $this->EramsDB->get('Groups', 'gname', [ 'id' => $gid ] );
        if ( $groups->count() != 1 ) {
            $this->Flash->set("学生を登録するグループを見つけることが出来ませんでした。");
            $this->redirect('/RegUser/index');
            return;
        }
        $group = $groups->first();
        // ユーザテーブルを確保
        $users = $this->getTableLocator()->get('Users');
        // post 変数の中身をチェック
        $mess = '';
        if ( 4 < strlen($prefix) ) {
            $mess .= '接頭語は4文字以下にする必要があります。'."\n";
        }
        if ( ! preg_match('/^[a-zA-Z]+$/', $prefix) ) {
            $mess .= '接頭語に不適切な文字が入っています。'."\n";
        }
        if ( ! preg_match('/^[0-9]+$/', $start) ) {
            $mess .= '開始番号に不適切な文字が入っています。'."\n";
        }
        if ( empty($count) ) {
            $mess .= '人数が指定されていません。'."\n";
        } else if ( ! preg_match('/^[0-9]+$/', $count) ) {
            $mess .= '人数に不適切な文字が入っています。'."\n";
        }
        // post 変数の中身に問題があればエラー
        if ( ! empty($mess) ) {
            $this->Flash->set($mess);
            $this->redirect('/RegUser/groupIndex?'.'gid='.$gid);
            return;
        }
        // ユーザの基本データを作成
        $records = array();
        for ( $c = $start ; $c < $start + $count ; $c++ ) {
            $gidstr = sprintf('%d', $group->id);
            $num = sprintf("%04d", $c);
            $passwd = substr(bin2hex(random_bytes(8)), 0, 8);
            $records[] = [ 'gid' => $gid,
            'uname' => '_'.($gidstr).'_'.$prefix.$num, 'passwd' => $passwd ];
        }
        // 生徒の登録
        $ok = 0;
        $ng = 0;
        foreach ( $records as $record ) {
            $row = $this->EramsDB->get('users',
            null, [ 'uname' => $record['uname'] ] );
            if ( $row->count() == 0 ) {
                $entity = $users->newEntity($record);
                // 保存する
                $users->save($entity);
                $ok++;
            } else {
                $ng++;
            }
        }
        // メッセージを設定してページを表示
        $this->Flash->set('学生を'.$ok.'名追加しました'.
        '（うち'.$ng.'名の登録に失敗しました）。');
        $this->redirect('/RegUser/groupIndex?'.'gid='.$gid);
    }    

    public function deleteById() {
        // セッションを確保
        $session = $this->request->getSession();
        // ログイン名を取得
        $tname = $session->read('Erams.uname');
        
        // gid を取得
        $gid = h(trim($this->request->getData('gid')));
        // gid のチェック
        if ( $gid == '1' ) {
            // ログ出力
            $this->log( $tname.' RegUser - deleteById  gid: '.$gid, 'info');
            
            $this->Flash->set("admin グループの内容を編集することはできません。");
            $this->redirect('/RegUser/index');
            return;
        }
        // uid を取得
        $uid = h(trim($this->request->getData('uid')));
        
        // ログ出力
        $this->log( $tname.' RegUser - deleteById  gid: '.$gid.' uid: '.$uid, 'info');

        // ユーザが居るか確認する
        $user = $this->EramsDB->get('Users', null, [ 'id' => $uid, 'gid' => $gid ] );
        if ( $user->count() != 1 ) {
            $this->Flash->set('削除するユーザが見つかりません。');
        }
        // もし削除するユーザが居れば
        else {
            // ユーザ名を取っておく
            $uname = preg_replace('/^_[0-9]+_/', '', $user->first()->uname);
            // ユーザテーブルを確保
            $users = $this->getTableLocator()->get('Users');
            $entity = $users->get($uid);
            $result = $users->delete($entity);
            if ( ! $result ) {
                $this->Flash->set('ユーザ「'.$uname.'」の削除に失敗しました。');
            } else {
                $this->Flash->set('ユーザ「'.$uname.'」を削除しました。');
            }
        }
        $this->redirect('/RegUser/groupIndex?'.'gid='.$gid);
    }

    public function deleteMulti() {
        // セッションを確保
        $session = $this->request->getSession();
        // ログイン名を取得
        $tname = $session->read('Erams.uname');
        
        // gid を取得
        $gid = h(trim($this->request->getData('gid')));

        // gid のチェック
        if ( $gid == '1' ) {
            // ログ出力
            $this->log( $tname.' RegUser - deleteMulti  gid: '.$gid, 'info');
            
            $this->Flash->set("admin グループの内容を編集することはできません。");
            $this->redirect('/RegUser/index');
            return;
        }
        // prefix, start, end を取得
        $prefix = h(trim($this->request->getData('prefix')));
        $start = h(trim($this->request->getData('start')));
        $end = h(trim($this->request->getData('end')));
        
        // ログ出力
        $this->log( $tname.' RegUser - deleteMulti  gid: '.$gid.
        ' prefix: '.$prefix.' start: '.$start.' end: '.$end, 'info');
        
        // パラメータの確認
        if ( $end < $start ) {
            $this->Flash->set("開始番号と終了番号の関係（終了番号＜開始番号）が不正です。");
        }
        // 削除ループ
        $deleted = $skip = $fail = 0;
        for ( $c = $start ; $c <= $end && $c <= 9999 ; $c++ ) {
            // ユーザ名の構築
            $name = sprintf("_%d_%s%04d", $gid, $prefix, $c);
            // ユーザが居るか確認する
            $user = $this->EramsDB->get('Users', null, [ 'uname' => $name ] );
            // ユーザが居ない場合
            if ( $user->count() != 1 ) {
                $skip++;
            }
            // もし削除するユーザが居れば
            else {
                // ユーザテーブルを確保
                $users = $this->getTableLocator()->get('Users');
                $entity = $users->get($user->first()->id);
                $result = $users->delete($entity);
                if ( ! $result ) {
                    $fail++;
                } else {
                    $deleted++;
                }
            }
        }
        // メッセージを作成
        if ( $skip == 0 && $fail == 0 ) {
            $mess = '全'.$deleted.'人のデータ削除を行いました。';
        } else if ( $fail == 0 ) {
            $mess = '全'.$deleted.'人'.
                '（スキップ'.$skip.'人）のデータ削除を行いました。';
        } else {
            $mess = '全'.$deleted.'人'.
                '（スキップ'.$skip.'人、失敗'.$fail.'人）のデータ削除を行いました。';
        }
        $this->Flash->set($mess);
        $this->redirect('/RegUser/groupIndex?'.'gid='.$gid);
    }

    public function changePass() {
        // セッションを確保
        $session = $this->request->getSession();
        // ログイン名を取得
        $tname = $session->read('Erams.uname');
        
        // gid を取得
        $gid = h(trim($this->request->getData('gid')));
        // gid のチェック
        if ( $gid == '1' ) {
            // ログ出力
            $this->log( $tname.' RegUser - changePass  gid: '.$gid, 'info');
            
            $this->Flash->set("admin グループの内容を編集することはできません。");
            $this->redirect('/RegUser/index');
            return;
        }
        // uid を取得
        $uid = h(trim($this->request->getData('uid')));
        
        // ログ出力
        $this->log( $tname.' RegUser - changePass  gid: '.$gid.' uid: '.$uid, 'info');

        // ユーザが居るか確認する
        $user = $this->EramsDB->get('Users', null, [ 'id' => $uid ] );
        if ( $user->count() != 1 ) {
            $this->Flash->set('対象とするユーザが見つかりません。');
        }
        // もしユーザが居れば
        else {
            // ユーザ名を取っておく
            $uname = preg_replace('/^_[0-9]+_/', '', $user->first()->uname);
            // ユーザテーブルを確保
            $users = $this->getTableLocator()->get('Users');
            $entity = $users->get($uid);
            $users->patchEntity( $entity,
            ['passwd' => substr(bin2hex(random_bytes(8)), 0, 8)] );
            $result = $users->save($entity);
            if ( ! $result ) {
                $this->Flash->set('ユーザ「'.$uname.'」のパスワード変更が失敗しました。');
            } else {
                $this->Flash->set('ユーザ「'.$uname.'」のパスワードを変更しました。');
            }
        }
        $this->redirect('/RegUser/groupIndex?'.'gid='.$gid);
    }
}
?>
