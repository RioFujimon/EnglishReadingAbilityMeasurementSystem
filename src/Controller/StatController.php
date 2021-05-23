<?php
// 集計結果ページ
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\ORM\Table;

class StatController extends AppAdminController{

    private $eset;
    private $sections;
    private $questions;
    private $choices;
    private $groups;
    private $users;
    private $rsets;
    private $rresults;
    private $aresults;
    
    public function initialize(){
        // 親クラスをロードする
        parent::initialize();
        // ページのタイトルセット
        $this->Erams->setTitle('集計結果ページ');

        // セットのデータを確保
        if ( ! ($this->eset = $this->EramsData->getEsetByForm('/StatTop')) ) {
            return;
        }
        $eid = $this->eset->id;
        
        // セクションのデータを確保
        $sections = $this->EramsDB->get('sections', 'subseq', [ 'eid' => $eid ]);
        // セクションのデータを配列に格納
        foreach ( $sections as $sec ) {
            $this->sections[] = $sec;
        }
        
        // 結果セットのデータを確保
        $rsets = $this->EramsDB->get('rsets', 'id', [ 'eid' => $eid ]);
        // 結果セットのデータを配列に格納
        foreach ( $rsets as $rset ) {
            if( $rset->valid == 1 ){
                $this->rsets[] = $rset;
            }
        }
        
        // セクションに付随する問題のデータを確保
        for( $c = 0; $c < count($this->sections); $c++ ) {
            $questions = $this->EramsDB->get('questions', 'subseq', [ 'sid' => $this->sections[$c]->id ]);
            // 問題のデータを配列に格納する
            foreach ( $questions as $ques ) {
                $this->questions[$c][] = $ques;
            }
        }

        // 問題の選択肢のデータを確保
        for( $c = 0; $c < count($this->sections); $c++ ) {
            for( $d = 0; $d < count($this->questions[$c]); $d++ ) {
                $choices = $this->EramsDB->get('choices', 'subseq', [ 'qid' => $this->questions[$c][$d]->id ]);
                // 選択肢のデータを配列に格納する
                foreach ( $choices as $choice ) {
                    $this->choices[$c][$d][] = $choice;
                }
            }
        }
        
        // post 変数を取得
        //$date_start = h(trim($this->request->getData('date_start')));
        //$date_end = h(trim($this->request->getData('date_end')));

        // グループのデータを確保
        $groups = $this->EramsDB->get('groups', null);
        // 結果セットのデータを配列に格納
        foreach ( $groups as $group ) {
                $this->groups[] = $group;
        }
        
        // ユーザのデータを確保
        for( $c = 0; $c < count($this->rsets); $c++ ) {
            $user = $this->EramsDB->get('users', null, [ 'id' => $this->rsets[$c]->uid ]);
            // ユーザのデータを配列に格納する
            $this->users[] = $user->first();
        }

        // 読解時間・解答結果のデータを確保
        for( $c = 0; $c < count($this->sections); $c++ ) {
            $rresults = $this->EramsDB->get('rresults', null, [ 'sid' => $this->sections[$c]->id ]);
            // 読解時間のデータを配列に格納する
            foreach ( $rresults as $rres ) {
                $this->rresults[$c][] = $rres;
            }

            for( $d = 0; $d < count($this->questions[$c]); $d++ ) {
                $aresults = $this->EramsDB->get('aresults', null, [ 'qid' => $this->questions[$c][$d]->id ]);
                // 解答結果のデータを配列に格納する
                foreach ( $aresults as $ares ) {
                    $this->aresults[$c][$d][] = $ares;                    
                }
            }
        }
        
        $this->set('Erams.Eset', $this->eset);
        $this->set('Erams.Secsions', $this->sections);
        $this->set('Erams.Questions', $this->questions);
        $this->set('Erams.Choices', $this->choices);
        $this->set('Erams.Groups', $this->groups);
        $this->set('Erams.Users', $this->users);
        $this->set('Erams.Rsets', $this->rsets);
        $this->set('Erams.Rresults', $this->rresults);
        $this->set('Erams.Aresults', $this->aresults);
        $this->Erams->setBackPage('/StatTop');
    }

