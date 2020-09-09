<?php

class ProfileController extends Controller {

  public function stats() {
    $servers = Servers::where('owner', $this->user->user_id)->get();
    $roles = implode(", ", json_decode($this->user->roles, true));


    $this->set("roles", $roles);
    $this->set("servers", $servers);
    return true;

    $thisMonth = strtotime(date("Y-m-01 00:00:00"));
    $lastMonth = strtotime("first day of last month");
    $lastWeek = strtotime("-1 week +1 day");
    $day = strtotime("today");
    $hour = strtotime("-1 hour");


    $data = [
        'votes' => [
            'total' => Votes::count(),
            'month' => Votes::where("votes", ">=", $thisMonth)->count(),
            'lastmonth' => Votes::where("votes", ">=", $lastMonth)->count(),
            'week' => Votes::where("votes", ">=", $lastWeek)->count(),
            'day' => Votes::where("votes", ">=", $day)->count(),
            'hour' => Votes::where("votes", ">=", $hour)->count()

        ],

    ];

  }

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
