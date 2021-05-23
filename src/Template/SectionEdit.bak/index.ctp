<?php

$alart = $this->element('Alart', [
    "alart" =>
    "問題の削除に伴って下記の関連するデータも削除されます。\n".
    "・問題に付随する選択肢。\n".
    "・学生が過去に実施したテストの結果データ。\n".
    "削除してもよろしいですか？"
]);

// コントローラでセットされたセクションを取得
$eset_mode = $this->viewVars['EramsSetMode'];
$sec_info = $this->viewVars['EramsSecInfo'];
$ques_info =  $this->viewVars['EramsQuesInfo'];

// 編集中なら説明を表示する
if( strcmp($eset_mode, 'E') == 0 ) {
    echo $this->element("Info", [ "info" =>
    'セクションの登録ができます。'."\n".
    '制限時間は、11秒以上に設定してください。'."\n".
    '次の特殊記号は、保存時に変換されますが表示時には問題ありません。'."\n".
    '『\'』⇒&amp;#039;　『"』⇒&amp;quot;　『_』⇒&amp;#095;　『/』⇒&amp;#047;　『\』⇒&amp;#092;'."\n".
    'また、改行は省略されています。'."\n".
    'タイトルが入力されていない場合、保存することができません。']);
}
else {
    echo $this->element("Info", [ "info" =>
    '登録されているセクションの内容を閲覧できます。'."\n".
    '次の特殊記号は、保存時に変換されています。'."\n".
    '『\'』⇒&amp;#039;　『"』⇒&amp;quot;　『_』⇒&amp;#095;　『/』⇒&amp;#047;　『\』⇒&amp;#092;'."\n".
    'また、改行は省略されています。']);
}


// 1。指定されたセクションの情報を表示する
// フォームの開始

echo $this->Form->create("null", [
    "type" => "post",
    "url" => [ "controller" => "SectionEdit", "action" => "saveSec"]])."\n";

echo '<div class="eset">'."\n";
echo '<div class="item1">タイトル</div>'."\n";
echo '<div class="item2">プロパティ</div>'."\n";

echo '<div class="item1">'."\n";
echo $this->Form->text('stitle', [
    'class' => 'stext', 'label' => false, 'default' => $sec_info['title'] ])."\n";
echo '</div>'."\n";
echo '<div class="item2"></div>'."\n";
echo '<div class="item1">制限時間</div>'."\n";
echo '<div class="item2"></div>'."\n";
echo '<div class="item1">'."\n";
echo  $this->Form->text('tlimit', [
    'class' => 'stext', 'label' => false, 'default' => $sec_info['tlimit'] ])."\n";
echo '秒</div>'."\n";

//echo '<div class="item2">プロパティ</div>'."\n";
echo '<div class="item2">'."\n";
echo  $this->Form->textarea('sproperty', [
    'class' => 'stext', 'label' => false, 'default' => $sec_info['prop'] ])."\n";
echo '</div>'."\n";
//echo '<div class="item1">本文</div>'."\n";
//echo '<div class="item2">'."\n";
echo $this->Form->textarea('stext', [
    'class' => 'stext', 'label' => false, 'default' => $sec_info['text'] ])."\n";
//echo '</div>'."\n";
if( strcmp($eset_mode, 'E') == 0 ) {
    echo '<div class="item1">&nbsp;</div>'."\n";
    echo '<div class="item2">'."\n";
    echo $this->Form->hidden('eid', [ 'value' => $eid ]);
    echo $this->Form->button('保存', [
        'name' => 'sid', 'value' => $sid ])."\n";
    echo '</div>'."\n";
}
echo '</div>'."\n";


// フォーム終了
echo $this->Form->end();

// 2。登録されている問題のリストを表示する
echo '<div class="spacer"></div>'."\n";
echo '<div class="label">登録済み問題リスト</div>'."\n";
echo '<div class="spacer"></div>'."\n";

