<?php
class BlogController extends Controller {

    public function index() {

    }

    public function add() {
        $client = new GuzzleHttp\Client();

        if ($this->request->isPost()) {
            $data = [
                'title'         => $this->request->getPost("title", "string"),
                'meta_tags'     => explode(",", $this->request->getPost("meta_tags", 'string')),
                'meta_info'     => $this->request->getPost("meta_info", "string"),
                'description'   => $this->purify($this->request->getPost("info")),
                'date_created'  => time()
            ];

            $validation = Blog::validate($data);

            if ($validation->fails()) {
                $errors = $validation->errors();
                $this->set("errors", $errors->firstOfAll());
            } else {
                $data['meta_tags'] = json_encode($data['meta_tags'], JSON_UNESCAPED_SLASHES);
                $create = Blog::create($data);
            }
        }

        $this->set("page_title", "Add Post");
        $revisions = Revisions::where('visible', 1)->get();
        $this->set("revisions", $revisions);
    	return true;
    }
    
}
