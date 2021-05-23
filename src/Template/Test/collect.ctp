<?php
// 戻るボタン禁止の JavaScript
echo '<script language="JavaScript">EramsForbidden();</script>'."\n";

// ラベルの表示
echo '<div class="label">テスト終了</div>'."\n";
echo '<dic class="spacer"></div>'."\n";

// 本文を表示
echo '<div class="context">'."\n";
echo '<p>お疲れ様でした。テストは終了しました。</p>'."\n";
echo '<p>以下のリンクからトップページに戻ってください。</p>'."\n";
echo '<p>'.$this->Html->link('学生トップページ', '/UserTop').'</p>'."\n";
echo '</div>'."\n";
?>
