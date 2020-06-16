<?php
use Fox\CSRF;
use Fox\Paginator;
use Fox\Request;
use Fox\Cookies;

use Illuminate\Database\Capsule\Manager as DB;

class IndexController extends Controller {

    public function index($rev = null, $page = 1) {
        $revisions = Revisions::where('visible', 1)->get();

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
            $this->set("page_title", "Servers");
        }

        $this->set("servers", $servers);
        $this->set("revisions", $revisions);
        
    	return true;
    }

    public function details($id, $rate = "month") {
        $server   = Servers::getServer($id);

        if (!$server) {
            $this->setView("errors/show404");
            return false;
        }
        
        if ($rate == "year") {
            $start = time() - (60 * 60 * 24 * 365);
        } else if ($rate == "month") {
            $start = time() - (60 * 60 * 24 * 30);
        } else if ($rate == "week") {
            $start = time() - (60 * 60 * 24 * 7);
        } else {
            $rate  = "week";
            $start = time() - (60 * 60 * 24 * 7);
        }



        $cache = new Cache('servers/server_'.$server->id.'_'.$rate.'_chart', 0);
        $cacheData = $cache->get();

        if (!$cacheData) {
            $votes = Votes::where("voted_on", ">", $start)
                ->where("server_id", $server->id)
                ->orderBy("voted_on", "ASC")->get();

            $cacheData = [];

            foreach ($votes as $vote) {
                $date  = date("m-d-y", $vote->voted_on);
                $votes = 0;

                if (isset($cacheData[$date])) {
                    $cacheData[$date] += 1;
                } else {
                    $cacheData[$date] = 1;
                }
            }

            $cache->save($cacheData);
        } else {
            $this->set("time_left", $cache->getTimeLeft());
        }

        $columns = '\''.implode('\',\'', array_keys($cacheData)).'\'';
        $data    = implode(',', array_values($cacheData));
        $seo     = Functions::friendlyTitle($server->id.'-'.$server->title);

        $this->set("rate", $rate);
        $this->set("chart_columns", $columns);
        $this->set("chart_data", $data);
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