<?php
// テストセット削除時の確認用メッセージ
$alart_del = $this->element('Alart', [
    "alart" =>
    "テストセットの削除に伴って下記の関連するデータも削除されます。\n".
    "・テストセットに付随するセクションやそこに含まれる問題\n".
    "・学生が過去に実施したテストテストの結果データ\n".
    "削除してもよろしいですか？"
]);

// モードを編集中に戻す時の確認用メッセージ
$alart_to_edit = $this->element('Alart', [
    "alart" =>
    "テストセットを再度編集ができるモードに変更します。\n".
    "学生がこれまでにテストを受けた結果も消去されますがよろしいですか？"
]);

// ラベルの表示
echo '<div class="label">登録済みテストセット一覧</div>'."\n";
echo '<div class="spacer"></div>'."\n";

// コントローラでセットされたテストセットの情報を確保
$esets = $this->viewVars['Erams.Esets'];

// もしセットがなければ
if( $esets->count() == 0 ) {
    echo '<div class="context">登録されているセットはありません。</div>'."\n";
}
// セットがあればリストを表示
else {
    // 注意書き
    echo $this->element("Info", ["info" =>
        '・テストセットをテスト可能な状態にすると、そのタイトルが学生側の画面に表示されてテストを受けることが可能になります。'."\n".
        '・テスト可能な状態のテストセットは編集することができません。再び編集するためには、テストセットを一度編集可能な状態に戻す必要があります。'."\n".
    '・テストセットを編集可能な状態に戻した場合には、学生がこれまでに受けたテストの結果が消去されます。'."\n"]);
    // 表の作成
    echo '<table>'."\n";
    echo '<tr>'."\n";
    echo '<th>&nbsp</th>'."\n";
    echo '<th>テストセット</th>'."\n";
    echo '<th>プロパティ</th>'."\n";
    echo '<th>作成日時</th>'."\n";
    echo '<th>モード</th>'."\n";
    echo '<th>操作</th>'."\n";
    echo '</tr>'."\n";
    $count = 0;
    foreach ( $esets as $eset ) {
        // 行開始
    	echo '<tr>'."\n";
        // シーケンス番号
        echo '<td>'.++$count.'</td>'."\n";
        // テストセットタイトル
        echo '<td width="20%">'.$eset->title.'</td>'."\n";
        // プロパティ
        echo '<td width="30%">'.$eset->property.'</td>'."\n";
        // 作成日時
        echo '<td>'.date("Y/m/d H:i:s", strtotime($eset->created)).'</td>'."\n";
        // モード
        echo '<td>';
        if ( $eset->mode == 'E' ) {
            echo '編集中';
        } else {
            echo 'テスト中';
        }
        echo '</td>'."\n";
        // 操作
        if ( $eset->mode == 'E' ) {
            $btn_mode = "テストに提供する";
            $alart = "";
            $btn_edit = "編集";
        } else {
            $btn_mode = "再編集可能な状態に変更";
            $alart = $alart_to_edit;
            $btn_edit = "内容確認";
        }        
        echo '<td>';
        echo '<div class="formset">'."\n";
        // 操作 : モードの切り替え
        if ( $alart != "" ) {
            echo $this->Form->create("null", [
                "type" => "post",
                "url" => [ "controller" => "Eset", "action" => "mode" ],
                "onsubmit" => $alart
            ])."\n";
        } else {
            echo $this->Form->create("null", [
                "type" => "post",
                "url" => [ "controller" => "Eset", "action" => "mode" ]
            ])."\n";
        }
        echo $this->Form->button($btn_mode, [
            'name' => 'eid', 'value' => $eset->id
        ]);
        echo $this->Form->end();
        // 操作 : 編集
        echo $this->Form->create("null", [
            "type" => "get",
            "url" => [ "controller" => "EsetEdit", "action" => "index" ]
        ])."\n";
        echo $this->Form->button($btn_edit, [
            'name' => 'eid', 'value' => $eset->id
        ]);
        echo $this->Form->end();
        // 操作 : 削除
        echo $this->Form->create("null", [
            "type" => "post",
            "url" => [ "controller" => "Eset", "action" => "delEset" ],
            "onsubmit" => $alart_del
        ])."\n";
        echo $this->Form->button("削除", [
            'name' => 'eid', 'value' => $eset->id, 'div' => false
        ]);
        echo $this->Form->end();
        echo '</div>';
        echo '</td>';
        // 行終了
        echo '</tr>'."\n";
    }
    echo '</table>'."\n";
}
// 新規作成
echo '<div class="spacer"></div>'."\n";
echo '<div class="center">';    
echo $this->Form->create("null", [
    "type" => "post",
    "url" => [ "controller" => "Eset", "action" => "createEset" ]
])."\n";
echo $this->Form->button("テストセットの新規作成", [
    'name' => 'eid', 'value' => -1
]);
echo $this->Form->end();
echo '</div>'."\n";
?>
