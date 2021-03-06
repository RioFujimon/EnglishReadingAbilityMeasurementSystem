<?php
// テストセット削除時の確認用メッセージの作成
$alart = $this->element('Alart', [
    "alart" =>
    "テストセットの削除に伴って下記の関連するデータも削除されます。\n".
    "・セットに付随するセクション。\n".
    "・セクションのに付随する問題。\n".
    "・学生が過去に実施したテストの結果データ。\n".
    "削除してもよろしいですか？"
]);

// セットのモード変更時の確認用メッセージの作成
$alart2 = $this->element('Alart', [
    "alart" =>
    "モードの変更を行います。\n".
    "変更してもよろしいですか？"
]);

// モードを編集中に戻す時の確認用メッセージの作成?
$alart3 = $this->element('Alart', [
    "alart" =>
    "セットをバージョンアップし、再度編集を行えるようにします。\n".
    "変更してもよろしいですか？"
]);


// ラベルの表示
echo '<div class="label">登録済みテストセット一覧</div>'."\n";
echo '<div class="spacer"></div>'."\n";

// コントローラでセットされたテストセットの情報を確保
$eset_info = $this->viewVars['EramsSetInfo'];

// もしセットがなければ
if( count($eset_info) == 0 ) {
    echo '<div class="context">登録されているセットはありません。</div>'."\n";
    echo $this->Form->create("null", [
        "type" => "get",
        "url" => [
            "controller" => "EsetEdit",
            "action" => "index" ]
    ])."\n";
    echo $this->Form->button('新規作成', [
        'name' => 'eid', 'value' => '0' ])."\n";
    echo $this->Form->end()."\n";
}
// セットがあればリストを表示
else {
    echo $this->element("Info", ["info" =>
    '登録されているセットの一覧です。'."\n".
    '編集ボタンでセットの編集、閲覧ボタンでセットの内容の閲覧が行えます。'."\n".
    'モード下のそれぞれのボタンでモードを変更します。'."\n".
    '削除ボタンでセットを削除します。']);
    
    echo '<table>'."\n";
    echo '<tr>'."\n";
    echo '<th>番号</th>'."\n";
    echo '<th>タイトル</th>'."\n";
    echo '<th>&nbsp;</th>'."\n";
    echo '<th>モード</th>'."\n";
    echo '<th>バージョン</th>'."\n";
    echo '<th>&nbsp;</th>'."\n";
    echo '</tr>'."\n";
    
    foreach ( $eset_info as $eset ) {
    	echo '<tr>'."\n";
        echo '<td>'.$eset['eid'].'</td>'."\n";
        echo '<td>'.$eset['title'].'</td>'."\n";
        
        echo '<td>'."\n";
        echo $this->Form->create("null", [
            "type" => "get",
            "url" => [ "controller" => "EsetEdit", "action" => "index" ]
        ]);
        // 編集中なら
        if( strcmp($eset['mode'], 'E') == 0 ) {
            $btn_name = '編集';
        }
        // それ以外なら
        else {
            $btn_name = '閲覧';
        }
        echo $this->Form->button($btn_name, [
            'name' => 'eid', 'value' => $eset['eid'] ]);
        echo $this->Form->end();

        if( strcmp($eset['mode'], 'W') == 0 ) {
            echo $this->Form->create("null", [
                "type" => "get",
                "url" => [ "controller" => "Stat", "action" => "index" ]
            ]);
            echo $this->Form->button('統計結果', [
                'name' => 'eid', 'value' => $eset['eid'] ]);
            echo $this->Form->end();
        }
        echo '</td>'."\n";
        
        echo '<td>'."\n";
        // モードの表示
        if( strcmp($eset['mode'], 'E') == 0 ) {
            echo '編集中';
            $label = 'テストに提供';
        }
        else if( strcmp($eset['mode'], 'W') == 0 ) {
            echo '提供中';
            $label = 'テストを終了';
        }
        else if( strcmp($eset['mode'], 'F') == 0 ) {
            echo '終了';
        }
        echo '<div class="formset">'."\n";
        // 提供中であれば戻るボタンも表示
        if( strcmp($eset['mode'], 'W') == 0 ) {
            echo $this->Form->create("null", [
                "type" => "post",
                "url" => [ "controller" => "EsetList", "action" => "returnMode" ],
                "onsubmit" => $alart3
            ])."\n";
            echo $this->Form->button('セットを再編集', [
                'name' => 'eid', 'value' => $eset['eid'] ]);
            echo $this->Form->end();
            //echo '</div>'."\n";
        }

        // 終了していなければモード変更ボタンを表示
        if( strcmp($eset['mode'], 'F') != 0 ) {
            echo $this->Form->create("null", [
                "type" => "post",
                "url" => [ "controller" => "EsetList", "action" => "mode" ],
                "onsubmit" => $alart2
            ])."\n";
            echo $this->Form->button($label, [
                'name' => 'eid', 'value' => $eset['eid'] ]);
            echo $this->Form->end();
        }
        echo '</div>'."\n";
        echo '</td>'."\n";
        echo '<td>'.$eset['version'].'</td>'."\n";
        echo '<td>'."\n";
        echo $this->Form->create("null", [
            "type" => "post",
            "url" => [ "controller" => "EsetList", "action" => "delEset" ],
            "onsubmit" => $alart
        ])."\n";
        echo $this->Form->button('削除',[
            'name' => 'eid', 'value' => $eset['eid'] ]);
        echo $this->Form->end();
        echo '</td>'."\n";
        echo '</tr>'."\n";
    }
    // 新規作成ボタンの追加
    echo '<tr>'."\n";
    echo '<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>'."\n";
    echo '<td>'."\n";
    echo $this->Form->create("null", [
        "type" => "get",
        "url" => [ "controller" => "EsetEdit", "action" => "index" ]
    ])."\n";
    echo $this->Form->button('新規作成', [
        'name' => 'eid', 'value' => '0' ]);
    echo $this->Form->end();
    echo '</td>'."\n";
    echo '</tr>'."\n";
    
    echo '</table>'."\n";
}
?>