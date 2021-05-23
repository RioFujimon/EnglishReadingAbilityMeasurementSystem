<?php
// グループ削除時の確認用メッセージの作成
$alart = $this->element('Alart', [
    "alart" =>
    "グループの削除に伴って下記の関連するデータも削除されます。\n".
    "・グループに所属する学生のログイン用アカウント\n".
    "・グループに所属する学生が過去に実施したテストの結果データ\n".
    "削除してもよろしいですか？"
]);

// 登録されている機関のリストを表示する
echo '<div class="label">登録済みグループ一覧</div>'."\n";
echo '<div class="spacer"></div>'."\n";
// コントローラでセットされたグループのリストデータを取得
$groups = $this->viewVars['EramsGroups'];
// グループが無ければ
if ( count($groups) == 0 ) {
    echo '<div class="context">登録されているグループはありません。</div>'."\n";
}
// グループがあれば表を表示
else {
    $count = 0;
    echo '<table>'."\n";
    echo '<th>番号</th><th>グループ名</th><th>&nbsp;</th>'."\n";
    foreach ( $groups as $group ) {
        echo '<tr>';
        echo '<td>'.++$count.'</td>';
        echo '<td>'.$group->gname.'</td>';
        echo '<td>';
        echo $this->Form->create("null", [
            "type" => "get", "onsubmit" => $alart,
            "url" => [
                "controller" => "RegInstitute",
                "action" => "del" ]
        ])."\n";
        echo $this->Form->button('削除',
        [ 'name' => 'gid', 'value' => $group->id ]);
        echo $this->Form->end();
        echo '</td>';
        echo '</tr>'."\n";
    }
    echo '</table>'."\n";
}
echo '<div class="spacer"></div>'."\n";

// グループの新規登録用フォームの表示
echo '<div class="label">グループの新規登録</div>'."\n";
echo '<div class="spacer"></div>'."\n";
echo $this->element("Info", [ "info" =>
'グループを新規登録するには下のフォームを利用してください。'."\n".
'グループ名には、英字または数字のみ利用することができます。'."\n".
'（グループ名 admin は利用できません）']);
// フォームを開始
echo $this->Form->create("null", [
    "type" => "post",
    "url" => [
        "controller" => "RegInstitute",
        "action" => "add" ]
])."\n";
// コントロールを配置
echo '<table>'."\n";
echo '<tr>'."\n";
echo '<th>新規グループ名</th>'."\n";
echo '<td>'.$this->Form->text('institute', [ 'label' => false, 'div' => false ] ).
    $this->Form->button('登録').'</td>'."\n";
echo '</tr>'."\n";
echo '</table>'."\n";

// フォームの終了
echo $this->Form->end();
?>
