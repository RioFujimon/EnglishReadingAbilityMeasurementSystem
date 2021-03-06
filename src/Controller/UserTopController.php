<?php
// 学生トップページ
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\ORM\Table;
use Cake\Datasource\ConnectionManager;

class UserTopController extends AppUserController {

    public function initialize(){
        // 親クラスをロードする
        parent::initialize();
        // ページのタイトルセット
        $this->Erams->setTitle('学生トップページ');
        // セッションを確保して消しておく
        $session = $this->request->getSession();
        $session->delete('Erams.Test');
    }
    
    public function index() {
        // セッションを確保
        $session = $this->request->getSession();
        // ログイン名を取得
        $tname = $session->read('Erams.uname');
        // ログ出力
        $this->log( $tname.' UserTop - index', 'info');
        
        // Eset のレコードを確保
        $EramsEsets =  $this->EramsDB->get('Esets', 'title', [ 'mode' => 1 ]);
        // Eset のレコードをセット
        $this->set('EramsEsets', $EramsEsets);

        // rsetのレコードを取得
        $EramsRsets = $this->EramsDB->get('Rsets');
        //ログインしたユーザのIDを取得
        $uid = $session->read('Erams.uid');

        //rsetsテーブルのidを取得するための配列を宣言
        $ridArray = array();
        
        //eidを取得するための配列を宣言
        $eidArray = array();

        //更新日時(modified)を取得するための配列を宣言
        $modArray = array();
                
        for($i = 0; $i < count($EramsRsets->toArray()); $i++){
            //ログインしたユーザのIDとrsetsテーブルのuidが一致した時
            if($uid == $EramsRsets->toArray()[$i]['uid']){
                //配列ridArrayにidを格納
                array_push($ridArray, $EramsRsets->toArray()[$i]['id']);
                //配列eidArrayにeidを格納
                array_push($eidArray, $EramsRsets->toArray()[$i]['eid']);
                                
                $modTimeObj = $EramsRsets->toArray()[$i]['modified'];
                //フォーマットを変換
                $modTimeObj = $modTimeObj->i18nFormat('yyyy-MM-dd HH:mm:ss');
                //配列modArrayにmodified(更新日時)を格納
                array_push($modArray, $modTimeObj);
            }
        }

        //modified(更新日時)を表示(テスト用)
        /*for($i = 0; $i < count($modArray); $i++){
            print_r($modArray[$i]);
            echo "</br>";
        }*/

        //配列eidArrayから重複した値を削除
        $eidArray = array_unique($eidArray);
        
        //配列eidArrayのキーを振り直す
        $eidArray = array_values($eidArray);
        
        //配列eidArrayを降順にソートする
        //rsort($eidArray);
        
        //被り除外後eidのを表示する(テスト用)
        //print_r($eidArray);
        //echo "</br>";

        //過去に受験したテストのタイトルを格納する配列を宣言
        $titleArray = Array();

        //esetsテーブルからid=〇〇のtitleを取得
        $connection = ConnectionManager::get('default');
        for($i = 0; $i < count($eidArray); $i++){
            $results = $connection
                     ->execute('select title from esets where id ='.$eidArray[$i])
                     ->fetchAll('assoc');
            array_push($titleArray, $results[0]['title']);
        }

        //過去に受験したテストのタイトルを表示(テスト用)
        /*for($i = 0; $i < count($titleArray); $i++){
            print_r($titleArray[$i]);
            echo "</br>";
        }*/


        //最新更新日時を格納する配列を宣言
        $newModArray = Array();
        
        //rsetsテーブルからeid=〇〇の最新modifiedを取得
        $connection = ConnectionManager::get('default');
        for($i = 0; $i < count($eidArray); $i++){
            $results = $connection
                     ->execute('SELECT (MAX(modified)) from rsets where eid ='.$eidArray[$i])
                     ->fetchAll('assoc');

            //最新受験日時のフォーマットを「Y-m-d H:i:s」に変更
            $newMod = date("Y/m/d H:i:s", strtotime($results[0]['max']));

            //配列$newModArrayにeid = 〇〇の最新のmodifiedを追加
            //array_push($newModArray, $results[0]['max']);
            array_push($newModArray, $newMod);
        }

        //最新更新日時(modified)を表示(テスト用)
        /*for($i = 0; $i < count($newModArray); $i++){
            print_r('最新更新日：'.$newModArray[$i]);
            echo "</br>";
        }*/

        //uid, rsetsのid, eid, title, modifiedをindex.ctp(View)にセットする
        $this->set('uid', $uid);
        $this->set('ridArray', $ridArray);
        $this->set('eidArray', $eidArray);
        $this->set('titleArray', $titleArray);
        //$this->set('modArray', $modArray);
        $this->set('newModArray', $newModArray);
    }
}
?>