// 問題がなければ
if( count($ques_info) == 0 ) {
    echo '<p>登録されている問題はありません。</p>'."\n";
    if( $sid != 0 ) {
        if( strcmp($eset_mode, 'E') == 0 ) {
            echo '<div>'."\n";
            echo $this->Form->create("null", [
                "type" => "get",
                "url" => [ "controller" => "QuestionEdit", "action" => "index" ]
            ])."\n";
            echo $this->Form->hidden('eid', [ 'value' => $eid ]);
            echo $this->Form->hidden('sid', [ 'value' => $sid ]);
            echo $this->Form->button('新規作成', [
                'name' => 'qid', 'value' => '0' ]);
            echo $this->Form->end();
            echo '</div>'."\n";
        }
    }
}
// 問題があればリストを表示
else {
    // 編集中なら
    if( strcmp($eset_mode, 'E') == 0 ) {
        echo $this->element("Info", [ "info" =>
        'このセクションに登録されている問題の一覧です。'."\n".
        '編集ボタンで編集ページへ遷移します。'."\n".
        '上下ボタンで順番の入れ替え、削除ボタンで問題の削除を行います。']);
    }
    // それ以外なら
    else {
        echo $this->element("Info", ["info" =>
        'このセクションに登録されている問題の一覧です。'."\n".
        '閲覧ボタンで問題を閲覧できます。']);
    }

    // 問題リストを表示する
    echo '<table>'."\n";
    echo '<tr>'."\n";
    echo '<th>順番</th>'."\n";
    echo '<th>本文</th>'."\n";
    echo '<th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th>'."\n";
    echo '</tr>'."\n";
    
    foreach ( $ques_info as $question ) {
        echo '<tr>'."\n";
        echo '<td>'.$question['subseq'].'</td>'."\n";
        echo '<td>'.$question['text'].'</td>'."\n";
        echo '<td>'."\n";
        echo $this->Form->create("null", [
            "type" => "get",
            "url" => [ "controller" => "QuestionEdit", "action" => "index" ]
        ])."\n";
        // 編集中なら
        if( strcmp($eset_mode, 'E') == 0 ) {
            $btn_name = '編集';
        }
        // それ以外なら
        else {
            $btn_name = '閲覧';
        }
        echo $this->Form->hidden('eid', [ 'value' => $eid ]);
        echo $this->Form->hidden('sid', [ 'value' => $sid ]);
        echo $this->Form->button($btn_name, [
            'name' => 'qid', 'value' => $question['qid'] ]);
        echo $this->Form->end();
        echo '</td>'."\n";
        // 編集中なら上下ボタン・削除ボタンを表示する
        if( strcmp($eset_mode, 'E') == 0 ) {
            echo '<td>'."\n";
            echo $this->Form->create("null", [
                "type" => "post",
                "url" => [ "controller" => "SectionEdit", "action" => "swapQues" ]
            ])."\n";
            echo $this->Form->hidden('eid', [ 'value' => $eid ]);
            echo $this->Form->hidden('sid', [ 'value' => $sid ]);
            echo $this->Form->hidden('moveto', [ 'value' => 'u' ]);
            echo $this->Form->button('▲', [
                'name' => 'qid', 'value' => $question['qid'] ]);
            echo $this->Form->end();
            echo $this->Form->create("null", [
                "type" => "post",
                "url" => [ "controller" => "SectionEdit", "action" => "swapQues" ]
            ])."\n";
            echo $this->Form->hidden('eid', [ 'value' => $eid ]);
            echo $this->Form->hidden('sid', [ 'value' => $sid ]);
            echo $this->Form->hidden('moveto', [ 'value' => 'd' ]);
            echo $this->Form->button('▼', [
                'name' => 'qid', 'value' => $question['qid'] ]);
            echo $this->Form->end();
            echo '</td>'."\n";
            echo '<td>'."\n";
            echo $this->Form->create("null", [
                "type" => "post",
                "url" => [ "controller" => "SectionEdit", "action" => "delQues" ],
                "onsubmit" => $alart
            ])."\n";
            echo $this->Form->hidden('eid', [ 'value' => $eid ]);
            echo $this->Form->hidden('sid', [ 'value' => $sid ]);
            echo $this->Form->button('削除', [
                'name' => 'qid', 'value' => $question['qid'] ]);
            echo $this->Form->end();
            echo '</td>'."\n";
        }
        else {
            echo '<td>&nbsp;</td><td>&nbsp;</td>'."\n";
        }
        echo '</tr>'."\n";
    }

    // 編集中なら新規作成ボタン
    if( strcmp($eset_mode, 'E') == 0 ) {
        echo '<tr>'."\n";
        echo '<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>'."\n";
        echo '<td>'."\n";
        echo $this->Form->create("null", [
            "type" => "get",
            "url" => [ "controller" => "QuestionEdit", "action" => "index" ]
        ])."\n";
        echo $this->Form->hidden('eid', [ 'value' => $eid ]);
        echo $this->Form->hidden('sid', [ 'value' => $sid ]);
        echo $this->Form->button('新規作成', [
            'name' => 'qid', 'value' => '0' ]);
        echo $this->Form->end();
        echo '</td>'."\n";
        echo '</tr>'."\n";
    }
    echo '</table>'."\n";
}

?>