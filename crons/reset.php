<?php
     include "cron_init.php";

     $updated = 0;

     writeLog("Started vote reset...");

     $startTime = microtime(true);

     $servers = Servers::get();

     foreach ($servers as $server) {
         $server->votes = 0;
         $server->save();
         $updated++;
     }

     $endTime = microtime(true);
     $elapsed = number_format($endTime - $startTime, 4);

     writeLog("Reset $updated servers' vote counts. Executed in ".$elapsed."s!");
