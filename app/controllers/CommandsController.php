<?php
class CommandsController extends Controller {

    public function index() {
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

    public function owner() {
        return true;
    }

    public function admin() {
        return true;
    }

    public function moderator() {
        return true;
    }

    public function donator() {
        return true;
    }

    public function player() {
        return true;
    }
     
}
?>