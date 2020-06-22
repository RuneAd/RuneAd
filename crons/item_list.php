<?php
include "cron_init.php";
use GuzzleHttp\Psr7\StreamWrapper;

$delay = 600; // 10 minutes in between runs.
$lock  = new CronLock("item_cron", $delay);
$start = microtime(true);

if ($lock->isLocked()) {
    exit;
}

$lock->writeLock();

$file_url = "https://www.osrsbox.com/osrsbox-db/items-complete.json";
$data = [];

try {
    $client   = new GuzzleHttp\Client();
    $response = $client->get($file_url);
    $stream   = StreamWrapper::getResource($response->getBody());

    foreach (\JsonMachine\JsonMachine::fromStream($stream) as $key => $value) {
        $data[] = [
            'id'           => $value['id'],
            'name'         => $value['name'],
            'members'      => $value['members'],
            'cost'         => $value['cost'],
            'examine'      => $value['examine'],
            'tradeable'    => $value['tradeable'],
            'equipment'    => $value['equipment'],
            'weapon'       => $value['weapon'],
            'release_date' => $value['release_date'],
            'wiki_url'     => $value['wiki_url']
        ];
    }
} catch(Exception $e) {
    exit;
}

if (!$data || empty($data)) {
    exit;
}

try {
    $cached = fopen(DOC_ROOT."/../app/cache/osrs-item-db.json", 'w');
    fwrite($cached, json_encode($data));
    fclose($cached);
} catch(Exception $e) {
    exit;
}

$item_path = DOC_ROOT."/../public/img/items";
$image_url = "https://www.osrsbox.com/osrsbox-db/items-icons";

foreach($data as $item) {
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
echo("Done updating item database. Took {$elapsed}s.");
$lock->writeLock();
