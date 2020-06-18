<?php
    include "cron_init.php";
    include DOC_ROOT.'/../app/plugins/ServerPing.php';

    writeLog("Started server status update...");

    $startTime = microtime(true);
    $updated   = 0;
    $servers   = Servers::get();

    foreach ($servers as $server) {
        $server->votes = 0;
        $server->save();

        $updated++;
    }

    writeLog("Finished vote reset cron.");
