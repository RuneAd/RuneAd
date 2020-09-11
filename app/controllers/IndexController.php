<?php
use Fox\CSRF;
use Fox\Paginator;
use Fox\Request;
use Fox\Cookies;

use Illuminate\Database\Capsule\Manager as DB;

class IndexController extends Controller {

    public function index($rev = null, $page = 1) {
        $revisions = Revisions::where('visible', 1)->get();

        $data = [
            'users' => [
                'total' => Users::count()
                ]
            ];

        if ($rev != null) {
            $revision = Revisions::where('revision', $rev)->first();

            if (!$revision) {
                $this->setView("errors/show404");
                return false;
            }

            $servers = Servers::getByRevision($revision, $page);

            $this->set("page_title", "{$revision->revision} Servers");
            $this->set("meta_info", "{$revision->revision} Runescape private servers.");
            $this->set("revision", $revision);
        } else {
            $servers = Servers::getAll($page);
            $this->set("page_title", "RSPS Toplist");
        }

        $sponsors = Sponsors::select([
                'sponsors.id',
                'servers.title',
                'servers.website',
                'servers.discord_link',
                'servers.banner_url'
            ])
            ->where('expires', '>', time())
            ->where('servers.banner_url', '!=', null)
            ->where('servers.website', '!=', null)
            ->leftJoin("servers", "servers.id", "=", "sponsors.server_id")
            ->orderBy("started", "ASC")
            ->get();

        $this->set("servers", $servers);
        $this->set("revisions", $revisions);
        $this->set("sponsors", $sponsors);
    	return true;
    }

    public function details($id, $rate = "month") {
        $server   = Servers::getServer($id);

        if (!$server) {
            $this->setView("errors/show404");
            return false;
        }

        $seo = Functions::friendlyTitle($server->id.'-'.$server->title);

        $this->set("rate", $rate);
        $this->set("server", $server);
        $this->set("purifier", $this->getPurifier());
        $this->set("page_title", $server->title);

        $seo = Functions::friendlyTitle($server->id.'-'.$server->title);

        if ($server->meta_tags)
            $this->set("meta_tags", implode(',',json_decode($server->meta_tags, true)));

        $this->set("meta_info", $this->filter($server->meta_info));
        $this->set("seo_link", $seo);

        $body = str_replace("<img src", "<img data-src", $server->description);
        $body = str_replace("\"img-fluid\"", "\"lazy img-fluid\"", $body);
        $this->set("description", $body);
        return true;
    }

    public function out($serverId) {
        $server = Servers::getServer($id);

        if (!$server) {
            $this->setView("errors/show404");
            return false;
        }

        $website = $server->website;

        if (!$website) {
            $this->redirect("");
        }

        $this->redirect($website, false);
        exit;
    }

    public function logout() {
        $token = $this->cookies->get("access_token");

        try {
            $discord = new Discord($token);
            $discord->revokeAccess();
        } catch (Exception $e) {

        }

        $this->cookies->delete("access_token");
        $this->redirect("");
        exit;
    }

    public $access =  [
        'login_required' => false,
        'roles'  => []
    ];

    public function beforeExecute() {
        if ($this->getActionName() == "logout") {
            $this->request = Request::getInstance();
            $this->cookies = Cookies::getInstance();
            $this->disableView(false);
            return true;
        }

        return parent::beforeExecute();
    }

}
