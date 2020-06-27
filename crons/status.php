<?php
    include "cron_init.php";

$delay = 1800; // minimum 30 minutes in between runs.
$lock  = new CronLock("status_cron", $delay);

try {
    if ($lock->isLocked()) {
        exit;
    }

    // write the lock so initially so it can't start again
    // until it's finished
    $lock->writeLock();

    include DOC_ROOT.'/../app/plugins/ServerPing.php';

    $online  = 0;
    $offline = 0;
    $updated = 0;

    writeLog("Started server status update...");

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
        $time = $ping->connect();

        $server->is_online = $time != -1;
        $server->ping = $time;
        $server->last_ping = time();
        $server->save();
        $updated++;
    }

    $endTime = microtime(true);
    $elapsed = number_format($endTime - $startTime, 4);

    writeLog("Updated $updated server statuses. Executed in ".$elapsed."s!");
    $lock->writeLock(); // write lock again so next one is delayed
} catch (Exception $e) {
    writeLog("[ERROR] ".$e->getMessage());
}