    public function index() {
        // セッションを確保
        $session = $this->request->getSession();
        // ログイン名を取得
        $tname = $session->read('Erams.uname');
        // ログ出力
        $this->log( $tname.' Stat - index  eid: '.$this->eset->id, 'info');
    }
    
    public function sdownload() {
        // セッションを確保
        $session = $this->request->getSession();
        // ログイン名を取得
        $tname = $session->read('Erams.uname');
        // ログ出力
        $this->log( $tname.' Stat - sdownload  eid: '.$this->eset->id, 'info');
        
        // ファイルパスを指定する
        $fp = fopen('php://output', 'w');
        // ファイル名を指定する
        $filename = rawurlencode('テストセット'.$this->eset->id.'集計結果.csv');
        // 文字コードを指定する
        $this->response = $this->response->withCharset('UTF8');
        stream_filter_append($fp, 'convert.iconv.UTF-8/SJIS', STREAM_FILTER_WRITE);
        // ファイルの拡張子を指定する
        $download = $this->response->withType('csv');
        // ファイル名を指定する
        //$download = $download->withDownload(date('Y.m.d_H:i:s').'「'.$this->eset->title.'」.csv');
        $download = $download->withDownload($filename);
        $strNum = array("テストセット番号", $this->eset->id);
        fputcsv($fp, $strNum);
        $strTitle = array("テストセットタイトル", $this->eset->title);
        fputcsv($fp, $strTitle);
        $strPerson = array("受験者数", count($this->rsets), "人");
        fputcsv($fp, $strPerson);
        $space[] = "";
        fputcsv($fp, $space);
        
        // ヘッダを書き込む
        $resultHeader = array();
        $resultHeader[] = "グループ名";
        $resultHeader[] = "ユーザ名";
        for( $c = 0; $c < count($this->sections); $c++ ) {
            $resultHeader[] = "セクションNo.";
            $resultHeader[] = "実施日";
            $resultHeader[] = "開始時間";
            $resultHeader[] = "終了時間";
            $resultHeader[] = "読解時間(s)";
            for( $d = 0; $d < count($this->questions[$c]); $d++ ) {
                $resultHeader[] = "問題No.";
                $resultHeader[] = "解答";
                $resultHeader[] = "正答";
                $resultHeader[] = "正誤";
                $resultHeader[] = "開始時間";
                $resultHeader[] = "終了時間";
                $resultHeader[] = "思考時間(s)";
            }
        }
        fputcsv($fp, $resultHeader);

        // 人数分の結果を表示する
        for( $c = 0; $c < count($this->rsets); $c++ ) {
            $results = array();
            // グループ名
            for( $d = 0; $d < count($this->groups); $d++ ){
                if($this->groups[$d]->id == $this->users[$c]->gid){
                    $results[] = $this->groups[$d]->gname;
                    $deletes = '/\_'.$this->groups[$d]->id.'\_/';
                }
            }
            // ユーザ名
            $results[] = preg_replace($deletes, '', $this->users[$c]->uname);
            // セクション数だけ繰り返す
            for( $d = 0; $d < count($this->sections); $d++ ) {
                for( $e = 0; $e < count($this->rresults[$d]); $e++ ) {
                    // rsets の id と rresults の rid が一致するなら
                    if( $this->rresults[$d][$e]->rid == $this->rsets[$c]->id ) {
                        // セクションNo.
                        $results[] = $this->sections[$d]->subseq;
                        // 実施日
                        $results[] = $this->rresults[$d][$e]->starttime->i18nFormat("YYYY-MM-dd");
                        //$results[] = date("Y/m/d",strtotime($this->rresults[$d][$e]->starttime));
                        // 開始時間
                        $results[] = $this->rresults[$d][$e]->starttime->i18nFormat("HH:mm:ss");
                        // 終了時間
                        $results[] = $this->rresults[$d][$e]->endtime->i18nFormat("HH:mm:ss");
                        // 読解時間
                        $results[] = $this->rresults[$d][$e]->readtime;
                        for( $f = 0; $f < count($this->questions[$d]); $f++ ) {
                            for( $g = 0; $g < count($this->aresults[$d][$f]); $g++ ) {
                                // rresults の rid と aresults の rid が一致するなら
                                if( $this->rresults[$d][$e]->rid == $this->aresults[$d][$f][$g]->rid ) {
                                    // 問題順
                                    $results[] = $this->questions[$d][$f]->subseq;
                                    // 解答
                                    $results[] = $this->aresults[$d][$f][$g]->answer;
                                    // 正答
                                    $results[] = $this->aresults[$d][$f][$g]->correct;
                                    // 正誤
                                    if( $this->aresults[$d][$f][$g]->iscorrect == 1 ) {
                                        $iscorrect = '○';
                                    }
                                    else {
                                        $iscorrect = '×';
                                    }
                                    $results[] = $iscorrect;
                                    // 開始時間
                                    $results[] = $this->aresults[$d][$f][$g]->starttime->i18nFormat("HH:mm:ss");
                                    // 終了時間
                                    $results[] = $this->aresults[$d][$f][$g]->endtime->i18nFormat("HH:mm:ss");
                                    // 思考時間
                                    $results[] = $this->aresults[$d][$f][$g]->thinktime;
                                }
                            }
                        }
                    }
                }
            }
            // 1行ずつ書き込む
            fputcsv($fp, $results);
        }
        
        fclose($fp);
        return $download;
    }

