<?php
use Fox\CSRF;
use Rakit\Validation\Validator;
use Fox\Request;

class BlogController extends Controller {

    public function index(){

    }

    public function post($id) {
        $blog   = Blog::getBlog($id);

        if (!$blog) {
            $this->setView("errors/show404");
            return false;
        }

        $seo = Functions::friendlyTitle($blog->id.'-'.$blog->title);

        $blogs = Blog::select([
            'blogs.id',
            'blogs.title',
            'blogs.website',
            'blogs.discord_link',
            'blogs.banner_url'
        ])
        ->leftJoin("blogs", "blogs.id", "=", "blogs.id")
        ->orderBy("started", "ASC")
        ->get();

    $this->set("data", $data);
    $this->set("servers", $servers);
    $this->set("revisions", $revisions);
    $this->set("sponsors", $sponsors);
    return true;

        $seo = Functions::friendlyTitle($blog->id.'-'.$blog->title);

        if ($blog->meta_tags)
            $this->set("meta_tags", implode(',',json_decode($blog->meta_tags, true)));

        $this->set("meta_info", $this->filter($blog->meta_info));
        $this->set("seo_link", $seo);

        $body = str_replace("<img src", "<img data-src", $blog->description);
        $body = str_replace("\"img-fluid\"", "\"lazy img-fluid\"", $body);
        $this->set("description", $body);
        return true;
    }

    public function add() {
        $client = new GuzzleHttp\Client();

        if ($this->request->isPost()) {
            $data = [
                'owner'         => $this->user->user_id,
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

        $this->set("page_title", "Add Blog Post");
        $revisions = Revisions::where('visible', 1)->get();
        $this->set("revisions", $revisions);
    	return true;
    }

    public function upload() {
        $file = $_FILES['image'];

        $dims = getimagesize($file['tmp_name']);
        if ($dims === false) {
            return [
                'success' => true,
                'message' => 'File must be an image.'
            ];
        }

        $mimes = ['jpg' => 'image/jpeg', 'gif' => 'image/gif', 'png' => 'image/png'];

        $type   = mime_content_type($file['tmp_name']);
        $ext    = pathinfo($file['name'])['extension'];
        $size   = $file['size'];

        $width  = $dims[0];
        $height = $dims[1];

        $maxDims = [468, 60];
        $maxSize = (1024 * 1024 * 5);

        if (!in_array($type, array_values($mimes))) {
            return [
                'success' => true,
                'message' => 'Invalid file mime type.'
            ];
        }

        if (!in_array($ext, array_keys($mimes))) {
            return [
                'success' => true,
                'message' => 'Invalid file extension. Allowed: '.implode(', ', array_keys($mimes))
            ];
        }

        if ($size > $maxSize) {
            return [
                'success' => true,
                'message' => "Image can not exceed ".(($maxSize/1024)/1024)."MB."
            ];
        }

        if ($width != $maxDims[0] && $height != $maxDims[1]) {
            return [
                'success' => true,
                'message' => "Image must be $maxDims[0]px x $maxDims[1]px."
            ];
        }

        $newname = md5($file['name'] . microtime()).'.'.$ext;

        if (!move_uploaded_file($file['tmp_name'], 'public/img/blog/'.$newname)) {
            return [
                'success' => true,
                'message' => 'Failed uploading file...'
            ];
        }

        return [
            'success' => true,
            'message' => $newname,
        ];
    }

    public $access =  [
        'login_required' => true,
        'roles'  => ['member', 'moderator', 'admin']
    ];

    public function beforeExecute() {
        if ($this->getActionName() == "upload") {
            $this->request = Request::getInstance();
            $this->disableView(true);
            return true;
        }

        return parent::beforeExecute();;
    }

}
