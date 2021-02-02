<?php
class ErrorsController extends Controller {

    public function index() {
        return true;
    }

    public function show404() {
        $turbos = Turbos::select([
            'turbos.id',
            'servers.title',
            'servers.website',
            'servers.discord_link',
            'servers.banner_url'
        ])
        ->where('expires', '>', time())
        ->where('servers.banner_url', '!=', null)
        ->where('servers.website', '!=', null)
        ->leftJoin("servers", "servers.id", "=", "turbos.server_id")
        ->orderBy("started", "ASC")
        ->get();

        
    $this->set("turbos", $turbos);
        return true;
            }

    public function show401() {
        $turbos = Turbos::select([
            'turbos.id',
            'servers.title',
            'servers.website',
            'servers.discord_link',
            'servers.banner_url'
        ])
        ->where('expires', '>', time())
        ->where('servers.banner_url', '!=', null)
        ->where('servers.website', '!=', null)
        ->leftJoin("servers", "servers.id", "=", "turbos.server_id")
        ->orderBy("started", "ASC")
        ->get();

        
    $this->set("turbos", $turbos);
        return true;
    }

    public function show500() {
        $turbos = Turbos::select([
            'turbos.id',
            'servers.title',
            'servers.website',
            'servers.discord_link',
            'servers.banner_url'
        ])
        ->where('expires', '>', time())
        ->where('servers.banner_url', '!=', null)
        ->where('servers.website', '!=', null)
        ->leftJoin("servers", "servers.id", "=", "turbos.server_id")
        ->orderBy("started", "ASC")
        ->get();

        
    $this->set("turbos", $turbos);
        return true;
    }

    public function missing() {
        $turbos = Turbos::select([
            'turbos.id',
            'servers.title',
            'servers.website',
            'servers.discord_link',
            'servers.banner_url'
        ])
        ->where('expires', '>', time())
        ->where('servers.banner_url', '!=', null)
        ->where('servers.website', '!=', null)
        ->leftJoin("servers", "servers.id", "=", "turbos.server_id")
        ->orderBy("started", "ASC")
        ->get();

        
    $this->set("turbos", $turbos);
        return true;
    }

}
?>
