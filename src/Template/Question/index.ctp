<?php
// コントローラでセットされた情報を取得
$eset = $this->viewVars['Erams.Eset'];
$section = $this->viewVars['Erams.Section'];
$question =  $this->viewVars['Erams.Question'];
$choices =  $this->viewVars['Erams.Choices'];
$question_pair =  $this->viewVars['Erams.QuestionPair'];
$question_key =  $this->viewVars['Erams.QuestionKey'];

// ラベルの表示
echo '<div class="label">問題編集フォーム</div>'."\n";
echo '<div class="spacer"></div>'."\n";

// 編集可能かどうかの確認
if ( $eset->mode == 1 ) {
    $readonly = true;
} else {
    $readonly = false;
}

// 編集中なら説明を表示する
if( ! $readonly ) {
    echo $this->element("Info", [ "info" =>
    "問題文の中に以下の文字が含まれる場合には自動的に文字を変換します。\n".
    "・「".'"'."（ダブルクオート）」-&gt; &amp;#034;\n".
    "・「'（シングルクオート）」-&gt; &amp;#039;\n"
    ]);
}

// 親のテストセットの状態を表示する
echo '<p class="'.($readonly ? 'readonly' : 'writable').'">'."\n";
echo '親のテストセットの状態（'.
    ($readonly ? "テスト提供中[編集不可]" : "編集可能").'）'.
    ' :&nbsp; '.($eset->title).'<br>'."\n";
echo '親のセクション： '.($section->title).'</p>'."\n";
echo '<div class="spacer"></div>'."\n";

// 次の問題と前のセクションの表示
echo '<div class="center">'."\n";
echo '<div class="formset">'."\n";
echo $this->Form->create(false, [
    "type" => "post",
    "url" => [ "controller" => "Question", "action" => "index"]
])."\n";
echo $this->Form->hidden('eid', [ 'value' => $eset->id ])."\n";
echo $this->Form->hidden('sid', [ 'value' => $section->id ])."\n";
echo $this->Form->button('前の問題', [
    'name' => 'qid',
    'value' => (0 < $question_pair[0] ? $question_pair[0] : $question->id)
])."\n";
echo $this->Form->button('次の問題', [
    'name' => 'qid',
    'value' => (0 < $question_pair[1] ? $question_pair[1] : $question->id)
])."\n";
echo $this->Form->end()."\n";
echo '</div>'."\n";
echo '</div>'."\n";

// 指定されたセクションの情報を表示する
// フォームを開始
echo $this->Form->create(null, [
    "name" => "mainForm",
    "type" => "post",
    "url" => [ "controller" => "Question", "action" => "save_"]
])."\n";

// 隠し情報を表示
echo $this->Form->hidden('eid', [ 'value' => $eset->id ])."\n";
echo $this->Form->hidden('sid', [ 'value' => $section->id ])."\n";
echo $this->Form->hidden('qid', [ 'value' => $question->id ])."\n";

// 問題の内容を表示
echo '<div class="test_set">'."\n";
echo '<div class="spacer"></div>'."\n";
echo '<div class="center">問題番号 '.($question->subseq).' の問題文</div>'."\n";
echo '<div class="center">'."\n";
$text = $question->text;
$text = preg_replace("/<br>/", "\n", $text);
echo $this->Form->textarea('text', [
    'label' => false, 'default' => $text,
    'readonly' => $readonly, 'rows' => 7, 'cols' => 40
]);
echo '</div>'."\n";
echo '<div class="spacer"></div>'."\n";
echo '</div>'."\n";
echo '<div class="spacer"></div>'."\n";

