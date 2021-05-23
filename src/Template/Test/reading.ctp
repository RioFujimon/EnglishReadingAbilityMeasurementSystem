<?php
// view 変数を確保
$section = $this->viewVars['Erams.Test.Section'];
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
$r_res = $stat['r_res'];

// 戻るボタン禁止の JavaScript
echo '<script language="JavaScript">EramsForbidden();</script>'."\n";

// ラベルの表示
echo '<div class="label">Section '.($r_seq + 1).' : Reading Phase</div>'."\n";
echo '<dic class="spacer"></div>'."\n";

// 本文を表示
echo '<div class="reader">'.$section->text.'</div>'."\n";

// フォームを開始する
echo '<div class="center">'."\n";
echo $this->Form->create(null, [
    "name" => "testing",
    "type" => "post",
    "onsubmit" => 'EramsEndTime()',
    "url" => [ "controller" => "Test", "action" => "readend" ]
])."\n";

// 次へボタン
echo $this->Form->button('問題解答に進む', [ 'id' => 'next' ] )."\n";

// 隠し情報
echo $this->Form->hidden('stime', [ 'id' => 'stime', 'value' => $r_res[$r_seq]['start'] ])."\n";
echo $this->Form->hidden('etime', [ 'id' => 'etime', 'value' => '' ])."\n";

// フォームを閉じる
echo $this->Form->end()."\n";
echo '</div>'."\n";

// このページの読み込みが終わった際の処理
if ( empty($r_res[$r_seq]['start']) ) {
    echo '<script language="JavaScript">window.onload = EramsOnLoad;</script>'."\n";
}

// 時間切れの処理を行うスクリプトを設定
echo "<script>setTimeout('EramsTimeout()', ".($section->tlimit*1000).")</script>"."<br>\n";
?>
