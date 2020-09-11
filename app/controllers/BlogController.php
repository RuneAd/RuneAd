<?php
class BlogController extends Controller {

    public function index() {

        $data = [
            'blog' => [
                'total' => Blog::count()
            ]
            ];

    }

    public function add() {
        $client = new GuzzleHttp\Client();

        if ($this->request->isPost()) {
            $data = [
                'title'         => $this->request->getPost("title", "string"),
                'owner'         => $this->user->user_id,
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

        if (!move_uploaded_file($file['tmp_name'], 'public/img/blog/images/'.$newname)) {
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
    
}
