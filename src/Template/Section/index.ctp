<?php
$alart = $this->element('Alart', [
    "alart" =>
    "問題の削除に伴って下記の関連するデータも削除されます。\n".
    "・問題に付随する選択肢\n".
    "・学生が過去に解答したこの問題の解答結果（他の問題は消えません）\n".
    "削除してもよろしいですか？"
]);

// コントローラでセットされた情報を取得
$eset = $this->viewVars['Erams.Eset'];
$section = $this->viewVars['Erams.Section'];
$questions =  $this->viewVars['Erams.Questions'];
$choices =  $this->viewVars['Erams.Choices'];
$section_pair =  $this->viewVars['Erams.SectionPair'];

// ラベルの表示
echo '<div class="label">セクション編集フォーム</div>'."\n";
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
    "英文（本文）の中に以下の文字が含まれる場合には自動的に文字を変換します。\n".
    "・「".'"'."（ダブルクオート）」-&gt; &amp;#034;\n".
    "・「'（シングルクオート）」-&gt; &amp;#039;\n"
    ]);
}

// 親のテストセットの状態を表示する
echo '<p class="'.($readonly ? 'readonly' : 'writable').'">';
echo '親のテストセットの状態（'.
    ($readonly ? "テスト提供中[編集不可]" : "編集可能").'）'.
    ' :&nbsp; '.($eset->title).'</p>'."\n";
echo '<div class="spacer"></div>'."\n";

// 次のセクションと前のセクションの表示
echo '<div class="center">'."\n";
echo '<div class="formset" align="center">'."\n";
echo $this->Form->create(false, [
    "type" => "post",
    "url" => [ "controller" => "Section", "action" => "index"]
])."\n";
echo $this->Form->hidden('eid', [ 'value' => $eset->id ])."\n";
echo $this->Form->button('前のセクション', [
    'name' => 'sid',
    'value' => (0 < $section_pair[0] ? $section_pair[0] : $section->id)
])."\n";
echo $this->Form->button('次のセクション', [
    'name' => 'sid',
    'value' => (0 < $section_pair[1] ? $section_pair[1] : $section->id)
])."\n";
echo $this->Form->end()."\n";
echo '</div>'."\n";
echo '</div>'."\n";

// 指定されたセクションの情報を表示する
// フォームを開始
echo '<div class="test_set">'."\n";
echo '<div class="spacer"></div>'."\n";
echo $this->Form->create(false, [
    "type" => "post",
    "url" => [ "controller" => "Section", "action" => "save"]
])."\n";
// 隠し情報を表示
echo $this->Form->hidden('eid', [ 'value' => $eset->id ] )."\n";
echo $this->Form->hidden('sid', [ 'value' => $section->id ] )."\n";

// セクションの内容を表示
echo '<div class="left">セクション</div>'."\n";
echo '<div class="right">No.'.($section->subseq).'</div>'."\n";
echo '<div class="left">タイトル</div>'."\n";
echo '<div class="right">';
echo $this->Form->text('title', [
         'label' => false, 'default' => $section->title,
         'readonly' => $readonly, 'size' => 80
]);
echo '</div>'."\n";
echo '<div class="left">制限時間（秒）</div>'."\n";
echo '<div class="right">';
echo $this->Form->text('tlimit', [
    'label' => false, 'default' => $section->tlimit,
    'readonly' => $readonly, 'size' => 10
]);
echo '</div>'."\n";
echo '<div class="left">説明</div>'."\n";
echo '<div class="right">';
echo $this->Form->textarea('property', [
    'label' => false, 'default' => $section->property,
    'readonly' => $readonly, 'rows' => 8, 'size' => 50
]);
echo '</div>'."\n";
echo '<div class="center">本文（英文）内容</div>'."\n";
echo '<div class="center">';
$text = $section->text;
$text = preg_replace("/<br>/", "\n", $text);
echo $this->Form->textarea('text', [
    'label' => false, 'default' => $text,
    'readonly' => $readonly, 'rows' => 15, 'cols' => 70
]);
echo '</div>'."\n";
// 編集モードであれば保存ボタンを表示
if ( ! $readonly ) {
    // ボタンの表示
    echo '<div class="center">'."\n";
    echo $this->Form->button('セクションを保存');
    echo '</div>'."\n";
}
echo '<div class="spacer"></div>'."\n";
echo '</div>'."\n";
// フォームの終了
echo $this->Form->end();
echo '<div class="spacer"></div>'."\n";

// 学生の画面でどう見れるかのボタンを表示
echo '<div class="center">'."\n";
echo $this->Form->create(false, [
    "type" => "post",
    "url" => [ "controller" => "Section", "action" => "view"]
])."\n";
echo $this->Form->hidden('eid', [ 'value' => $eset->id ] );
echo $this->Form->button('学生側での本文（英文）表示を確認する',[
    'name' => 'sid', 'value' => $section->id
])."\n";
echo $this->Form->end();
echo '</div>'."\n";

