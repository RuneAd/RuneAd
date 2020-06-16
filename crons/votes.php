<?php
    $start = microtime(true);
    include "cron_init.php";

    $servers = Servers::select(['id', 'title', 'votes'])
        ->where('server_ip', '!=', null)
        ->get();

    $startDate = date("Y-m-01 00:00:00");

    $updated = 0;

    foreach ($servers as $server) {
        $votes = Votes::select(['server_id'])
            ->where('server_id', $server->id)
            ->where('voted_on', '>=', strtotime($startDate))
            ->count();

        if ($votes != $server->votes) {
            $server->votes = $votes;
            $server->save();
            $updated++;
        }
    }

    $end = microtime(true);
    $elapsed = number_format($end - $start, 4);

    writeLog("Updated $updated vote counts. Executed in ".$elapsed."s!");