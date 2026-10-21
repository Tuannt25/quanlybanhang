<?php
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
$base = rtrim(str_replace('\\', '/', dirname($scriptName)), '/') . '/';
define('BASE_URL', $base === '//' ? '/' : $base);
?>
