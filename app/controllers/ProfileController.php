<?php

class ProfileController extends Controller {

    public function index() {
        $servers = Servers::where('owner', $this->user->user_id)->get();
        $roles = implode(", ", json_decode($this->user->roles, true));
        
        $this->set("roles", $roles);
        $this->set("servers", $servers);
        return true;
    }

    public $access =  [
        'login_required' => true,
        'roles'  => ['member', 'moderator', 'admin']
    ];

    public function beforeExecute() {
        return parent::beforeExecute();
    }

}