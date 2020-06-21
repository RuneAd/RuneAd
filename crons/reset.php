<?php
include "cron_init.php";

$delay = 600; // 10 minutes in between runs.
$lock  = new CronLock("reset_cron", $delay);

if ($lock->isLocked()) {
    exit;
}

$lock->writeLock();
writeLog("Started vote reset...");

$updated   = 0;
$startTime = microtime(true);
$servers   = Servers::get();

foreach ($servers as $server) {
    $server->votes = 0;
    $server->save();
    $updated++;
}

$endTime = microtime(true);
$elapsed = number_format($endTime - $startTime, 4);

writeLog("Reset $updated servers' vote counts. Executed in ".$elapsed."s!");
$lock->writeLock(); // write lock again so next one is delayed
