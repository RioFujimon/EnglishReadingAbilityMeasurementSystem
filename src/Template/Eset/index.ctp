<?php
$alart = $this->element('Alart', [
    "alart" =>
    "セクションの削除に伴って下記の関連するデータも削除されます。\n".
    "・セクションに含まれる問題や選択肢のデータ\n".
    "・このセクションに関連する学生が過去に実施したテストの解答結果\n\n".
    "削除してもよろしいですか？"
]);

// コントローラでセットされた情報を取得
$eset = $this->viewVars['Erams.Eset'];
$sections = $this->viewVars['Erams.Sections'];

// ラベルを表示
echo '<div class="label">テストセット編集フォーム</div>'."\n";
echo '<div class="spacer"></div>'."\n";

// 編集可能かどうかの確認
if ( $eset->mode == 1 ) {
    $readonly = true;
} else {
    $readonly = false;
}

// 親のテストセットの状態を表示する
echo '<p class="'.($readonly ? 'readonly' : 'writable').'">';
echo 'テストセットの状態（'.
    ($readonly ? "テスト提供中（閲覧のみ可能）" : "編集可能").'）<p>'."\n";
echo '<div class="spacer"></div>'."\n";

// 指定されたセットの情報を表示する
// フォームを開始
echo $this->Form->create("null", [
    "type" => "post",
    "url" => [ "controller" => "Eset", "action" => "save" ]
])."\n";

// 隠し情報を表示
echo $this->Form->hidden('eid', [ 'value' => $eset->id ])."\n";

// テストセットの内容
echo '<div class="test_set">'."\n";
echo '<div class="left">タイトル</div>'."\n";
echo '<div class="right">'."\n";
echo $this->Form->text('title', [
    'label' => false, 'default' => $eset->title, 'readonly' => $readonly
]);
echo '</div>'."\n";
echo '<div class="left">プロパティ<br>(学生側では非表示)</div>'."\n";
echo '<div class="right">'."\n";
echo $this->Form->textarea('property', [
    'class' => 'text', 'label' => false, 'default' => $eset->property, 'readonly' => $readonly
]);
echo '</div>'."\n";
if( ! $readonly ) {
    echo '<div class="center">'."\n";
    echo $this->Form->button('保存',[
        'name' => 'eid', 'value' => $eset->id])."\n";
    echo '</div>'."\n";
}
echo '</div>'."\n";
echo '<div class="spacer"></div>'."\n";
// フォーム終了
echo $this->Form->end();

// テストセットに登録されているセクションの一覧を表示する
echo '<div class="label">登録済みセクション一覧</div>'."\n";
echo '<div class="spacer"></div>'."\n";
// セクションがなければ
if( $sections->count() == 0 ) {
    echo '<div class="context" align="center">このテストセットに登録されているセクションはまだありません。</div>'."\n";
}
// セクションがあればリストを表示
else {
    // 表の作成（タイトル）
    echo '<table>'."\n";
    echo '<tr>'."\n";
    echo '<th>順番</th>'."\n";
    echo '<th>セクションタイトル</th>'."\n";
    echo '<th>順序変更</th>'."\n";
    echo '<th>内容の編集</th>'."\n";
    echo '</tr>'."\n";
    // 表の作成（内容表示）
    foreach ( $sections as $section ) {
        // 順番とセクションタイトルの表示
        echo '<tr><td>'.$section->subseq.'</td><td>'.$section->title.'</td>'."\n";
        // 編集可能なら上下ボタンを表示する
        if( ! $readonly ) {
            echo '<td>';
            echo '<div class="formset" align="center">'."\n";
            echo $this->Form->create("null", [
                "type" => "post",
                "url" => [ "controller" => "Eset", "action" => "swap" ]
            ])."\n";
            echo $this->Form->hidden('eid', [ 'value' => $eset->id ]);
            echo $this->Form->hidden('moveto', [ 'value' => 'u' ]);
            echo $this->Form->button('▲', [
                'name' => 'sid', 'value' => $section->id ]);
            echo $this->Form->end();
            echo $this->Form->create("null", [
                "type" => "post",
                "url" => [ "controller" => "Eset", "action" => "swap" ]
            ])."\n";
            echo $this->Form->hidden('eid', [ 'value' => $eset->id ]);
            echo $this->Form->hidden('moveto', [ 'value' => 'd' ]);
            echo $this->Form->button('▼', [
                'name' => 'sid', 'value' => $section->id ]);
            echo $this->Form->end();
            echo '</div>'."\n";
            echo '</td>'."\n";
        }
        else {
            echo '<td>操作不可</td>'."\n";
        }
        // 内容操作
        echo '<td>'."\n";
        // 編集ボタンのフォーム開始
        echo '<div class="formset" align="center">'."\n";
        echo $this->Form->create("null", [
            "type" => "get",
            "url" => [ "controller" => "Section", "action" => "index" ]
        ])."\n";
        echo $this->Form->hidden('eid', [ 'value' => $eset->id ]);
        // 編集中かそれ以外でボタン名を変更する
        if ( ! $readonly ) {
            $btn_name = '編集';
        } else {
            $btn_name = '閲覧';
        }
        echo $this->Form->button($btn_name, [
            'name' => 'sid', 'value' => $section->id
        ]);
        echo $this->Form->end();
        // 編集中の場合には削除フォームも表示させる
        if ( ! $readonly ) {
            echo $this->Form->create("null", [
                "type" => "post",
                "url" => [ "controller" => "Eset", "action" => "deleteSec" ],
                "onsubmit" => $alart
            ])."\n";
            echo $this->Form->hidden('eid', [ 'value' => $eset->id ]);
            echo $this->Form->button('削除', [
                'name' => 'sid', 'value' => $section->id
            ]);
            echo $this->Form->end();
        }
        echo '</div></td>'."\n";        
        // 1 行終わり
        echo '</tr>'."\n";
    }
    echo '</table>'."\n";
}

// 新規作成
if ( ! $readonly ) {
    echo '<div class="center">'."\n";
    echo $this->Form->create("null", [
        "type" => "get",
        "url" => [ "controller" => "Eset", "action" => "createSec" ]
    ])."\n";
    echo $this->Form->hidden('eid', [ 'value' => $eset->id ]);
    echo $this->Form->button('セクションの新規作成', [
        'name' => 'sid', 'value' => '0' ]);
    echo $this->Form->end();
    echo '</div>'."\n";
}
?>