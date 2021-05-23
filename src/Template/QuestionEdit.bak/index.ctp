<?php

// コントローラでセットされたセクションを取得
$set_mode = $this->viewVars['EramsEsetMode'];
$ques_info = $this->viewVars['EramsQuesInfo'];
$choice_info = $this->viewVars['EramsChoiceInfo'];

// 編集中なら説明を表示する
if( strcmp($set_mode, 'E') == 0 ) {
    echo $this->element("Info", [ "info" =>
    '問題文と選択肢の登録ができます。'."\n".
    '正答となる選択肢は、ラジオボタンで選んでください。'."\n".
    'また、選択肢を追加するには、一度保存をしてください。'."\n".
    '問題文が入力されていない場合、保存することができません。']);
}

// 指定された問題の情報を表示する
// フォームの開始
echo $this->Form->create("null", [
    "type" => "post",
    "url" => [
        "controller" => "QuestionEdit",
        "action" => "saveQues" ],
]);
echo '<div class="eset">'."\n";
echo '<div class="item1">問題'.$ques_info['subseq'].'</div>'."\n";
echo '<div class="item2">&nbsp;</div>'."\n";
echo '<div class="item1">問題文</div>'."\n";
echo '<div class="item2">'."\n";
echo $this->Form->textarea('qtext', [
    'label' => false, 'default' => $ques_info['text'] ] );
echo '</div>'."\n";
echo '</div>'."\n";

// 選択肢があれば表示
echo '<table>'."\n";

if( count($choice_info) != 0 ){
    foreach ( $choice_info as $choice ) {
        echo '<tr>'."\n";
        echo '<td>選択肢'.$choice['subseq'].'</td>'."\n";
        echo '<td>'."\n";
        echo '<div class="formset">'."\n";
        if( $correct == $choice['subseq'] ){
            echo $this->Form->radio('correct', [
                $choice['subseq'] => '' ], [ 'checked' => true, 'hiddenField'=>false ]);
        }
        else{
            echo $this->Form->radio('correct', [ $choice['subseq'] => '' ], [ 'hiddenField'=>false ]);
        }
        echo $this->Form->text('ctext[]', [
            'label' => false, 'default' => $choice['text'] ])."\n";
        echo '</div>'."\n";
        echo '</td>'."\n";
        echo '</tr>'."\n";
    }
    
    echo '<tr>'."\n";
    echo '<td>(新規作成)</td>'."\n";
    echo '<td>'."\n";
    echo '<div class="formset">'."\n";
    echo $this->Form->radio('correct', [ (count($choice_info) + 1) => '' ], [ 'hiddenField'=>false ]);
    echo $this->Form->text('ctext[]', [
        'label' => false, 'default' => '' ])."\n";
    echo '</div>'."\n";
    echo '</td>'."\n";
    echo '</tr>'."\n";
}
else {
    echo '<tr>'."\n";
    echo '<td>(新規作成)</td>'."\n";
    echo '<td>'."\n";
    echo '<div class="formset">'."\n";
    echo $this->Form->radio('correct', [ 1 => ''], ['checked' => true, 'hiddenField'=>false ]);
    echo $this->Form->text('ctext[]', [
        'label' => false, 'default' => '' ])."\n";
    echo '</div>'."\n";
    echo '</td>'."\n";
    echo '</tr>'."\n";
}

if( strcmp($set_mode, 'E') == 0 ) {
    echo '<tr>'."\n";
    echo '<td>&nbsp;</td>'."\n";
    echo '<td>'."\n";
    echo $this->Form->hidden('eid', [ 'value' => $eid ])."\n";
    echo $this->Form->hidden('sid', [ 'value' => $sid ])."\n";
    echo $this->Form->button('保存',[
        'name' => 'qid', 'value' => $qid ])."\n";
    echo '</td>'."\n";
    echo '</tr>'."\n";
}
echo $this->Form->end();
echo '</table>'."\n";

?>