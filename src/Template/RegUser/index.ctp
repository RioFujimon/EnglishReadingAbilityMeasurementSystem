<?php
// 登録されているグループに所属するユーザの統計情報を表示
echo '<div class="label">登録済み学生の状況</div>'."\n";
echo '<div class="spacer"></div>'."\n";
// コントローラでセットされたグループのユーザ情報を取得
$info = $this->viewVars['EramsGroupInfo'];
// グループが無ければ
if ( count($info) == 0 ) {
    echo '<div class="context">システムに登録されているグループがまだありません。</div>'."\n";
}
// グループがあれば表を表示
else {
    $count = 0;
    echo '<table>'."\n";
    echo '<tr><th>&nbsp;</th><th>グループ名</th><th>登録済み学生人数</th><th>&nbsp;</th></tr>'."\n";
    foreach ( $info as $i ) {
        echo '<tr>';
        echo '<td>'.++$count.'</td>';
        echo '<td>'.$i['gname'].'</td>';
        echo '<td>'.$i['count'].' 名</td>';
        echo '<td>';
        echo $this->Form->create("null", [
            "type" => "get",
            "url" => [ "controller" => "RegUser", "action" => "groupIndex" ]
        ])."\n";
        echo $this->Form->button('学生情報編集', [ 'name' => 'gid', 'value' => $i['gid'] ] );
        echo $this->Form->end();
        echo '</td>';
        echo '</tr>'."\n";
    }
    echo '</table>'."\n";
}
?>
