<?php
$class = 'warn';
if (!empty($params['class'])) {
    $class .= ' ' . $params['class'];
}
if (!isset($params['escape']) || $params['escape'] !== false) {
    $message = h($message);
}
$message = preg_replace('/\n+$/', "", $message);
$message = preg_replace('/\n+/', "<br>", $message);
?>
<div class="spacer"></div>
<div class="<?= h($class) ?>"><?= $message ?></div>
<div class="spacer"></div>
