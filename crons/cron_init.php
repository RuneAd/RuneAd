<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    define("DOC_ROOT", __DIR__);
    define("log_file", DOC_ROOT."/cron.log");

    include DOC_ROOT.'/../vendor/autoload.php';
    include DOC_ROOT.'/../app/config.php';
    include 'cron_lock.php';

    require_once DOC_ROOT.'/../app/Functions.php';

    $model_dir = DOC_ROOT."/../app/models/";

    foreach (glob($model_dir.'*.php') as $filename) {
        include_once(''.$filename.'');
    }

    use Illuminate\Database\Capsule\Manager as DB;

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

    function debug($data) {
        $encoded = json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
        echo "<pre>".htmlspecialchars($encoded)."</pre>";
    }

    function writeLog($text) {
        error_log(date('[m-d-y g:i A] ')."[CRON] ".$text.PHP_EOL, 3, log_file);
    }
