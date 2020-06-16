<?php
use Fox\Request;

class PremiumController extends Controller {

    public function index() {
        $premium = array_chunk(Premium::get()->toArray(), 2);

        if ($this->user) {
            $servers = Servers::getServersByOwner($this->user->user_id);
            $this->set("servers", $servers);
        }

        $this->set("cheap", $premium[0]);
        $this->set("expensive", $premium[1]);
        return true;
    }

    public function button() {
        $packageId = $this->request->getPost("package", "int");
        $serverId  = $this->request->getPost("server", "int");

        $package  = Premium::where('id', $packageId)->first();
        
        if (!$package) {
            return [
                'success' => false,
                'message' => "Package not found."
            ];
        }

        $server = Servers::where("owner", $this->user->user_id)
            ->where("id", $serverId)
            ->first();

        if (!$server) {
            return [
                'success' => false,
                'message' => "Server not found."
            ];
        }

        return [
            "success" => true,
            "message" => $this->getViewContents("premium/button", [
                "package" => $package,
                "server"  => $server
            ])
        ];
    }

    public function verify() {
        $order_info = $this->request->getPost("orderDetails");
        $server_id  = $this->request->getPost("server_id", "int");

        if (empty($order_info)) {
            return [
                'success' => false,
                'message' => $this->getViewContents("premium/error", [
                    "message" => "No data was received."
                ])
            ];
        }

        $buyer     = $order_info['payer'];
        $firstName = $buyer['name']['given_name'];
        $lastName  = $buyer['name']['surname'];

        $item    = $order_info['purchase_units'][0]['items'][0];
        $name    = $this->filter($item['name'], 'string');
        $sku     = $this->filter($item['sku'], 'int');
        $amount  = $this->filter($item['quantity'], 'int');
        $value   = $this->filter($item['unit_amount']['value'], 'float');

        $status  = $order_info['status'];

        if ($status != "APPROVED") {
            return [
                'success' => false,
                'message' => $this->getViewContents("premium/error", [
                    "message" => "Payment was not approved."
                ])
            ];
        }

        $package = Premium::where('id', $sku)->first();
        
        if (!$package) {
            return [
                'success' => false,
                'message' => $this->getViewContents("premium/error", [
                    "message" => "Package could not be loaded."
                ])
            ];
        }

        if ($package->price != $value) {
            return [
                'success' => false,
                'message' => $this->getViewContents("premium/error", [
                    "message" => "Invalid purchase price!"
                ])
            ];
        }

        $server = Servers::where("owner", $this->user->user_id)
            ->where("id", $server_id)
            ->first();

        if (!$server) {
            return [
                'success' => false,
                'message' => $this->getViewContents("premium/error", [
                    "message" => "Could not find your server!"
                ])
            ];
        }
        
        $token = Functions::generateString(15);
        $this->session->set("pp_key", $token);

        return [
            'success' => true,
            'message' => $this->request->getPost(),
            'token'   => $this->session->get("pp_key")
        ];
    }

    public function process() {
        $pp_key   = $this->request->getPost("pp_key", "string");
        $sess_key = $this->session->get("pp_key");
        
        if (!$pp_key || !$sess_key || $pp_key != $sess_key) {
            return [
                'success' => false,
                'message' => "Invalid Request."
            ];
        }

        $this->session->delete("pp_key");
        
        $order_info = $this->request->getPost("orderDetails");
        $server_id  = $this->request->getPost("server_id", "int");

        if (empty($order_info)) {
            return [
                'success' => false,
                'message' => $this->getViewContents("premium/error", [
                    "message" => "No data was received"
                ])
            ];
        }

        $buyer     = $order_info['payer'];
        $firstName = $buyer['name']['given_name'];
        $lastName  = $buyer['name']['surname'];
        $email     = $buyer['email_address'];

        $item    = $order_info['purchase_units'][0]['items'][0];
        $name    = $this->filter($item['name'], 'string');
        $sku     = $this->filter($item['sku'], 'int');
        $amount  = $this->filter($item['quantity'], 'int');

        $capture = $order_info['purchase_units'][0]['payments']['captures'][0];
        $paid    = $this->filter($capture['amount']['value'], 'float');
        $cap_id  = $order_info['id'];
        $transId = $capture['id'];
        $status  = $order_info['status'];

        if ($status != "COMPLETED") {
            return [
                'success' => false,
                'message' => $this->getViewContents("premium/error", [
                    "message" => "Payment was not complete."
                ])
            ];
        }

        $package = Premium::where('id', $sku)->first();
        
        if (!$package) {
            return [
                'success' => false,
                'message' => $this->getViewContents("premium/error", [
                    "message" => "Package could not be found"
                ])
            ];
        }

        $payment = new Payments;

        $payment->fill([
            'user_id' => $this->user->user_id,
            'username' => $this->user->username,
            'ip_address' => $this->request->getAddress(),
            'sku' => $package->id,
            'item_name' => $package->title,
            'email' => $email,
            'status' => $status,
            'paid' => $paid,
            'quantity' => $amount,
            'currency' => 'USD',
            'transaction_id' => $transId,
            'date_paid' => time(),
        ])->save();

        if ($package->price != $paid) {
            return [
                'success' => false,
                'message' => $this->getViewContents("premium/error", [
                    "message" => "Invalid purchase price"
                ])
            ];
        }

        $expires = $package->duration;

        $server = Servers::where("owner", $this->user->user_id)
            ->where("id", $server_id)
            ->first();

        if (!$server) {
            return [
                'success' => false,
                'message' => $this->getViewContents("premium/error", [
                    "message" => "Could not find your server!"
                ])
            ];
        }

        if ($package->level > $server->premium_level) {
            $server->premium_level = $package->level;
        }

        if ($server->premium_expires > time()) {
            $server->premium_expires = $server->premium_expires + $package->duration;
        } else {
            $server->premium_expires = time() + $package->duration;
        }

        if (!$server->save()) {
            return [
                'success' => false,
                'message' => $this->getViewContents("premium/error", [
                    "message" => "Could not update server."
                ])
            ];
        }
        
        return [
            'success' => true,
            'message' => $this->getViewContents("premium/success", [
                "package" => $package,
                "server"  => $server
            ])
        ];
    }

    public $access =  [
        'login_required' => true,
        'roles'  => ['member', 'moderator', 'admin']
    ];

    public function beforeExecute() {
        if ($this->getActionName() == "button" || $this->getActionName() == "process" || $this->getActionName() == "verify") {
            $this->disableView(true);
        } else {
            $this->access = [
                'login_required' => false,
                'roles'  => []
            ];
        }

        return parent::beforeExecute();
    }
}