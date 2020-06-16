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
    
    foreach ($servers as $server) {
        if (!$server->server_ip || !filter_var($server->server_ip, FILTER_VALIDATE_IP)) {
            $server->is_online = 0;
            $server->ping = -1;
            $server->last_ping = time();
            $server->save();
            continue;
        }

        $ping = new ServerPing();
        $ping->setAddress($server->server_ip)->setPort($server->server_port);
        $ping->connect();

        $time = $ping->getPing();

        $server->is_online = $time != -1;
        $server->ping = $time;
        $server->last_ping = time();
        $server->save();

        $updated++;
    }

    $endTime = microtime(true);
    $elapsed = number_format($endTime - $startTime, 4);

    writeLog("Updated $updated server statuses. Executed in ".$elapsed."s!");