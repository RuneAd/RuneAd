# RuneAd
A new, free, and open source toplist project.

Prerequisites:
* PHP 7.2+ with mbstring and curl
* MySQL 5.6+
* Composer

To install dependencies:
```composer install```

Copy file `config.sample.php` to `config.php` in the app folder.
Edit `config.php` with appropriate settings.

A file and folder needs to have read/write access, so chmod them to 777.
This is for cron logs, and banner uploads.
```
chmod 777 public/img/banners
chmod 777 crons/cron.log
chmod -R 777 app/cache
```

If install properly you should now have a toplist ready to go!
