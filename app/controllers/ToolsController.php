<?php
use Fox\Paginator;

class ToolsController extends Controller {

    public function itemdb() {

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