// 登録されている問題のリストを表示する
// ラベルの表示
echo '<div class="label">登録済み問題一覧</div>'."\n";
echo '<div class="spacer"></div>'."\n";

// 問題がなければ
if( $questions->count() == 0 ) {
    echo '<div class="center">このセクションに登録されている問題はまだありません。</div>'."\n";
}
// 問題があればリストを表示
else {
    // 表の作成（タイトル）
    echo '<table>'."\n";
    echo '<tr>'."\n";
    echo '<th>順番</th>'."\n";
    echo '<th>問題文</th>'."\n";
    echo '<th>選択肢の数</th>'."\n";
    echo '<th>順序変更</th>'."\n";
    echo '<th>操作</th>'."\n";
    echo '</tr>'."\n";
    // 表の作成（内容表示）
    foreach ( $questions as $question ) {
        echo '<tr>'."\n";
        // 順番と問題の表示
        $text = mb_substr($question->text, 0, 25);
        echo '<td>'.$question->subseq.'</td><td>'.$text.'</td>';
        // 選択肢の数
        echo '<td>'.count($choices[($question->subseq)-1]).'</td>'."\n";
        // 編集中なら上下ボタンを表示する
        if( ! $readonly ) {
            echo '<td>'."\n";
            echo '<div class="formset" align="center">'."\n";
            echo $this->Form->create(false, [
                "type" => "post",
                "url" => [ "controller" => "Section", "action" => "swap" ]
            ])."\n";
            echo $this->Form->hidden('eid', [ 'value' => $eset->id ])."\n";
            echo $this->Form->hidden('sid', [ 'value' => $section->id ])."\n";
            echo $this->Form->hidden('moveto', [ 'value' => 'u' ])."\n";
            echo $this->Form->button('▲', [
                'name' => 'qid', 'value' => $question->id ]);
            echo $this->Form->end()."\n";
            echo $this->Form->create(false, [
                "type" => "post",
                "url" => [ "controller" => "Section", "action" => "swap" ]
            ])."\n";
            echo $this->Form->hidden('eid', [ 'value' => $eset->id ])."\n";
            echo $this->Form->hidden('sid', [ 'value' => $section->id ])."\n";
            echo $this->Form->hidden('moveto', [ 'value' => 'd' ])."\n";
            echo $this->Form->button('▼', [
                'name' => 'qid', 'value' => $question->id ])."\n";
            echo $this->Form->end()."\n";
            echo '</div>'."\n";
            echo '</td>'."\n";
        }
        else {
            echo '<td>操作不可</td>'."\n";
        }
        // 内容操作
        echo '<td>'."\n";
        echo '<div class="formset" align="center">'."\n";
        // 編集（閲覧）ボタン
        echo $this->Form->create(false, [
            "type" => "get",
            "url" => [ "controller" => "Question", "action" => "index" ]
        ])."\n";
        // 編集中かそれ以外でボタン名を変更する
        if ( $eset->mode == "E" ) {
            $btn_name = '編集';
        } else {
            $btn_name = '閲覧';
        }
        echo $this->Form->hidden('eid', [ 'value' => $eset->id ]);
        echo $this->Form->hidden('sid', [ 'value' => $section->id ]);
        echo $this->Form->button($btn_name, [
  	     'name' => 'qid', 'value' => $question->id ]);
        echo $this->Form->end();
        // 編集中の場合には削除ボタンも表示させる
        if ( $eset->mode == "E" ) {
            echo $this->Form->create(false, [
                "type" => "post",
                "url" => [ "controller" => "Section", "action" => "delQuestion" ],
                "onsubmit" => $alart
            ])."\n";
            echo $this->Form->hidden('eid', [ 'value' => $eset->id ]);
            echo $this->Form->hidden('sid', [ 'value' => $section->id ]);
            echo $this->Form->button('削除', [
                'name' => 'qid', 'value' => $question->id ]);
            echo $this->Form->end();
        }
        echo '</div></td>'."\n";
        // 1 行終わり
        echo '</tr>'."\n";
    }
    echo '</table>'."\n";
}

// 新規追加のボタンを表示
if ( $eset->mode == "E" ) {
    echo '<div class="spacer"></div>'."\n";
    echo '<div class="center">'."\n";
    echo $this->Form->create(false, [
        "type" => "post",
        "url" => [ "controller" => "Section", "action" => "createQuestion" ]
    ])."\n";
    echo $this->Form->hidden('eid', [ 'value' => $eset->id ])."\n";
    echo $this->Form->button('問題の新規追加',  [
        'name' => 'sid', 'value' => $section->id
    ]);
    echo $this->Form->end()."\n";
    echo '</div>'."\n";
}
?>
