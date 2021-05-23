<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Datasource\ConnectionManager;

class TestResultController extends AppUserController {
    public function initialize(){
        // 親クラスをロードする
        parent::initialize();
        // ページのタイトルセット
        $this->Erams->setTitle('テスト結果閲覧ページ');
    }
    
    function index() {
        //クエリーパラメータ(uidの値)を取得する
        $uid = $this->request->getQuery('uid');
        //クエリーパラメータ(eidの値)を取得する
        $eid = $this->request->getQuery('eid');

        $connection = ConnectionManager::get('default');
        
        //テーブルesetsからid=〇〇のtitleを取得する
        $result = $connection
                   ->execute('select title from esets where id ='.$eid)
                   ->fetchAll('assoc');
        $title = $result[0]['title'];

        //テーブルusersからid=〇〇のunameを取得する
        $result = $connection
                   ->execute('select uname from users where id ='.$uid)
                   ->fetchAll('assoc');
        $uname = $result[0]['uname'];
        $uname = str_replace('_2_', '', $uname);


        //テーブルusersからid=〇〇のgidを取得する
        $result = $connection
                   ->execute('select gid from users where id ='.$uid)
                   ->fetchAll('assoc');
        $gid = $result[0]['gid'];


        //テーブルgroupsからid=〇〇のgnameを取得する
        $result = $connection
                   ->execute('select gname from groups where id ='.$gid)
                   ->fetchAll('assoc');
        $gname = $result[0]['gname'];
        

        //rsetsテーブルのidを格納する配列idArrayを宣言
        $idArray = Array();
        
        //テーブルrsetsからeid = 〇〇 かつ uid = 〇〇のidを取得する
        $result = $connection
                ->execute('select id from rsets where eid ='.$eid.'and uid ='.$uid)
                ->fetchAll('assoc');

        //rsetsのid(resultに入っている値)を$idArrayに格納する
        for($i = 0; $i < count($result); $i++){
            //$idArrayにresult[$i]['id']の値を格納する
            array_push($idArray, $result[$i]['id']);
        }

        //rresultsテーブルのstarttimeを格納するrstimeArrayを宣言
        $rstimeArray = Array();

        //テスト実施日を格納するimpDateArrayを宣言
        $impDateArray = Array();
        
        //テーブルrresultsからrid = 〇〇のstarttimeを取得
        for($i = 0; $i < count($idArray); $i++){
             $result = $connection
                ->execute('select starttime from rresults where rid ='.$idArray[$i])
                ->fetchAll('assoc');

             //読解開始時間のフォーマットを「H:i:s」に変更
             $rstime = date("H:i:s", strtotime($result[0]['starttime']));

             //$rstimeArrayに$result[$i]['starttime']を格納
             array_push($rstimeArray, $rstime);
             
             //実施日を取得
             $impDate = date("Y/m/d", strtotime($result[0]['starttime']));
             //実施日を配列impDateArrayに格納
             array_push($impDateArray, $impDate);
        }

        //rresultsテーブルのendtimeを格納するretimeArrayを宣言
        $retimeArray = Array();
        
        //テーブルrresultsからrid = 〇〇のendtimeを取得
        for($i = 0; $i < count($idArray); $i++){
             $result = $connection
                ->execute('select endtime from rresults where rid ='.$idArray[$i])
                ->fetchAll('assoc');

             //読解終了時間のフォーマットを「H:i:s」に変更
             $retime = date("H:i:s", strtotime($result[0]['endtime']));

             //$rstimeArrayに$result[$i]['endtime']を格納
             array_push($retimeArray, $retime);
        }

        //rresultsテーブルのreadtimeを格納するrrtimeArrayを宣言
        $rrtimeArray = Array();
        
        //テーブルrresultsからrid = 〇〇のreadtimeを取得
        for($i = 0; $i < count($idArray); $i++){
             $result = $connection
                ->execute('select readtime from rresults where rid ='.$idArray[$i])
                ->fetchAll('assoc');

             //$rstimeArrayに$result[$i]['starttime']を格納
             array_push($rrtimeArray, $result[0]['readtime']);
        }


        //sectionsテーブルのtlimitを格納するtlimitArrayを宣言
        $tlimitArray = Array();
        
        //テーブルsectionsからeid = 〇〇のtlimitを取得
        for($i = 0; $i < count($idArray); $i++){
            $result = $connection
               ->execute('select tlimit from sections where eid ='.$eid)
               ->fetchAll('assoc');

            //$tlimitArrayに$result[$i]['tlimit']を格納
            array_push($tlimitArray, $result[0]['tlimit']);
        }


        //sectionsテーブルのsubseqを格納するssubseqArrayを宣言
        $ssubseqArray = Array();
        
        //テーブルsectionsからeid = 〇〇のsubseqを取得
        for($i = 0; $i < count($idArray); $i++){
            $result = $connection
           ->execute('select subseq from sections where eid ='.$eid)
           ->fetchAll('assoc');
            
            //$ssubseqArrayに$result[$i]['subseq']を格納
            array_push($ssubseqArray, $result[0]['subseq']);
        }
        


        //rresultsテーブルのsidを格納する配列sidArrayを宣言
        $sidArray = Array();
        
        //テーブルrresultsからrid = 〇〇のsidを取得する
        for($i = 0; $i < count($idArray); $i++){
            $result = $connection
                ->execute('select sid from rresults where rid ='.$idArray[$i])
                ->fetchAll('assoc');

            array_push($sidArray, $result[0]['sid']);
        }


        //questionsテーブルのsubseqを格納する配列qsubseqArrayを宣言
        $qsubseqArray = Array();
        
        //テーブルquestionsからsid = 〇〇のsubseqを取得する
        for($i = 0; $i < count($sidArray); $i++){
            $result = $connection
                ->execute('select subseq from questions where sid ='.$sidArray[$i])
                ->fetchAll('assoc');

            array_push($qsubseqArray, $result[0]['subseq']);
        }


        //aresultsテーブルのanswerを格納する配列ansArrayを宣言
        $ansArray = Array();
        
        //テーブルaresultsからrid = 〇〇のanswerを取得する
        for($i = 0; $i < count($idArray); $i++){
            $result = $connection
                ->execute('select answer from aresults where rid ='.$idArray[$i])
                ->fetchAll('assoc');

            array_push($ansArray, $result[0]['answer']);
        }


        //aresultsテーブルのcorrectを格納する配列correctArrayを宣言
        $correctArray = Array();
        
        //テーブルaresultsからrid = 〇〇のcorrectを取得する
        for($i = 0; $i < count($idArray); $i++){
            $result = $connection
                ->execute('select correct from aresults where rid ='.$idArray[$i])
                ->fetchAll('assoc');

            array_push($correctArray, $result[0]['correct']);
        }


        //aresultsテーブルのiscorrectを格納する配列iscorrectArrayを宣言
        $iscorrectArray = Array();
        
        //テーブルaresultsからrid = 〇〇のcorrectを取得する
        for($i = 0; $i < count($idArray); $i++){
            $result = $connection
                ->execute('select iscorrect from aresults where rid ='.$idArray[$i])
                ->fetchAll('assoc');

            array_push($iscorrectArray, $result[0]['iscorrect']);
        }


        //aresultsテーブルのstarttimeを格納する配列astimeArrayを宣言
        $astimeArray = Array();
        
        //テーブルaresultsからrid = 〇〇のstarttimeを取得する
        for($i = 0; $i < count($idArray); $i++){
            $result = $connection
                ->execute('select starttime from aresults where rid ='.$idArray[$i])
                ->fetchAll('assoc');

            //解答開始時間のフォーマットを「H:i:s」に変更
            $astime = date("H:i:s", strtotime($result[0]['starttime']));

            //解答時間を配列に格納
            //array_push($astimeArray, $result[0]['starttime']);
            array_push($astimeArray, $astime);
        }


        //aresultsテーブルのendtimeを格納する配列aetimeArrayを宣言
        $aetimeArray = Array();
        
        //テーブルaresultsからrid = 〇〇のendtimeを取得する
        for($i = 0; $i < count($idArray); $i++){
            $result = $connection
                ->execute('select endtime from aresults where rid ='.$idArray[$i])
                ->fetchAll('assoc');

            //解答終了時間のフォーマットを「H:i:s」に変更
            $aetime = date("H:i:s", strtotime($result[0]['endtime']));
            
            //array_push($aetimeArray, $result[0]['endtime']);
            array_push($aetimeArray, $aetime);
        }



        //aresultsテーブルのendtimeを格納する配列aetimeArrayを宣言
        $thinkTimeArray = Array();
        
        //テーブルaresultsからrid = 〇〇のthinktimeを取得する
        for($i = 0; $i < count($idArray); $i++){
            $result = $connection
                ->execute('select thinktime from aresults where rid ='.$idArray[$i])
                ->fetchAll('assoc');

            array_push($thinkTimeArray, $result[0]['thinktime']);
        }
        
        //titleの値を表示する（test用）
        /*print_r($title);
        echo "</br>";*/

        //unameの値を表示する（test用）
        /*print_r($uname);
          echo "</br>";*/

        //SQLで取得したresetsのidを表示する（test用）
        /*for($i = 0; $i < count($idArray); $i++){
            print_r('rsetsのid：'.$idArray[$i]);
            echo "</br>";
        }*/

        //実施日を表示する
        /*for($i = 0; $i < count($impDateArray); $i++){
            print_r('実施日：'.$impDateArray[$i]);
            echo "</br>";
        }*/
        
        //SQLで取得したrresultsのstarttimeを表示する（test用）
        /*for($i = 0; $i < count($rstimeArray); $i++){
            print_r('rresultsのstarttime：：'.$rstimeArray[$i]);
            echo "</br>";
        }*/

        //SQLで取得したrresultsのendtimeを表示する（test用）
        /*for($i = 0; $i < count($retimeArray); $i++){
            print_r('rresultsのendtime：：'.$retimeArray[$i]);
            echo "</br>";
        }*/

        //SQLで取得したrresultsのreadtimeを表示する（test用）
        /*for($i = 0; $i < count($rrtimeArray); $i++){
            print_r('rresultsのreadtime：：'.$rrtimeArray[$i]);
            echo "</br>";
        }*/

        //SQLで取得したsectionsテーブルのtlimitを表示する(test用)
        /*print_r($tlimitArray);
        echo "</br>";*/

        //SQLで取得したsectionsテーブルのsubseqを表示する(test用)
        /*print_r($ssubseqArray);
        echo "</br>";*/

        //SQLで取得したrresultsテーブルのsidを表示する（test用）
        /*for($i = 0; $i < count($sidArray); $i++){
            print_r($sidArray[$i]);
            echo "</br>";
        }*/


        //SQLで取得したquestionsテーブルのsubseqを表示する
        /*for($i = 0; $i < count($qsubseqArray); $i++){
            print_r($qsubseqArray[$i]);
            echo "</br>";
        }*/

        
        //SQLで取得したaresultsテーブルのanswerを表示する
        /*for($i = 0; $i < count($ansArray); $i++){
            print_r($ansArray[$i]);
            echo "</br>";
        }*/


        //SQLで取得したaresultsテーブルのcorrectを表示する
        /*for($i = 0; $i < count($correctArray); $i++){
            print_r($correctArray[$i]);
            echo "</br>";
        }*/


        //SQLで取得したaresultsテーブルのiscorrectを表示する
        /*for($i = 0; $i < count($iscorrectArray); $i++){
            print_r($iscorrectArray[$i]);
            echo "</br>";
        }*/


        //SQLで取得したaresultsテーブルのstarttimeを表示する
        /*for($i = 0; $i < count($astimeArray); $i++){
            print_r($astimeArray[$i]);
            echo "</br>";
        }*/


        //SQLで取得したaresultsテーブルのendtimeを表示する
        /*for($i = 0; $i < count($aetimeArray); $i++){
            print_r($aetimeArray[$i]);
            echo "</br>";
        }*/


        //SQLで取得したaresultsテーブルのthinktimeを表示する
        /*for($i = 0; $i < count($thinkTimeArray); $i++){
            print_r($thinkTimeArray[$i]);
            echo "</br>";
        }*/

        
        
        //$uidと$eidの値をViewにセットする
        $this->set('idArray', $idArray);
        $this->set('gname', $gname);
        $this->set('uid', $uid);
        $this->set('eid', $eid);
        $this->set('title', $title);
        $this->set('uname', $uname);
        $this->set('rstimeArray', $rstimeArray);
        $this->set('retimeArray', $retimeArray);
        $this->set('rrtimeArray', $rrtimeArray);
        $this->set('impDateArray', $impDateArray);
        $this->set('sidArray', $sidArray);
        $this->set('ssubseqArray', $ssubseqArray);
        $this->set('tlimitArray', $tlimitArray);
        $this->set('qsubseqArray', $qsubseqArray);
        $this->set('ansArray', $ansArray);
        $this->set('correctArray', $correctArray);
        $this->set('iscorrectArray', $iscorrectArray);
        $this->set('astimeArray', $astimeArray);
        $this->set('aetimeArray', $aetimeArray);
        $this->set('thinkTimeArray', $thinkTimeArray);
    }
}
?>
