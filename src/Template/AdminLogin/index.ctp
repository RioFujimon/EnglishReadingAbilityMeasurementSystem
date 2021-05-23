<?php
// ポータルに戻る
echo '<div class="context">'.
     $this->Html->link('英文読解能力測定システムポータルページ', '/').
     'に戻る</div>'."\n";
echo '<div class="spacer"></div>'."\n";

// ログインフォームの開始
echo $this->Form->create("null", [
    "type" => "post",
    "url" => [
        "controller" => "AdminLogin",
        "action" => "index" ]
])."\n";

echo '<div class="login">'."\n";
echo '<div class="spacer"></div>'."\n";

echo '<table class="noborder">'."\n";
echo '<tr><td>ユーザ名</td><td>'.$this->Form->text('uname', [ 'label' => false, 'div' => false]).'</td></tr>'."\n";
echo '<tr><td>パスワード</td><td>'.$this->Form->password('passwd', [ 'label' => false, 'div' => false]).'</td></tr>'."\n";
echo '</table>'."\n";

echo '<div class="spacer"></div>'."\n";
echo '<div class="center">'.$this->Form->button('login').'</div>'."\n";
echo '<div class="spacer"></div>'."\n";
echo '</div>'."\n";


// フォームの終了
echo $this->Form->end();
?>
