<?php
use Illuminate\Pagination\Paginator;

class VideosController extends Controller {

    public function index($category = null, $page = 1) {
        Paginator::currentPageResolver(function() use ($page) {
            return $page;
        });

        if ($category == null) {
            $posts = Videos::select("*")
                ->leftJoin("users", "users.user_id", "=", "videos.author_id")
                ->orderBy("id", "DESC")
                ->paginate(7);
        } else {

            $category = str_replace("-", " ", $category);

            $posts = Videos::select("*")
                ->where("category", "=", $category)
                ->leftJoin("users", "users.user_id", "=", "videos.author_id")
                ->orderBy("id", "DESC")
                ->paginate(7);

            $this->set("category", $this->filter($category));
        }

        $categories = Videos::selectRaw("category as title")->groupBy("category")->get();

        $this->set("posts", $posts);
        $this->set("categories", $categories);
        
        return true;
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
            'owner', 'moderator', 'respected', 'youtuber', 'administrators', 'veteran'
        ]);

        if (!$canPost) {
            $this->setView("errors/show401");
            return false;
        }

        if ($this->request->isPost() && $csrf->isValidPost()) {
            $data = [
                'title'       => $this->request->getPost("title", "string"),
                'date_posted' => time()
            ];

            $validation = Videos::validate($data);

            if ($validation->fails()) {
                $errors = $validation->errors();
                $this->set("errors", $errors->firstOfAll());
            } else {
                $data['meta_tags'] = json_encode($data['meta_tags'], JSON_UNESCAPED_SLASHES);
                $post = Videos::create($data);
                $seo_title = Functions::friendlyTitle($post['id'].'-'.$post['title']);
                $this->redirect("videos/post/".$seo_title);
                exit;
            }
        }

        $this->set("csrf_token", $csrf->getToken());
    }

    public function edit($postId) {
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

        if (!$canEdit) {
            $this->setView("errors/show401");
            return false;
        }

        $csrf = new AntiCSRF;

        if ($this->request->isPost() && $csrf->isValidPost()) {
            $data = [
                'title'       => $this->request->getPost("title", "string"),
            ];

            $validation = Videos::validate($data);

            if ($validation->fails()) {
                $errors = $validation->errors();
                $this->set("errors", $errors->firstOfAll());
            } else {
                $data['meta_tags'] = json_encode($data['meta_tags'], JSON_UNESCAPED_SLASHES);
                
                $post->fill($data);
                $post->save();

                $seo_title = Functions::friendlyTitle($post['id'].'-'.$post['title']);
                $this->redirect("videos/post/".$seo_title);
                exit;
            }
        }

        $this->set("post", $post);

        if ($post->meta_tags) {
            $this->set("meta_tags", implode(",", json_decode($post->meta_tags, true)));
        }

        $this->set("can_edit", $canEdit);
        $this->set("csrf_token", $csrf->getToken());
        return true;
    }

    public function delete($postId) {
        $post = Videos::select("*")
        ->where("id", $postId)
        ->leftJoin("users", "users.user_id", "=", "videos.author_id")
        ->first();

        if (!$post) {
            $this->setView("errors/show404");
            return false;
        }

        $canDelete = $this->user != null && $this->user->isRole(['owner']);

        if (!$canDelete) {
            $this->setView("errors/show401");
            return false;
        }

        $csrf = new AntiCSRF;

        if ($this->request->isPost() && $csrf->isValidPost()) {
            $post->delete();
            $this->redirect("videos");
            exit;
        }

        $this->set("post", $post);
        $this->set("csrf_token", $csrf->getToken());
        return true;
    }

}