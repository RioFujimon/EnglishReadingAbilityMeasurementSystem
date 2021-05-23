<?php
// view 変数を確保
$section = $this->viewVars['Erams.Test.Question'];
$choices = $this->viewVars['Erams.Test.Choices'];
$stat = $this->viewVars['Erams.Test.Stat'];

// 基本的な情報を構築
$eid = $stat['info']['eid'];
$r_seq = $stat['info']['r_seq'];
$a_seq = $stat['info']['a_seq'];
$sid = $stat['sids'][$r_seq];
if ( $a_seq != -1 ) {
    $qid = $stat['qids'][$r_seq][$a_seq];
} else {
    $qid = "unset";
}
$a_res = $stat['a_res'];

// 戻るボタン禁止の JavaScript
echo '<script language="JavaScript">EramsForbidden();</script>'."\n"; 

// ラベルの表示
echo '<div class="label">Section '.($stat['info']['r_seq'] + 1).
    ' : Question No.'.($stat['info']['a_seq'] + 1).'</div>'."\n";
    echo '<dic class="spacer"></div>'."\n";

// フォームを開始する
echo $this->Form->create("null", [
    "name" => "testing",
    "type" => "post",
    "onsubmit" => 'return EramsAtEndOfAsking()',
    "url" => [ "controller" => "Test", "action" => "askend" ]
])."\n";

// 問題文を表示
echo '<div class="question">'."\n";
echo '<p class="sentence">'.$section->text.'</p>'."\n";

// 選択肢を表示
$count=1;
echo '<p class="choice">'."\n";
foreach ( $choices as $choice ) {
    echo '&nbsp;('.$count.') <input type="radio" name="answer" value="'.$count.'">&nbsp;'.$choice->text.'<br>'."\n";
    $count++;
}
echo '</p>'."\n";

// 隠し情報
echo $this->Form->hidden('stime', [ 'id' => 'stime', 'value' => $a_res[$r_seq][$a_seq]['start'] ])."\n";
echo $this->Form->hidden('etime', [ 'id' => 'etime', 'value' => '' ])."\n";

// 次へボタン
echo '<div class="center">'."\n";
echo $this->Form->button('解答を確定')."\n";
echo '</div>'."\n";

// 問題文表示の終了
echo '</div>'."\n";

// フォームを閉じる
echo $this->Form->end()."\n";

// このページの読み込みが終わった際の処理
if ( empty($a_res[$r_seq][$a_seq]['start']) ) {
    echo '<script language="JavaScript">window.onload = EramsOnLoad;</script>'."\n";
}
?>
