<?php
    include "cron_init.php";
    include DOC_ROOT.'/../app/plugins/ServerPing.php';
    
    $online  = 0;
    $offline = 0;
    $updated = 0;

    $startTime = microtime(true);

    $servers = Servers::select([
        'id', 'title', 'is_online', 'server_ip', 'server_port', 'last_ping'
    ])->where('server_ip', '!=', null)->get();

    $ping = new ServerPing();
    
    foreach ($servers as $server) {
        if (!$server->server_ip || !filter_var($server->server_ip, FILTER_VALIDATE_IP)) {
            continue;
        }

        $ping->setAddress($server->server_ip)->setPort($server->server_port);
        $ping->connect();

        $time = $ping->getPing();

        $server->is_online = $time < 1000;
        $server->ping = $time < 1000 ? $time : -1;
        $server->last_ping = time();
        $server->save();

        if ($time < 1000) {
            $online++;
        } else {
            $offline++;
        }

        echo "Pinged {$server->server_ip}:{$server->server_port}: $time ms\r\n";
        $updated++;
    }

    $endTime = microtime(true);
    $elapsed = number_format($endTime - $startTime, 4);

    writeLog("Updated $updated server statuses. Online: $online, Offline: $offline. Executed in ".$elapsed."s!");