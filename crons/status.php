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
        try {
            $ping->setAddress($server->server_ip)->setPort($server->server_port);
            $time = $ping->connect();

            $server->is_online = $time != -1;
            $server->ping = $time != -1 ? $time : -1;
            $server->last_ping = time();
            $server->save();

            if ($time > -1) {
                $online++;
            } else {
                $offline++;
            }
        } catch (Exception $e) {
            $server->is_online = 0;
            $server->ping = -1;
            $server->last_ping = time();
            $server->save();
            $offline++;
        }
        $updated++;
    }

    $endTime = microtime(true);
    $elapsed = number_format($endTime - $start, 4);

    writeLog("Updated $updated server statuses. Online: $online, Offline: $offline. Executed in ".$elapsed."s!");