    public function edownload(){
        // セッションを確保
        $session = $this->request->getSession();
        // ログイン名を取得
        $tname = $session->read('Erams.uname');
        // ログ出力
        $this->log( $tname.' Stat - edownload  eid: '.$this->eset->id, 'info');
        
        // ファイルパスを指定する
        $fp = fopen('php://output', 'w');
        // ファイル名を指定する
        $filename = rawurlencode('テストセット'.$this->eset->id.'情報.txt');
        // 文字コードを指定する
        $this->response = $this->response->withCharset('UTF-8');
        // ファイルの拡張子を指定する
        $download = $this->response->withType('txt');
        // ファイル名を指定する
        $download = $download->withDownload($filename);

        //echo '【テストセット】'."\n";
        echo '内部ID：'.$this->eset->id."\n";
        echo 'タイトル：'.$this->eset->title."\n";
        echo 'プロパティ：'.$this->eset->property."\n";
        echo "\n";
        
        for( $c = 0; $c < count($this->sections); $c++ ){
            echo "\n";
            echo '【セクション'.($c+1).'】'."\n";
            echo 'タイトル：'.$this->sections[$c]->title."\n";
            echo '制限時間：'.$this->sections[$c]->tlimit.'秒'."\n";
            echo '説明：'.$this->sections[$c]->property."\n";
            $text = preg_replace('/<br>/','',$this->sections[$c]->text);
            echo '本文(ここから)'."\n";
            echo $text."\n";
            echo '本文(ここまで)'."\n";
            echo "\n";
            
            for( $d = 0; $d < count($this->questions[$c]); $d++ ) {
                echo '［問題'.($d+1).'］'."\n";
                echo '問題文：'.$this->questions[$c][$d]->text."\n";
                for( $e = 0; $e < count($this->choices[$c][$d]); $e++ ) {
                    if($this->choices[$c][$d][$e]->correct == 1){
                        $correct = '○';
                    }
                    else {
                        $correct = '×';
                    }
                    echo '選択肢'.($e+1).''.$correct.'：'.$this->choices[$c][$d][$e]->text."\n";
                }
                echo "\n";
            }
            echo "\n";
        }
        
        fclose($fp);
        return $download;
    }
}

?>