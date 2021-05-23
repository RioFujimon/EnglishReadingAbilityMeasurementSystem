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

// コントローラでセットされたテストセットの情報を取得
$esets = $this->viewVars['Erams.Esets'];

// ラベルの表示
echo '<div class="label">登録済みテストセット一覧</div>'."\n";
echo '<div class="spacer"></div>'."\n";

// もしセットがなければ
if( $esets->count() == 0 ) {
    echo '<div class="context">システムに登録されているテストセットはまだありません。</div>'."\n";
}
// セットがあればリストを表示
else {
    // 注意書き
    echo $this->element("Info", ["info" =>
        '・テストセットをテスト可能な状態にすると、そのタイトルが学生側の画面に表示されてテストを受けることが可能になります。'."\n".
        '・テスト可能な状態にあるテストセットは編集することができません。再び編集するためには、テストセットを一度編集可能な状態に戻す必要があります。'."\n".
    '・テストセットを再び編集可能な状態に戻した場合には、学生がこれまでに受けたテストの結果が消去されます。'."\n"]);
    // 表の作成
    echo '<table>'."\n";
    echo '<tr>'."\n";
    echo '<th>No.</th>'."\n";
    echo '<th>テストセット</th>'."\n";
    echo '<th>プロパティ</th>'."\n";
    echo '<th>作成日時</th>'."\n";
    echo '</tr>'."\n";
    $count = 0;
    foreach ( $esets as $eset ) {
        // 1行目開始
    	echo '<tr>'."\n";
        // シーケンス番号
        echo '<td rowspan="2">'.++$count.'</td>'."\n";
        // テストセットタイトル
        echo '<td>'.$eset->title.'</td>'."\n";
        // プロパティ
        echo '<td>'.$eset->property.'</td>'."\n";
        // 作成日時
        //echo '<td>'.date("Y/m/d H:i:s", strtotime($eset->created)).'</td>'."\n";
        echo '<td>'.$eset->created->i18nFormat("HH:mm:ss").'</td>'."\n";
        // 1 行目終了
        echo '</tr>'."\n";
        // 2 行目開始
        echo '<tr>'."\n";
        // モード
        echo '<td>';
        if ( $eset->mode != 1 ) {
            echo '編集可能';
        } else {
            echo 'テスト中';
        }
        echo '</td>'."\n";
        // 操作
        echo '<td colspan="2">'."\n";
        echo '<div class="formset" align="center">'."\n";
        if ( $eset->mode != 1 ) {
            $btn_mode = "テストに提供する";
            $alart = "";
            $btn_edit = "編集";
        } else {
            $btn_mode = "再編集可能な状態に変更";
            $alart = $alart_to_edit;
            $btn_edit = "確認";
        }        
        // 操作 : モードの切り替え
        if ( $alart != "" ) {
            echo $this->Form->create("null", [
                "type" => "post", "onsubmit" => $alart,
                "url" => [ "controller" => "EsetTop", "action" => "changeMode" ],
            ])."\n";
        } else {
            echo $this->Form->create("null", [
                "type" => "post",
                "url" => [ "controller" => "EsetTop", "action" => "changeMode" ]
            ])."\n";
        }
        echo $this->Form->button($btn_mode, [
            'name' => 'eid', 'value' => $eset->id
        ])."\n";
        echo $this->Form->end()."\n";
        // 操作 : 編集
        echo $this->Form->create("null", [
            "type" => "get",
            "url" => [ "controller" => "Eset", "action" => "index" ]
        ])."\n";
        echo $this->Form->button($btn_edit, [
            'name' => 'eid', 'value' => $eset->id
        ])."\n";
        echo $this->Form->end()."\n";
        // 操作 : 削除
        echo $this->Form->create("null", [
            "type" => "post",
            "url" => [ "controller" => "EsetTop", "action" => "deleteEset" ],
            "onsubmit" => $alart_del
        ])."\n";
        echo $this->Form->button("削除", [
            'name' => 'eid', 'value' => $eset->id, 'div' => false
        ]);
        echo $this->Form->end()."\n";
        echo '</div>'."\n";
        echo '</td>'."\n";
        // 2 行目終了
        echo '</tr>'."\n";
    }
    echo '</table>'."\n";
}

// 新規作成
echo '<div class="spacer"></div>'."\n";
echo '<div class="center">';    
echo $this->Form->create("null", [
    "type" => "post",
    "url" => [ "controller" => "EsetTop", "action" => "createEset" ]
])."\n";
echo $this->Form->button("テストセットの新規作成", [
    'name' => 'eid', 'value' => -1
]);
echo $this->Form->end();
echo '</div>'."\n";
?>
