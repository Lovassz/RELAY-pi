<?php


// The Tasmota relay's Address
$relayURL = '192.168.0.17';


/*************************/
/*  Switch ping section  */
/*************************/
try {

    // Getting the current log file
    $handle   = fopen('logs/'. date('Y-m-d') .'.csv', 'r');

    // Gathering the last ping log
    $lastLine = '';
    if ($handle) {
        while (($line = fgets($handle)) !== false) {
            $lastLine = $line;
        }
        fclose($handle);
    }
    $lastLine       = explode(',', $lastLine);

    // Gathering the time from the last ping log
    $lastSwitchPing = intval($lastLine[1]);

}
catch (\Throwable $t) {}
catch (\Exception $e) {}



/************************/
/*  Relay ping section  */
/************************/
try {

    // Getting Tasmota status
    $relayStatus = json_decode(file_get_contents("http://$relayURL/cm?user=admin&password=stupid&cmnd=Status0"), true);

    if (!empty($relayStatus))
        $lastRelayPing = time();

}
catch (\Throwable $t) {}
catch (\Exception $e) {}


// RESPONDING
echo json_encode([
    'now'            => time(),
    'lastSwitchPing' => $lastSwitchPing,
    'lastRelayPing'  => $lastRelayPing,
], JSON_PRETTY_PRINT);