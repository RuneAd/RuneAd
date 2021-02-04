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
        }
        
        $data = [
            'servers' => [
                'total' => Servers::count()
            ]
            ];

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

        $this->set("data", $data);
        $this->set("servers", $servers);
        $this->set("revisions", $revisions);
        $this->set("sponsors", $sponsors);
        $this->set("server_count", $servers->total());
    	return true;
    }


    public function beta($rev = null, $page = 1) {
        
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
        }
        
        $data = [
            'servers' => [
                'total' => Servers::count()
            ]
            ];

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

        $this->set("data", $data);
        $this->set("servers", $servers);
        $this->set("revisions", $revisions);
        $this->set("sponsors", $sponsors);
        $this->set("server_count", $servers->total());
    	return true;
    }

    public function staffpanel($rev = null, $page = 1) {
        $servers = Servers::where('owner', $this->user->user_id)->get();
        $roles = implode(", ", json_decode($this->user->roles, true));

        $idArr = array_column($servers->toArray(), "id");
        $ids   = implode(",", $idArr);

        if (count($servers) > 0) {
            $votes = Votes::
                select("*")
                ->whereRaw("server_id IN (".$ids.")")
                ->leftJoin("servers", "servers.id", "=", "votes.server_id")
                ->orderByRaw("votes.voted_on DESC")
                ->get();
        }

        $votesArr = [];

        foreach ($idArr as $id) {
            $votesArr[$id] = [
                '1hour'    => 0,
                '1day'     => 0,
                '7days'    => 0,
                '30days'   => 0,
                '60days'   => 0,
                'lifetime' => 0,
            ];
        }

        if (count($servers) > 0) {
            foreach($votes as $vote) {
                $vote_time = $vote->voted_on;
                $timeDiff  = time() - $vote_time;

                if ($timeDiff <= 3600) {
                    $votesArr[$vote->server_id]['1hour']++;
                }
                if ($timeDiff <= 86400) {
                    $votesArr[$vote->server_id]['1day']++;
                }
                if ($timeDiff <= 604800) {
                    $votesArr[$vote->server_id]['7days']++;
                }
                if ($timeDiff <= 2592000) {
                    $votesArr[$vote->server_id]['30days']++;
                }
                if ($timeDiff <= 10368000) {
                    $votesArr[$vote->server_id]['60days']++;
                }
                $votesArr[$vote->server_id]['lifetime']++;
            }
        }
        $this->set("roles", $roles);
        $this->set("servers", $servers);
        $this->set("voteData", $votesArr);
        return true;
    }


    public function details($id) {
        $server   = Servers::getServer($id);

        if (!$server) {
            $this->setView("errors/show404");
            return false;
        }

        $seo = Functions::friendlyTitle($server->id.'-'.$server->title);

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
