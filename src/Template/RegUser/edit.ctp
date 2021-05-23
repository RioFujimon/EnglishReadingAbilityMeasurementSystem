<?php
// 学生削除時の確認用メッセージの作成
$alart = $this->element('Alart', [
    "alart" =>
    "学生の削除に伴って下記の関連するデータも削除されます。\n".
    "・学生が過去に実施したテストの結果データ\n".
    "削除してもよろしいですか？"
]);

// 1。登録されている機関のユーザ統計を表示する
echo '<div class="label">機関「'.$this->viewVars['EramsGname'].'」の学生登録状況（<a href="#bottom">学生の登録や削除</a>はページ下部へ）</div>'."\n";
echo '<div class="spacer"></div>'."\n";
// コントローラでセットされたグループのユーザ情報を取得
$users = $this->viewVars['EramsUsers'];
// グループが無ければ
if ( count($users) == 0 ) {
    echo '<div class="context">登録されている学生はまだ居ません。</div>'."\n";
}
// ユーザが居れば表を表示
else {
    $count = 0;
    echo '<table>'."\n";
    echo '<tr><th>&nbsp;</th><th>ユーザ名</th><th>パスワード</th><th>&nbsp;</th></tr>'."\n";
    foreach ( $users as $user ) {
        echo '<tr>';
        echo '<td>'.++$count.'</td>';
        echo '<td>'.$user['uname'].'</td>';
        echo '<td>'.$user['passwd'].'</td>';
        //
        echo '<td><div class="formset">';
        echo $this->Form->create("null", [
            "type" => "post",
            "url" => [ "controller" => "RegUser", "action" => "chpass" ]
        ])."\n";
        echo $this->Form->button('パスワード初期化', [ 'name' => 'uid', 'value' => $user['uid'] ] );
        echo $this->Form->end();
        echo $this->Form->create("null", [
            "type" => "post", "onsubmit" => $alart,
            "url" => [ "controller" => "RegUser", "action" => "del" ]
        ])."\n";
	echo $this->Form->hidden('gid', [ 'value' => $this->viewVars['EramsGid']] )."\n";
        echo $this->Form->button('削除', [ 'name' => 'uid', 'value' => $user['uid'] ]);
        echo $this->Form->end();
        echo '</div></td>';
        echo '</tr>'."\n";
    }
    echo '</table>'."\n";
}
echo '<div class="spacer"></div>'."\n";


// 2。学生の新規登録用フォームの表示
echo '<a name="bottom"></a>'."\n";
echo '<div class="label">学生の新規登録</div>'."\n";
echo $this->element("Info", [ "info" =>
'接頭語＝"TGU", 開始番号＝"2000", 人数＝"100" を指定して'."\n".
'学生のアカウントを作成した場合には、TGU2000〜TGU2099 が作られます。'."\n".
'数字部分の桁数は4桁に固定され、桁数が足りない場合には先頭に0が埋められます。'."\n".
'機関の中に複数の接頭語を持つ学生アカウントが存在しても問題はありません。'
]);
// フォームを開始
echo $this->Form->create('null', [
    'type' => 'post',
    'url' => [
        'controller' => 'RegUser',
        'action' => 'add' ]
])."\n";
// Gid を設置しないとね
echo $this->Form->hidden('gid',
[ 'value' => $this->viewVars['EramsGid'], 'label' => false, 'div' => false ] )."\n";
// コントロールを配置
echo '<table class="noborder">'."\n";
echo '<tr><th>接頭語（英字4文字以内）</th><td>'.$this->Form->text('prefix', [ 'label' => false, 'div' => false ]).'</td></tr>'."\n";
echo '<tr><th>開始番号</th><td>'.$this->Form->text('start', [ 'label' => false, 'div' => false ] ).'</td></tr>'."\n";
echo '<tr><th>人数</th><td>'.$this->Form->text('count', [ 'label' => false, 'div' => false ] ).'</td></tr>'."\n";
echo '<tr><td>&nbsp;</td><td>'.$this->Form->button('登録').'</td></tr>'."\n";
echo '</table>'."\n";
// フォームの終了
echo $this->Form->end();
echo '<div class="spacer"></div>'."\n";

// 3。学生の一括削除フォームの表示
echo '<div class="label">学生の一括削除</div>'."\n";
echo $this->element("Info", [ "info" =>
'接頭語＝"TGU", 開始番号＝"2000", 終了番号＝"2099" を指定した場合、'."\n".
'TGU2000〜TGU2099 までのアカウント名に一致する学生アカウントが削除されます。'
]);
// フォームを開始
echo $this->Form->create('null', [
    'type' => 'post', 'onsubmit' => $alart,
    'url' => [
        'controller' => 'RegUser',
        'action' => 'del' ]
])."\n";
// Gid を設置しないとね
echo $this->Form->hidden('gid', [ 'value' => $this->viewVars['EramsGid'] ] )."\n";
// コントロールを配置
echo '<table class="noborder">'."\n";
echo '<tr><th>接頭語（英字4文字以内）</th><td>'.$this->Form->text('prefix', [ 'label' => false, 'div' => false ]).'</td></tr>'."\n";
echo '<tr><th>開始番号</th><td>'.$this->Form->text('start').'</td></tr>'."\n";
echo '<tr><th>終了番号</th><td>'.$this->Form->text('end').'</td></tr>'."\n";
echo '<tr><td>&nbsp;</td><td>'.$this->Form->button('削除').'</td></tr>'."\n";
echo '</table>'."\n";
// フォームの終了
echo $this->Form->end();

?>
