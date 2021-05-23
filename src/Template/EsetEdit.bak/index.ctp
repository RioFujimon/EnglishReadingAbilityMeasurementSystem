<?php
$alart = $this->element('Alart', [
    "alart" =>
    "セクションの削除に伴って下記の関連するデータも削除されます。\n".
    "・セクションに含まれる問題や選択肢のデータ\n".
    "・学生が過去に実施したテスト（このセクションに該当する）解答結果\n\n".
    "削除してもよろしいですか？"
]);

// コントローラでセットされたセットを取得
$eset_info = $this->viewVars['EramsSetInfo'];
$sec_info = $this->viewVars['EramsSecInfo'];

// ラベルを表示
echo '<div class="label">テストセット編集フォーム</div>'."\n";

// 編集中なら説明を表示する
if( strcmp($eset_info['mode'], 'E') == 0 ) {
    echo $this->element("Info", [ "info" =>
    'セットの登録や編集には必ずタイトルを入力する必要があります。']);
}
else {
    echo $this->element("Info", [ "info" =>
    '登録されているセットの内容を閲覧できます。'."\n".
    '次の特殊記号は、保存時に変換されています。'."\n".
    '『\'』⇒&amp;#039;　『"』⇒&amp;quot;　『_』⇒&amp;#095;　『/』⇒&amp;#047;　『\』⇒&amp;#092;'."\n".
    '']);
}

// 1。指定されたセットの情報を表示する
// フォームの開始
echo $this->Form->create("null", [
    "type" => "post",
    "url" => [ "controller" => "EsetEdit", "action" => "saveEset" ]
])."\n";

echo '<div class="eset">'."\n";
echo '<div class="item1">タイトル</div>'."\n";
echo '<div class="item2">'."\n";
echo $this->Form->text('title', [
    'label' => false,'default' => $eset_info['title'] ]);
echo '</div>'."\n";
echo '<div class="item1">プロパティ</div>'."\n";
echo '<div class="item2">'."\n";
echo $this->Form->textarea('property', [
    'class' => 'text', 'label' => false, 'default' => $eset_info['prop'] ]);
echo '</div>'."\n";
if( strcmp($eset_info['mode'], 'E') == 0 ) {
    echo '<div class="item3">'."\n";
    echo $this->Form->button('保存',[
        'name' => 'eid', 'value' => $eid])."\n";
    echo '</div>'."\n";
}
echo '</div>'."\n";
echo '<div class="spacer"></div>'."\n";

// フォーム終了
echo $this->Form->end();

// 2。登録されているセクションのリストを表示する
echo '<div class="label">登録済みセクションリスト</div>'."\n";
echo '<div class="spacer"></div>'."\n";

