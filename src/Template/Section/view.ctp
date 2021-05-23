<?php
// view 変数を確保
$eset = $this->viewVars['Erams.Eset'];
$section = $this->viewVars['Erams.Section'];

// ラベルの表示
echo '<div class="label">Section '.$section->subseq.' : '.$section->title.'</div>'."\n";
echo '<dic class="spacer"></div>'."\n";

// 注意書き
echo $this->element("Info", [ "info" =>
"学生側の画面ではこのように本文（英文）が表示されます。\n".
"（この注意書き自身は表示されません）"
]);

// 本文を表示
echo '<div class="reader">'.$section->text.'</div>'."\n";

// 戻るためのフォームを開始する
echo '<div class="center">'."\n";
echo $this->Form->create(null, [
    "name" => "testing",
    "type" => "get",
    "url" => [ "controller" => "Section", "action" => "index" ]
])."\n";

// 次へボタン
echo $this->Form->button('セクションの編集に戻る')."\n";

// 隠し情報
echo $this->Form->hidden('eid', [ 'value' => $eset->id ] )."\n";
echo $this->Form->hidden('sid', [ 'value' => $section->id ])."\n";

// フォームを閉じる
echo $this->Form->end()."\n";
echo '</div>'."\n";
?>
