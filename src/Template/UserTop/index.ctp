<?php
// ViewVars の確保
$EramsEsets = $this->viewVars['EramsEsets'];

// 公開されている Eset のリストを表示する
echo '<div class="label">公開されているテスト一覧</div>'."\n";
echo '<div class="spacer"></div>'."\n";

// ログアウトボタンの表示
echo '<div class="context">'."\n";
echo $this->Form->create("null", [
    "type" => "post",
    "url" => [ "controller" => "Logout" ]
])."\n";
echo $this->Form->button('ログアウト')."\n";
echo $this->Form->end()."\n";
echo '</div>'."\n";

// 公開されているテストセットが無ければ
if ( count($EramsEsets) == 0 ) {
    echo '<div class="context">公開されているテストはまだありません。</div>'."\n";
}
// 公開されているテストセットがある場合
else {
    $count = 0;
    echo $this->Form->create("null", [
        "type" => "get",
        "url" => [ "controller" => "PreTest" ]
    ])."\n";
    echo '<table>'."\n";
    echo '<tr><th>&nbsp;</th><th>テスト名</th><th>操作</th></tr>'."\n";
    foreach ( $EramsEsets as $eset ) {
        // 
        echo '<tr>'."\n";
        echo '<td>'.++$count.'</td>';
        echo '<td>'.$eset->title.'</td>';
        echo '<td>';
        echo $this->Form->button('このテストを受ける',
        [ 'name' => 'eid', 'value' => $eset->id ] );
        echo '</td>'."\n";
        echo '</tr>'."\n";
    }
    echo '</table>'."\n";
    echo $this->Form->end()."\n";
}

// ログアウトボタンの表示
echo '<div class="context">'."\n";
echo $this->Form->create("null", [
    "type" => "post",
    "url" => [ "controller" => "Logout" ]
])."\n";
echo $this->Form->button('ログアウト')."\n";
echo $this->Form->end()."\n";
echo '</div>'."\n";

echo '<div class="spacer"></div>'."\n";
echo '<div class="label">過去に受験したテスト一覧</div>'."\n";
echo '<div class="spacer"></div>'."\n";
echo $this->Form->create("null", [
        "type" => "get",
        "url" => [ "controller" => "TestResult", '?' => ['uid' => $uid]]
    ])."\n";
echo $this->Form->hidden( 'uid' ,['value' => $uid ]) ;
echo '<table>'."\n";
    echo '<tr><th>&nbsp;</th><th>テスト名</th><th>最新受験日時</th><th>操作</th></tr>'."\n";
    $count = 0;
    for ( $i = count($eidArray); 0 < $i; $i-- ) {
    	$index = $i - 1;
        echo '<tr>';
	echo '<td>'.++$count.'</td>';
        echo '<td>'.$titleArray[$index].'</td>';
        echo '<td>'.$newModArray[$index].'</td>';
	echo '<td>';
        echo $this->Form->button('このテストの結果を見る',
        [ 'name' => 'eid', 'value' => $eidArray[$index] ] );
        echo '</td>'."\n";
        echo '</tr>'."\n";
    }
    echo '</table>';
    echo $this->Form->end()."\n";
?>

