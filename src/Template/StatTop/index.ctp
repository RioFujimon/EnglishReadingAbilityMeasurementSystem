<?php
// コントローラでセットされたテストセットの情報を取得
$esets = $this->viewVars['Erams.Esets'];

// ラベルの表示
echo '<div class="label">テストセット一覧</div>'."\n";
echo '<div class="spacer"></div>'."\n";

// セットがなければ
if( $esets->count() == 0 ) {
    echo '<div class="context">システムに登録されているテストセットはまだありません。</div>'."\n";
}
// セットがあればリストを表示
else {
    echo '<table>'."\n";
    echo '<tr>'."\n";
    echo '<th>No.</th>'."\n";
    echo '<th>テストセット</th>'."\n";
    echo '<th>&nbsp;</th>'."\n";
    echo '</tr>'."\n";
    
    $count = 0;
    foreach ( $esets as $eset ) {
        echo '<tr>'."\n";
        // シーケンス番号
        echo '<td>'.++$count.'</td>'."\n";
        // テストセットタイトル
        echo '<td>'.$eset->title.'</td>'."\n";
        // ボタン
        echo '<td>'."\n";
        // テスト中なら
        if( $eset->mode == 1 ) {
            echo $this->Form->create(null, [
                "type" => "get",
                "id" => "search",
                "url" => [
                    "controller" => "Stat",
                    "action" => "index" ]
                    //"?" => [ "eid" => $eset->id ]]
            ])."\n";
            echo $this->Form->button( '集計結果を表示', [
                'name' => 'eid', 'value' => $eset->id ,
                'form' => 'search'])."\n";
            echo $this->Form->end()."\n";
        }
        else {
            echo '閲覧不可'."\n";
        }
        echo '</td>'."\n";
        echo '</tr>'."\n";
    }
    echo '</table>'."\n";
/*
    // 日付入力
echo '<div class="center">'."\n";
    echo '日付';
    echo $this->Form->create(null, [ "form" => "search" ])."\n";
    echo $this->Form->text('date_start',  [ 'form' => 'search' ])."\n";
    echo '～';
    echo $this->Form->text('date_end', [ 'form' => 'search' ])."\n";
    echo $this->Form->end()."\n";
echo '</div>'."\n";
*/
}
?>