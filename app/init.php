<?php
    session_start();
    use Illuminate\Database\Capsule\Manager as DB;

    require_once 'config.php';
    require_once 'core/Controller.php';
    require_once 'Functions.php';

    $dirs = [
        "app/core/",
        "app/core/acl/",
        "app/controllers/",
        "app/models/",
        "app/models/forums/",
        "app/plugins/",
        "app/plugins/discord/",
    ];

    foreach($dirs as $dir) {
        foreach (glob($dir.'*.php') as $filename) {
            include_once(''.$filename.'');
        }
    }

    $db = new DB;

    $db->addConnection([
        "driver"   => "mysql",
        "host"     => MYSQL_HOST,
        "database" => MYSQL_DATABASE,
        "username" => MYSQL_USERNAME,
        "password" => MYSQL_PASSWORD,
    ]);

    $db->setAsGlobal();
    $db->bootEloquent();
