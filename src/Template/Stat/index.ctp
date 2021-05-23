<?php
// 集計結果を表示する

// ViewVars の確保
$eset = $this->viewVars['Erams.Eset'];
$sections = $this->viewVars['Erams.Secsions'];
$questions = $this->viewVars['Erams.Questions'];
$choices = $this->viewVars['Erams.Choices'];
$groups = $this->viewVars['Erams.Groups'];
$users =  $this->viewVars['Erams.Users'];
$rsets = $this->viewVars['Erams.Rsets'];
$rresults = $this->viewVars['Erams.Rresults'];
$aresults = $this->viewVars['Erams.Aresults'];

// 1。テストセットの情報を表示する

// ラベルの表示
echo '<div class="label">テストセット '.$eset->id.' 情報</div>'."\n";
echo '<div class="spacer"></div>'."\n";

echo '<p>テストセット</p>'."\n";
echo '<div class="reader">タイトル：'.$eset->title.'</div>'."\n";
echo '<div class="reader">プロパティ：'.$eset->property.'</div>'."\n";
if( $eset->mode == 1) {
    $mode = 'テスト中';
}
else{
    $mode = '編集可能';
}
echo '<div class="reader">モード：'.$mode.'</div>'."\n";

for( $c = 0; $c < count($sections); $c++ ){
    echo '<p>セクション'.($c+1).'</p>'."\n";
    echo '<div class="reader">タイトル：'.$sections[$c]->title.'</div>'."\n";
    echo '<div class="reader">本文：'.$sections[$c]->text.'</div>'."\n";
    echo '<div class="reader">制限時間：'.$sections[$c]->tlimit.'秒</div>'."\n";
    echo '<div class="reader">プロパティ：'.$sections[$c]->property.'</div>'."\n";
    
    for( $d = 0; $d < count($questions[$c]); $d++ ) {
        echo '<div class="question">'."\n";
        echo '<p>問題'.($d+1).'</p>'."\n";
        echo '<p class="sentence">問題文：'.$questions[$c][$d]->text.'</p>'."\n";

        echo '<p class="choice">'."\n";
        for( $e = 0; $e < count($choices[$c][$d]); $e++ ) {
            if($choices[$c][$d][$e]->correct == 1){
                $correct = '○';
            }
            else {
                $correct = '×';
            }   
            echo $correct.' 選択肢'.($e+1).' ： '.$choices[$c][$d][$e]->text.'<br>'."\n";
        }
        echo '</p>'."\n";
        echo '</div>'."\n";
    }
}
// 2。集計結果を表示する

// ラベルの表示
echo '<div class="spacer"></div>'."\n";
echo '<div class="label">個人結果</div>'."\n";
echo '<div class="spacer"></div>'."\n";

