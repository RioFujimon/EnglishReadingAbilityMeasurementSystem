<?php
// view 変数を確保
$eset = $this->viewVars['Erams.Eset'];
$section = $this->viewVars['Erams.Section'];
$question = $this->viewVars['Erams.Question'];
$choices = $this->viewVars['Erams.Choices'];

// ラベルの表示
echo '<div class="label">Section '.$section->subseq.' : Question No. '.
    $question->subseq.' </div>'."\n";
echo '<dic class="spacer"></div>'."\n";

// 注意書き
echo $this->element("Info", [ "info" =>
"学生側の画面ではこのように問題が表示されます。\n".
"（この注意書き自身は表示されません）"
]);

// 問題文を表示
echo '<div class="question">'."\n";
echo '<p class="sentence">'.$question->text.'</p>'."\n";

// 選択肢を表示
$count=1;
echo '<p class="choice">'."\n";
foreach ( $choices as $choice ) {
    echo '&nbsp;('.$count.') <input type="radio" name="dummy_answer" value="'.$count.'"'.
        ($choice->correct == 1 ? ' checked' : '').'>&nbsp;'.$choice->text.'<br>'."\n";
    $count++;
}
echo '</p>'."\n";

// 表示を終了
echo '</div>'."\n";

// 戻るためのフォームを開始する
echo '<div class="context" align="center">'."\n";
echo $this->Form->create(null, [
    "name" => "testing",
    "type" => "get",
    "url" => [ "controller" => "Question", "action" => "index" ]
])."\n";

// 次へボタン
echo $this->Form->button('問題の編集に戻る')."\n";
// 隠し情報
echo $this->Form->hidden('eid', [ 'value' => $eset->id ] )."\n";
echo $this->Form->hidden('sid', [ 'value' => $section->id ])."\n";
echo $this->Form->hidden('qid', [ 'value' => $question->id ])."\n";

// フォームを閉じる
echo $this->Form->end()."\n";
echo '</div>'."\n";
?>
