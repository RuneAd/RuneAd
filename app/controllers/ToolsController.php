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

        foreach($results['items'] as $item) {
            if (!file_exists('public/img/items/'.$item['id'].'.png')) {
                $this->getImage($item['id']);
            }
        }

        $this->set("results", $results);
    }

    private function getItemList() {
        $cache    = new Cache("osrs-item-db", 86400);
        $itemList = $cache->get();

        if (!$itemList) {
            $itemList = array_values($this->getFile());

            if (!$itemList) {
                return $cache->getData();
            }

            $cache->save($itemList);
        }

        return $itemList;
    }

    private function getFile() {
        try {
            $client = new GuzzleHttp\Client();
            $resp   = $client->get('https://www.osrsbox.com/osrsbox-db/items-summary.json');
            return array_values(json_decode($resp->getBody(), true));
        } catch(Exception $e) {
            return null;
        }
    }

    public function getImage($itemId) {
        try {
            $client = new GuzzleHttp\Client();
            $resp   = $client->get("https://www.osrsbox.com/osrsbox-db/items-icons/{$itemId}.png", [
                'save_to' => 'public/img/items/'.$itemId.'.png'
            ]);
            return true;
        } catch(Exception $e) {
            return null;
        }
    }

}
