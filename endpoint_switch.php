<?php

if (!isset($_GET['interval']) || empty($_GET['interval'])) exit;

file_put_contents('logs/'. date('Y-m-d') .'.csv', date('H:i:s,') . json_encode($_GET['interval'] ?? '') ."\r\n", FILE_APPEND);

echo 'ok';