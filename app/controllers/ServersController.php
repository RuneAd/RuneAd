<?php
use Fox\CSRF;
use Rakit\Validation\Validator;
use Fox\Request;

class ServersController extends Controller {

    public function add() {
        $client = new GuzzleHttp\Client();

        if ($this->request->isPost()) {
            $data = [
                'owner'         => $this->user->user_id,
                'revision'      => $this->request->getPost("revision", "string"),
                'title'         => $this->request->getPost("title", "string"),
                'server_port'   => $this->request->getPost("server_port", "int"),
                'server_ip'     => $this->request->getPost("server_ip", "string"),
                'website'       => $this->request->getPost("website", "url"),
                'callback_url'  => $this->request->getPost("callback_url", "url"),
                'discord_link'  => $this->request->getPost("discord_link", "url"),
                'banner_url'    => $this->request->getPost("banner_url", "string"),
                'meta_tags'     => explode(",", $this->request->getPost("meta_tags", 'string')),
                'meta_info'     => $this->request->getPost("meta_info", "string"),
                'description'   => $this->purify($this->request->getPost("info")),
                'date_created'  => time()
            ];

            $validation = Servers::validate($data);

            if ($validation->fails()) {
                $errors = $validation->errors();
                $this->set("errors", $errors->firstOfAll());
            } else {
                $data['meta_tags'] = json_encode($data['meta_tags'], JSON_UNESCAPED_SLASHES);
                $create = Servers::create($data);

                if ($create) {
                    if ($create['server_ip'] && $create['server_port']) {
                        $api_url  = "http://api.rune-nexus.com/ping";

                        $endpoint = $api_url."?address=".$data['server_ip']."&port=".$data['server_port'];
                        $res      = json_decode($client->request('GET', $endpoint)->getBody(), true);

                        $success = $res['success'];

                        $create->is_online = $success;
                        $create->ping = $success ? $res['ping'] : -1;
                        $create->save();
                    }

                    $seo  = Functions::friendlyTitle($create->id.'-'.$create->title);
                    $link = "[{$data['title']}](https://rune-nexus.com/details/{$seo})";

                    (new DiscordMessage([
                        'channel_id' => '607320502268330016',
                        'title'      => 'New Server!',
                        'message'    => "{$this->user->username} has listed a new server: $link",
                    ]))->send();

                    $this->redirect("details/".$seo);
                    exit;
                }
            }
        }

        $this->set("page_title", "Add Server");
        $revisions = Revisions::where('visible', 1)->get();
        $this->set("revisions", $revisions);
    	return true;
    }

    public function edit($id) {
        $server = Servers::getServer($id);

        if (!$server) {
            $this->setView("errors/show404");
            return false;
        }

        /*if ($server->owner != $this->user->user_id) {
            $this->setView("errors/show401");
            return false;
        }*/

        if ($this->request->isPost()) {
            $data = [
                'revision'      => $this->request->getPost("revision", "string"),
                'title'         => $this->request->getPost("title", "string"),
                'server_port'   => $this->request->getPost("server_port", "int"),
                'server_ip'     => $this->request->getPost("server_ip", "string"),
                'website'       => $this->request->getPost("website", "url"),
                'callback_url'  => $this->request->getPost("callback_url", "url"),
                'discord_link'  => $this->request->getPost("discord_link", "url"),
                'banner_url'    => $this->request->getPost("banner_url", "string"),
                'meta_tags'     => explode(",", $this->request->getPost("meta_tags", 'string')),
                'meta_info'     => $this->request->getPost("meta_info", "string"),
                'description'   => $this->purify($this->request->getPost("info")),
            ];

            $validation = Servers::validate($data);

            if ($validation->fails()) {
                $errors = $validation->errors();
                $this->set("errors", $errors->firstOfAll());
            } else {
                $data['meta_tags'] = json_encode($data['meta_tags'], JSON_UNESCAPED_SLASHES);

                $server->fill($data);
                $saved = $server->save();

                if ($saved) {
                    $seo  = Functions::friendlyTitle($server->id.'-'.$server->title);
                    $link = "[{$data['title']}](https://runead.com/details/{$seo})";

                    (new DiscordMessage([
                        'channel_id' => '610038623743639559',
                        'title'      => 'Server Update',
                        'message'    => "{$this->user->username} has updated their listing for $link",
                    ]))->send();

                    $this->redirect("details/".$seo);
                    exit;
                }
            }
        }

        $revisions = Revisions::where('visible', 1)->get();

        $this->set("revisions", $revisions);
        $this->set("server", $server);
        $this->set("page_title", "Edit Server");

        $this->set("seo_link", Functions::friendlyTitle($server->id.'-'.$server->title));
        if ($server->meta_tags)
            $this->set("server_tags", implode(',',json_decode($server->meta_tags, true)));
        return true;
    }

    public function upload() {
        $file = $_FILES['image'];
        $dims = getimagesize($file['tmp_name']);

        if ($dims === false) {
            return [
                'success' => false,
                'message' => 'File must be an image.'
            ];
        }

        $mimes = ['jpg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif'];

        $type   = mime_content_type($file['tmp_name']);
        $ext    = pathinfo($file['name'])['extension'];
        $size   = $file['size'];

        $width  = $dims[0];
        $height = $dims[1];

        $maxDims = [468, 60];
        $maxSize = (1024 * 1024 * 5);

        if (!in_array($type, array_values($mimes))) {
            return [
                'success' => false,
                'message' => 'Invalid file mime type.'
            ];
        }

        if (!in_array($ext, array_keys($mimes))) {
            return [
                'success' => false,
                'message' => 'Invalid file extension. Allowed: '.implode(', ', array_keys($mimes))
            ];
        }

        if ($size > $maxSize) {
            return [
                'success' => false,
                'message' => "Image can not exceed ".(($maxSize/1024)/1024)."MB."
            ];
        }

        if ($width != $maxDims[0] && $height != $maxDims[1]) {
            return [
                'success' => false,
                'message' => "Image must be $maxDims[0]px x $maxDims[1]px."
            ];
        }

        $newname = md5($file['name'] . microtime()).'.'.$ext;

        if (!move_uploaded_file($file['tmp_name'], 'public/img/banners/'.$newname)) {
            return [
                'success' => false,
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
