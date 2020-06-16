<?php
    $start = microtime(true);
    include "cron_init.php";

    use Rakit\Validation\Validator;

    $servers = Servers::select([
            'id', 'title', 'is_online', 'server_ip', 'server_port', 'last_ping'
        ])
        ->where('server_ip', '!=', null)
        ->get();
    
    $online = 0;
    $offline = 0;

    $updated = 0;
    $validator = new Validator;
    $client    = new GuzzleHttp\Client();

    $api_url = "http://api.rune-nexus.com/ping";

    foreach ($servers as $server) {
        $data = [
            'address' => $server->server_ip,
            'port' => $server->server_port
        ];

        $validation = $validator->validate($data, [
            'port'    => 'required|numeric|min:0|max:65535',
            'address' => 'required|ipv4'
        ]);

        if (!$validation->fails()) {
            try {
                $endpoint = $api_url."?address=".$data['address']."&port=".$data['port'];
                $res = json_decode($client->request('GET', $endpoint)->getBody(), true);

                $success = $res['success'];

                $server->is_online = $success;
                $server->ping = $success ? $res['ping'] : -1;
                $server->last_ping = time();
                $server->save();

                if ($success) {
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
    }

    $end = microtime(true);
    $elapsed = number_format($end - $start, 4);

    writeLog("Updated $updated server statuses. Online: $online, Offline: $offline. Executed in ".$elapsed."s!");