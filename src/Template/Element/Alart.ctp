<?php
$alart = trim($alart);
$alart = preg_replace("/\n+$/", "", $alart);
$alart = preg_replace("/\n/", "\\\\n", $alart);
echo "return EramsAlart('".$alart."')";
?>