// 選択肢がなければ
if( $choices->count() == 0 ) {
    echo '<div class="center">この問題に登録されている選択肢はまだありません。</div>'."\n";
}
// 選択肢があればリストを表示
else {
    // 表の作成（タイトル）
    echo '<table>'."\n";
    echo '<tr><th>選択肢</th><th>正誤</th><th>選択肢の内容</th>'.
        '<th>順序変更</th><th>操作</th></tr>'."\n";
    // 表の作成（内容表示）
    foreach ( $choices as $choice ) {
        // サブフォーム用のデータを準備しておく
        $opts = [
            'eid' => $eset->id,
            'sid' => $section->id,
            'qid' => $question->id,
            'cid' => $choice->id,
            'key' => $question_key
        ];
        echo '<tr>'."\n";
        // 選択肢番号
        echo '<td>'.$choice->subseq.'</td>'."\n";
        // 正誤のボタン
        echo '<td><input type="radio" name="answer" value="'.$choice->subseq.'" '.
            ($choice->correct == 1 ? 'checked' : '').'></td>'."\n";
        // 選択肢の内容
        echo '<td>'.$this->Form->text('choices['.($choice->subseq - 1).']', [
            'label' => false, 'default' => ($choice->text),
            'readonly' => $readonly, 'size' => 60
        ]).'</td>'."\n";
        // 順序変更 : 編集モードであればボタンを表示
        echo '<td>'."\n";
        if ( ! $readonly ) {
            echo '<div class="formset">'."\n";
            echo $this->Form->button('▲', [
                'onclick' => "location.href='".
                $this->Url->build([
                    'controller' => 'Question', "action" => 'moveUp',
                    '?' => $opts
                ])."'", 'type' => 'button']);
            echo $this->Form->button('▼', [
                'onclick' => "location.href='".
                $this->Url->build([
                    'controller' => 'Question', "action" => 'moveDown',
                    '?' => $opts
                ])."'", 'type' => 'button']);
        }
        echo '</div>'."\n";
        echo '</td>'."\n";
        // 操作 : 編集モードであればボタンを表示
        echo '<td>'."\n";
        if ( ! $readonly ) {
            echo $this->Form->button('削除', [
                'name' => 'cid',
                'value' => $choice->id,
                'onclick' => "location.href='".
                $this->Url->build([
                    'controller' => 'Question', "action" => 'deleteChoice',
                    '?' => $opts
                ])."'", 'type' => 'button'
            ]);
        }
        echo '</td>'."\n";
        // 1 行終わり
        echo '</tr>'."\n";
    }
    echo '</table>'."\n";
}

// 編集モードであれば保存ボタンを表示
if ( ! $readonly ) {
    echo '<div class="center">'."\n";
    echo $this->Form->button('問題を保存する');
    echo '</div>'."\n";
}

// フォームの終了
echo $this->Form->end()."\n";

// セクションの新規追加と学生の画面でどう見れるかのボタンを表示
echo '<div class="center">'."\n";
echo '<div class="formset">'."\n";
// 編集モードであれば新規追加ボタンを表示する
if ( ! $readonly ) {
    echo $this->Form->create(false, [
        "type" => "post",
        "url" => [ "controller" => "Question", "action" => "createChoice"]
    ])."\n";
    echo $this->Form->hidden('eid', [ 'value' => $eset->id ])."\n";
    echo $this->Form->hidden('sid', [ 'value' => $section->id ])."\n";
    echo $this->Form->hidden('qid', [ 'value' => $question->id ])."\n";
    echo $this->Form->button('選択肢を新規追加')."\n";
    echo $this->Form->end()."\n";
}
//  View ボタンを表示する
echo $this->Form->create(false, [
    "type" => "post",
    "url" => [ "controller" => "Question", "action" => "view"]
])."\n";
echo $this->Form->hidden('eid', [ 'value' => $eset->id ])."\n";
echo $this->Form->hidden('sid', [ 'value' => $section->id ])."\n";
echo $this->Form->hidden('qid', [ 'value' => $question->id ])."\n";
echo $this->Form->button('表示内容を確認')."\n";
echo $this->Form->end()."\n";
echo '</div>'."\n";
echo '</div>'."\n";
?>