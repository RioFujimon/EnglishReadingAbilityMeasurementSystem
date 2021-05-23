<?php
echo '<div class="label">教員メニュー</div>'."\n";
echo '<ul>'."\n";
echo '<li>'.$this->Html->link('所属機関（グループ）の編集', "/RegInstitute").'</li>'."\n";
echo '<li>'.$this->Html->link('学生情報の編集', "/RegUser").'</li>'."\n";
echo '<li>'.$this->Html->link('テストセットの編集', "/EsetTop").'</li>'."\n";
echo '<li>'.$this->Html->link('テスト結果の集計', "/StatTop").'</li>'."\n";
echo '<li>'.$this->Html->link('ログアウト', "/Logout").'</li>'."\n";
echo '</ul>'."\n";
echo '<div class="label">学生用メニューのテスト</div>'."\n";
echo '<ul>'."\n";
echo '<li>'.$this->Html->link('学生トップページ', "/UserTop").'</li>'."\n";
echo '</ul>'."\n";
?>