// セクションがなければ
if( count($sec_info) == 0 ) {
    echo '<div class="context">登録されているセクションはありません。</div>'."\n";
    if( $eid != 0 ) {
        if( strcmp($eset_info['mode'], 'E') == 0 ) {
            echo $this->Form->create("null", [
                "type" => "get",
                "url" => [ "controller" => "SectionEdit", "action" => "index" ]
            ])."\n";
            echo $this->Form->hidden('eid', [ 'value' => $eid ]);
            echo $this->Form->button('新規作成', [
                'name' => 'sid', 'value' => '0' ]);
            echo $this->Form->end();
        }
    }
}
// セクションがあればリストを表示
else {
    // 編集中なら説明を表示する
    if( strcmp($eset_info['mode'], 'E') == 0 ) {
        echo $this->element("Info", [ "info" =>
        'このセットに登録されているセクションの一覧です。'."\n".
        '編集ボタンで編集ページへ遷移します。'."\n".
        '上下ボタンで順番の入れ替え、削除ボタンでセクションの削除を行います。']);
    }
    else {
        echo $this->element("Info", ["info" =>
        'このセットに登録されているセクションの一覧です。'."\n".
        '閲覧ボタンでセクションの内容を閲覧できます。']);
    }
    // 表の作成（タイトル）
    echo '<table>'."\n";
    echo '<tr>'."\n";
    echo '<th>順番</th>'."\n";
    echo '<th>セクションタイトル</th>'."\n";
    echo '<th>順序変更操作</th>'."\n";
    echo '<th>内容の編集</th>'."\n";
    echo '</tr>'."\n";
    // 表の作成（内容表示）
    foreach ( $sec_info as $sec ) {
        // 順番とセクションタイトルの表示
        echo '<tr><td>'.$sec['subseq'].'</td><td>'.$sec['title'].'</td>'."\n";
        // 編集中なら上下ボタンを表示する
//        if( strcmp($eset_info['mode'], 'E') == 0 ) {
        if( $eset_info['mode'] == "E" ) {
            echo '<td>';
            echo $this->Form->create("null", [
                "type" => "post",
                "url" => [ "controller" => "EsetEdit", "action" => "swapSec" ]
            ])."\n";
            echo $this->Form->hidden('eid', [ 'value' => $eid ]);
            echo $this->Form->hidden('moveto', [ 'value' => 'u' ]);
            echo $this->Form->button('▲', [
                'name' => 'sid', 'value' => $sec['sid'] ]);
            echo $this->Form->end();
            echo $this->Form->create("null", [
                "type" => "post",
                "url" => [ "controller" => "EsetEdit", "action" => "swapSec" ]
            ])."\n";
            echo $this->Form->hidden('eid', [ 'value' => $eid ]);
            echo $this->Form->hidden('moveto', [ 'value' => 'd' ]);
            echo $this->Form->button('▼', [
                'name' => 'sid', 'value' => $sec['sid'] ]);
            echo $this->Form->end();
            echo '</td>'."\n";
        }
        else {
            echo '<td>テスト提供中<br>またはテスト終了</td>'."\n";
        }
        // 内容操作
        echo '<td>'."\n";
        echo '<div class="formset">'."\n";
        // 編集（閲覧）ボタン
        echo $this->Form->create("null", [
            "type" => "get",
            "url" => [ "controller" => "SectionEdit", "action" => "index" ]
        ])."\n";
        // 編集中かそれ以外でボタン名を変更する
        if ( $eset_info['mode'] == "E" ) {
            $btn_name = '編集';
        } else {
            $btn_name = '閲覧';
        }
        echo $this->Form->hidden('eid', [ 'value' => $eid ]);
        echo $this->Form->button($btn_name, [
            'name' => 'sid', 'value' => $sec['sid'] ]);
        echo $this->Form->end();
        // 編集中の場合には削除ボタンも表示させる
        if ( $eset_info['mode'] == "E" ) {
            echo $this->Form->create("null", [
                "type" => "post",
                "url" => [ "controller" => "EsetEdit", "action" => "delSec" ],
                "onsubmit" => $alart
            ])."\n";
            echo $this->Form->hidden('eid', [ 'value' => $eid ]);
            echo $this->Form->button('削除', [
                'name' => 'sid', 'value' => $sec['sid'] ]);
            echo $this->Form->end();
        }
        echo '</div></td>'."\n";
        
        // 1 行終わり
        echo '</tr>'."\n";
        

        
    }
    echo '</table>'."\n";
    if ( $eset_info['mode'] == "E" ) {
        echo '<div class="center">'."\n";
        echo $this->Form->create("null", [
            "type" => "get",
            "url" => [ "controller" => "SectionEdit", "action" => "index" ]
        ])."\n";
        echo $this->Form->hidden('eid', [ 'value' => $eid ]);
        echo $this->Form->button('新規作成', [
            'name' => 'sid', 'value' => '0' ]);
        echo $this->Form->end();
        echo '</div>'."\n";
    }


    echo '<div class="spacer"></div>'."\n";
}

?>