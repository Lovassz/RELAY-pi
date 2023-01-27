<?php

if (!isset($_GET['interval']) || empty($_GET['interval'])) exit;

$currentCallEstimated = intval(@file_get_contents('data/switch_esimated_next_call.log') ?? 0);
@file_put_contents('data/switch_esimated_next_call.log', time() + intval($_GET['interval'] ?? '') / 1000000);
@file_put_contents('logs/'. date('Y-m-d') .'.csv', implode(',', 
    [
        date('H:i:s'),
        time(),
        intval($_GET['interval'] ?? ''),
        (time() - $currentCallEstimated),
    ]
) ."\r\n", FILE_APPEND);

echo 'ok';

//http://192.168.1.6/cm?user=admin&password=stupid&cmnd=Power%20On