<?php
        // $titleのこれまでのテスト結果を表示する
        echo '<div class="label">'.$uname.'さんの'.$title.'のこれまでの結果一覧</div>'."\n";
	echo '<div class="spacer"></div>'."\n";
	echo "</br>";


	echo '<div class="stat">'."\n";
    	echo '<table>'."\n";
    	echo '<tr>'."\n";
    	echo '<th>グループ名</th>'."\n";
    	echo '<th>ユーザ名</th>'."\n";
        echo '<th>セクションNo.</th>'."\n";
        echo '<th>実施日</th>'."\n";
        echo '<th>開始時間</th>'."\n";
        echo '<th>終了時間</th>'."\n";
        echo '<th>読解時間</th>'."\n";
        echo '<th>制限時間</th>'."\n";
        echo '<th>問題No.</th>'."\n";
        echo '<th>解答</th>'."\n";
        echo '<th>正答</th>'."\n";
        echo '<th>正誤</th>'."\n";
        echo '<th>開始時間</th>'."\n";
        echo '<th>終了時間</th>'."\n";
        echo '<th>思考時間</th>'."\n";
    	echo '</tr>'."\n";
	for($i = 0; $i < count($idArray); $i++){
	       echo '<tr>'."\n";
	       echo '<td>'.$gname.'</td>';
	       echo '<td>'.$uname.'</td>';
	       echo '<td>'.$ssubseqArray[$i].'</td>';
	       echo '<td>'.$impDateArray[$i].'</td>';
	       echo '<td>'.$rstimeArray[$i].'</td>';
	       echo '<td>'.$retimeArray[$i].'</td>';
	       echo '<td>'.$rrtimeArray[$i].'秒</td>';
	       echo '<td>'.$tlimitArray[$i].'秒</td>';
	       echo '<td>'.$qsubseqArray[$i].'</td>';
	       echo '<td>'.$ansArray[$i].'</td>';
	       echo '<td>'.$correctArray[$i].'</td>';
	       if($iscorrectArray[$i] == 1){
	       echo '<td>○</t>';
	       } else {
	       echo '<td>×</t>';
	       }
	       echo '<td>'.$astimeArray[$i].'</td>';
	       echo '<td>'.$aetimeArray[$i].'</td>';
	       echo '<td>'.$thinkTimeArray[$i].'秒</td>';
	       echo '</tr>'."\n";
	}
	echo '</table>'."\n";


	//User Topページへ戻るボタンの表示
	echo '<div class="context">'."\n";
	echo $this->Form->create("null", [
    	     "type" => "post",
    	     "url" => [ "controller" => "UserTop" ]
	])."\n";
	echo $this->Form->button('UserTopページへ戻る')."\n";
	echo $this->Form->end()."\n";
	echo '</div>'."\n";
?>