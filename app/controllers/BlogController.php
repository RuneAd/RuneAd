<?php
use Illuminate\Pagination\Paginator;

class BlogController extends Controller {

    public function index($category = null, $page = 1) {
        Paginator::currentPageResolver(function() use ($page) {
            return $page;
        });

        if ($category == null) {
            $posts = Blog::select("*")
                ->leftJoin("users", "users.user_id", "=", "blog.author_id")
                ->paginate(15);
        } else {
            $posts = Blog::select("*")
                ->where("category", "=", $category)
                ->leftJoin("users", "users.user_id", "=", "blog.author_id")
                ->paginate(15);

            $this->set("category", $this->filter($category));
        }

        $categories = Blog::selectRaw("category as title")->groupBy("category")->get();

        $this->set("posts", $posts);
        $this->set("categories", $categories);
        return true;
    }

    public function post($postId) {
        $post = Blog::select("*")
            ->where("id", $postId)
            ->leftJoin("users", "users.user_id", "=", "blog.author_id")
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
            'owner', 'blog author'
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
                'meta_tags'   => explode(",", $this->request->getPost("meta_tags", 'string')),
                'meta_info'   => $this->request->getPost("meta_info", "string"),
                'content'     => $this->purify($this->request->getPost("info")),
                'date_posted' => time()
            ];

            $validation = Blog::validate($data);

            if ($validation->fails()) {
                $errors = $validation->errors();
                $this->set("errors", $errors->firstOfAll());
            } else {
                $data['meta_tags'] = json_encode($data['meta_tags'], JSON_UNESCAPED_SLASHES);
                $post = Blog::create($data);
                $seo_title = Functions::friendlyTitle($post['id'].'-'.$post['title']);
                $this->redirect("blog/post/".$seo_title);
                exit;
            }
        }

        $this->set("csrf_token", $csrf->getToken());
    }

    public function edit($postId) {
        $post = Blog::select("*")
            ->where("id", $postId)
            ->leftJoin("users", "users.user_id", "=", "blog.author_id")
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
                "category"    => strtolower($this->request->getPost("category", "string")),
                'meta_tags'   => explode(",", $this->request->getPost("meta_tags", 'string')),
                'meta_description' => $this->request->getPost("meta_info", "string"),
                'content'     => $this->purify($this->request->getPost("info")),
            ];

            $validation = Blog::validate($data);

            if ($validation->fails()) {
                $errors = $validation->errors();
                $this->set("errors", $errors->firstOfAll());
            } else {
                $data['meta_tags'] = json_encode($data['meta_tags'], JSON_UNESCAPED_SLASHES);
                
                $post->fill($data);
                $post->save();

                $seo_title = Functions::friendlyTitle($post['id'].'-'.$post['title']);
                $this->redirect("blog/post/".$seo_title);
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
        $post = Blog::select("*")
        ->where("id", $postId)
        ->leftJoin("users", "users.user_id", "=", "blog.author_id")
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
            $this->redirect("blog");
            exit;
        }

        $this->set("post", $post);
        $this->set("csrf_token", $csrf->getToken());
        return true;
    }

}