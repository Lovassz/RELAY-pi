<?php

if (!isset($_GET['interval']) || empty($_GET['interval'])) exit;

file_put_contents('logs/'. date('Y-m-d') .'.csv', implode(',', 
    [
        date('H:i:s'),
        time(),
        intval($_GET['interval'] ?? ''),
    ]
) ."\r\n", FILE_APPEND);

echo 'ok';