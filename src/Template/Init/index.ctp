<?php
// このページの利用上の注意を表示
echo $this->element("Info", [ "info" =>
     "システムの初期ユーザ（管理者）の情報を登録します。\n".
     "ユーザ名に使える文字は a~zA~Z と _ のみで、".
     "長さを4文字以上にする必要があります。\n".
     "パスワードに使える文字に制限はありませんが、".
     "ユーザ名と同様に長さを4文字以上にする必要があります。"
     ]);

//フォームの作成
echo $this->Form->create("null", [
    "type" => "post",
    "url" => [ "controller" => "Init" ]
])."\n";

echo '<div class="login">'."\n";
echo '<div class="spacer"></div>'."\n";

echo '<table class="noborder">'."\n";
echo '<tr><td>管理者ユーザ</td><td>'.$this->Form->text('uname', [ 'label' => false, 'div' => false ]).'</td></tr>'."\n";
echo '<tr><td>パスワード</td><td>'.$this->Form->password('passwd', [ 'label' => false, 'div' => false ]).'</td></tr>'."\n";
echo '</table>'."\n";

echo '<div class="spacer"></div>'."\n";
echo '<div class="center">'.$this->Form->button('登録').'</div>'."\n";
echo '<div class="spacer"></div>'."\n";
echo '</div>'."\n";

//フォームの終了
echo $this->Form->end();
?>
