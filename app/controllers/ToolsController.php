<?php
use Fox\Paginator;

class ToolsController extends Controller {

    public function index() {

    }

    public function itemdb() {

    }

    public function map() {

    }

    public function xptable() {

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
