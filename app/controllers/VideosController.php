<?php
class VideosController extends Controller {

    public function index() {

     }

     public function post($postId) {
        $post = Videos::select("*")
            ->where("id", $postId)
            ->leftJoin("users", "users.user_id", "=", "videos.author_id")
            ->first();

        if (!$post) {
            $this->setView("errors/show404");
            return false;
        }

        $canEdit = $this->user != null && ($this->user->isRole(['owner']) 
            || $post->author_id == $this->user->user_id);

        $this->set("post", $post);
        $this->set("page_title", $post->title);

        if ($post->meta_tags) {
            $this->set("meta_tags", implode(",", json_decode($post->meta_tags, true)));
        }

        $this->set("can_edit", $canEdit);
        $this->set("meta_info", $post->meta_description);
        return true;
    }

     public function add() {
        $csrf = new AntiCSRF;

        $canPost = $this->user != null && $this->user->isRole([
            'owner', 'blogger', 'moderator', 'respected', 'youtuber', 'administrators', 'veteran'
        ]);

        if (!$canPost) {
            $this->setView("errors/show401");
            return false;
        }

        if ($this->request->isPost() && $csrf->isValidPost()) {
            $data = [
                'title'       => $this->request->getPost("title", "string"),
                "category"    => strtolower($this->request->getPost("category", "string")),
                'author_id'   => $this->user->user_id,
                'meta_info'   => $this->request->getPost("meta_info", "string"),
                'content'     => $this->purify($this->request->getPost("info")),
                'date_posted' => time()
            ];

            $validation = Videos::validate($data);

            if ($validation->fails()) {
                $errors = $validation->errors();
                $this->set("errors", $errors->firstOfAll());
            } else {
                $post = Videos::create($data);
                $seo_title = Functions::friendlyTitle($post['id'].'-'.$post['title']);
                $this->redirect("videos/post/".$seo_title);
                exit;
            }
        }

        $this->set("csrf_token", $csrf->getToken());
    }
     
}
?>