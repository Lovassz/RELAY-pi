<?php


// The Tasmota relay's Address (STATIC)
$serverURL = '192.168.1.5';
$relayURL  = '192.168.1.6';
$switchURL = '192.168.1.7';


switch(array_keys($_GET)[0]) {

    case 'cron'  : cron($relayURL);   break;
    case 'switch': sw();              break;
    case 'status': status($relayURL); break;

}











function cron($relayURL) {

    try {
        
        // Ekkorra várjuk a következő becsekkolást
        $currentCallEstimated = intval(@file_get_contents('data/switch_esimated_next_call.log') ?? 0);

        // Ha még nem értük el a következő becsekkolás idejét, akkor rendben vagyunk
        if (time() < $currentCallEstimated) {
            file_get_contents("http://$relayURL/cm?user=admin&password=stupid&cmnd=Power%20On");
        }
        else {

            @file_put_contents('logs/errors.csv', implode(',', 
                [
                    date('Y-m-d H:i:s'),
                    time(),
                    (time() - $currentCallEstimated), // Eltérés a várt becsekkolás időpont és a tényleges között
                ]
            ) ."\r\n", FILE_APPEND);

        }
        
    }
    catch (\Throwable $t) {}
    catch (\Exception $e) {}

}

function sw() {

    try {
                
        // Ha hiányoznak a paraméterek, akkor megállunk
        if (!isset($_GET['interval']) || empty($_GET['interval'])) exit;

        // Ekkorra vártuk a mostani becsekkolást
        $currentCallEstimated = intval(@file_get_contents('data/switch_esimated_next_call.log') ?? 0);
        // Eltesszük a következő becsekkolás várható időpontja
        @file_put_contents('data/switch_esimated_next_call.log', time() + intval($_GET['interval'] ?? '') / 1000000);
        // Elmentjük a logba a rekordot
        @file_put_contents('logs/'. date('Y-m-d') .'.csv', implode(',', 
            [
                date('H:i:s'),
                time(),
                intval($_GET['interval'] ?? ''),
                (time() - $currentCallEstimated), // Eltérés a várt becsekkolás időpont és a tényleges között
            ]
        ) ."\r\n", FILE_APPEND);
        
    }
    catch (\Throwable $t) {}
    catch (\Exception $e) {}

}

function status ($relayURL) {

    /***************************************************/
    /*  Megnézzük mikor csekkolt be utoljára a Switch  */
    /***************************************************/
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
        $switchStatus = (intval($lastLine[1]) + intval($lastLine[2]) / 1000000 + 60) > time() ? 'OK' : 'ERROR';

    }
    catch (\Throwable $t) {}
    catch (\Exception $e) {}



    /**************************/
    /*  Megpingeljük a relét  */
    /**************************/
    try {

        // Getting Tasmota status
        $relayStatus   = 'ERROR';
        $relayResponse = json_decode(file_get_contents("http://$relayURL/cm?user=admin&password=stupid&cmnd=Status0"), true);

        if (!empty($relayResponse))
        $relayStatus = 'OK';

    }
    catch (\Throwable $t) {}
    catch (\Exception $e) {}


    // RESPONDING
    echo json_encode([
        'serverStatus' => 'OK',
        'switchStatus' => $switchStatus,
        'relayStatus'  => $relayStatus,
    ], JSON_PRETTY_PRINT);

}