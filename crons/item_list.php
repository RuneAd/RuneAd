<?php
include "cron_init.php";

$delay = 600; // 10 minutes in between runs.
$lock  = new CronLock("item_cron", $delay);

if ($lock->isLocked()) {
    exit;
}

$lock->writeLock();

$json  = null;
$start = microtime(true);

try {
    $client = new GuzzleHttp\Client();
    $content = $client->get('https://www.osrsbox.com/osrsbox-db/items-summary.json');
    $json    = array_values(json_decode($content->getBody(), true));
} catch(Exception $e) {
    exit;
}

if (!$json || empty($json)) {
    exit;
}

try {
    $cached = fopen("../app/cache/osrs-item-db.json", 'w');
    fwrite($cached, json_encode($content, JSON_PRETTY_PRINT));
    fclose($cached);
} catch(Exception $e) {
    exit;
}

$item_path = "../public/img/items";
$image_url = "https://www.osrsbox.com/osrsbox-db/items-icons";

foreach($json as $item) {
    if (file_exists($item_path."/{$item['id']}.png")) {
        continue;
    }

    try {
        $client->get($image_url."/{$item['id']}.png", [
            'save_to' => $item_path."/{$item['id']}.png"
        ]);
    } catch(Exception $e) {
        exit;
    }
}

$end = microtime(true);
$elapsed = number_format($end - $start, 4);

writeLog("Done updating item database. Took {$elapsed}s.");
$lock->writeLock(); // write lock again so next one is delayed