// テスト結果がなければ
if( count($rresults) == 0 ) {
    echo '<div class="context" align="center">このテストセットの受験結果はまだありません。</div>'."\n";
}
// テスト結果があれば
else {
    // 集計結果を表示する
    echo '<p>受験者数　'.count($rsets).'人</p>';
    
    
    echo '<div class="stat">'."\n";
    echo '<table>'."\n";
    echo '<tr>'."\n";
    echo '<th>グループ名</th>'."\n";
    echo '<th>ユーザ名</th>'."\n";
    //echo '<th>実施日</th>'."\n";
    
    //  セクションの数だけヘッダーを設定する
    for( $c = 0; $c < count($sections); $c++ ) {
        echo '<th>セクションNo.</th>'."\n";
        echo '<th>実施日</th>'."\n";
        echo '<th>開始時間</th>'."\n";
        echo '<th>終了時間</th>'."\n";
        echo '<th>読解時間</th>'."\n";
        echo '<th>制限時間</th>'."\n";
        
        // 問題の数だけヘッダーを設定する
        for( $d = 0; $d < count($questions[$c]); $d++ ) {
            echo '<th>問題No.</th>'."\n";
            echo '<th>解答</th>'."\n";
            echo '<th>正答</th>'."\n";
            echo '<th>正誤</th>'."\n";
            echo '<th>開始時間</th>'."\n";
            echo '<th>終了時間</th>'."\n";
            echo '<th>思考時間</th>'."\n";
        }
    }
    echo '</tr>'."\n";
    
    echo '<tr>'."\n";

    // 人数分の結果を表示する
    for( $a = 0; $a < count($rsets); $a++ ) {
        echo '<tr>'."\n";
        // グループ名
        for( $d = 0; $d < count($groups); $d++ ) {
            if( $groups[$d]->id == $users[$a]->gid ) {
                echo '<td>'.$groups[$d]->gname.'</td>'."\n";
                $deletes = '/\_'.$groups[$d]->id.'\_/';
            }
        }
        // ユーザ名
        echo '<td>'.preg_replace($deletes, '', $users[$a]->uname).'</td>'."\n";
        //echo '<td>'.$rsets[$a]->created->i18nFormat("YYYY/MM/dd").'</td>'."\n";
        for( $c = 0; $c < count($sections); $c++ ) {    
            // 読解時間を表示する
            for( $d = 0; $d < count($rresults[$c]); $d++ ) {
                if( $rresults[$c][$d]->rid == $rsets[$a]->id ) {
                    // セクションNo.
                    echo '<td>'.$sections[$c]->subseq.'</td>'."\n";
                    // 実施日
                    echo '<td>'.$rresults[$c][$d]->starttime->i18nFormat("YYYY/MM/dd").'</td>'."\n";
                    // 開始時間
                    echo '<td>'.$rresults[$c][$d]->starttime->i18nFormat("HH:mm:ss").'</td>'."\n";
                    // 終了時間
                    echo '<td>'.$rresults[$c][$d]->endtime->i18nFormat("HH:mm:ss").'</td>'."\n";
                    // 読解時間
                    echo '<td>'.$rresults[$c][$d]->readtime.'秒</td>'."\n";
                    // 制限時間
                    echo '<td>'.$sections[$c]->tlimit.'秒</td>'."\n";
                    // 解答結果を表示する
                    for( $e = 0; $e < count($questions[$c]); $e++ ) {
                        for( $f = 0; $f < count($aresults[$c][$e]); $f++ ) {
                            // 読解結果と解答結果の結果idが一致するなら表示する
                            if( $rresults[$c][$d]->rid == $aresults[$c][$e][$f]->rid ) {
                                // 問題No.
                                echo '<td>'.$questions[$c][$e]->subseq.'</td>'."\n";
                                // 解答
                                echo '<td>'.$aresults[$c][$e][$f]->answer.'</td>'."\n";
                                // 正答
                                echo '<td>'.$aresults[$c][$e][$f]->correct.'</td>'."\n";
                                // 正誤
                                if( $aresults[$c][$e][$f]->iscorrect == 1 ) {
                                    $iscorrect = '○';
                                }
                                else {
                                    $iscorrect = '×';
                                }
                                echo '<td>'.$iscorrect.'</td>'."\n";
                                // 開始時間
                                echo '<td>'.$aresults[$c][$e][$f]->starttime->i18nFormat("HH:mm:ss").'</td>'."\n";
                                // 終了時間
                                echo '<td>'.$aresults[$c][$e][$f]->endtime->i18nFormat("HH:mm:ss").'</td>'."\n";
                                // 思考時間
                                echo '<td>'.$aresults[$c][$e][$f]->thinktime.'秒</td>'."\n";
                            }
                        }
                    }
                }
            }
        }
        echo '</tr>'."\n";
    }
    echo '</table>'."\n";
    echo '</div>'."\n";
}


// 3。統計結果とテストセットの情報をダウンロードする
echo '<div class="spacer"></div>'."\n";
echo '<div class="spacer"></div>'."\n";

echo '<div>'."\n";
echo $this->Form->create(null, [
    "type" => "post",
    "url" => [ "countroller" => "Stat", "action" => "sdownload" ]
])."\n";
echo $this->Form->hidden('eid', [ 'value' => $eset->id ]);
echo $this->Form->button('統計結果をダウンロード')."\n";
echo $this->Form->end()."\n";
echo '</div>'."\n";

echo '<div>'."\n";
echo $this->Form->create(null, [
    "type" => "post",
    "url" => [ "countroller" => "Stat", "action" => "edownload" ]
])."\n";
echo $this->Form->hidden('eid', [ 'value' => $eset->id ]);
echo $this->Form->button('テストセットの情報をダウンロード')."\n";
echo $this->Form->end()."\n";
echo '</div>'."\n";

?>