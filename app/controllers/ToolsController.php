<?php
use Fox\Paginator;

class ToolsController extends Controller {

    public function index() {
        return true;
    }

    public function itemdb() {
        return true;
    }

    public function map() {
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

    public function commands() {
        return true;
    }

    public function analytics() {
        return true;
    }

    public function xptable() {
        return true;
    }

    public function servers() {

        Paginator::currentPageResolver(function() use ($page) {
            return $page;
        });

        return Servers::where('website', '!=', null)
            ->select(
                'id', 
                'title', 
                'revision',
                'votes',
                'banner_url', 
                'is_online',
                'premium_expires'
            )
            ->where('title', 'LIKE', '%'.$name.'%')
            ->orWhere('owner', '=', ''.$name.'')
            ->orderBy('id', 'ASC')
            ->paginate(per_page);
    }

    public function search() {
        $data   = $this->getItemList();
        $search = $this->request->getPost("search", "string");
        $found  = [];

        if ($search != null && $search != '') {
            foreach ($data as $item) {
                $itemName = $item['name'];
                if (stripos(strtolower($itemName), strtolower($search)) !== false) {
                    $found[] = $item;
                }
            }
        } else {
            $found = $data;
        }

        $pageNum   = $this->request->getPost("page", "int");
        $paginator = (new Paginator($found, $pageNum, 20))->paginate();
        $results   = $paginator->getResults();
        $this->set("results", $results);
        return true;
    }

    private function getItemList() {
        $file_name = "app/cache/osrs-item-db.json";

        $file     = file_get_contents($file_name);
        $itemList = json_decode($file, true);

        if (!$itemList) {
            return [];
        }

        return $itemList;
    }

}
