<?php
$info = trim($info);
$info = preg_replace('/\n+$/', "", $info);
$info = preg_replace('/\n/', "<br>", $info);
echo '<div class="info">'.$info.'</div>'."\n";
echo '<div class="spacer"></div>'."\n";
?>
