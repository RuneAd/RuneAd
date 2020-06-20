<?php
class PagesController extends Controller {

    public function docs() {

    }

    public function updates() {

    }

    public function terms() {
        
    }

    public function map() {

    }

    public function privacy() {

    }

    public function nyan() {

    }

    public function stats() {
        $servers = Servers::count();
        $votes   = Votes::count();
        $users   = Users::count();

        $this->set("servers", $servers);
        $this->set("votes", $votes);
        $this->set("users", $users);
        return true;
    }

    public static function getChartData($start_date) {
        return Votes::where("voted_on", '>', $start_date)
            ->selectRaw('COUNT(*) AS total')
            ->selectRaw("FROM_UNIXTIME(voted_on, '%m %d') AS time")
            ->groupBy("time")
            ->orderBy("time", 'ASC')
            ->get();
    }

    public function commits() {
        try {
            $client = new GuzzleHttp\Client();

            $commits = $client->request('GET', github['api_url'].github['username']."/".github['repo']."/commits", [
                'headers' => [
                    'Accept' => 'application/json'
                ],
                'auth' => [
                    github['client_id'],
                    github['client_secret']
                ]
            ])->getBody();

            if (!$commits) {
                return [
                    'success' => false,
                    'message' => 'Unable to load commits.',
                ];
            }

            return [
                'success' => true,
                'message' => $this->getViewContents("pages/commits", [
                    'commits' => json_decode($commits, true)
                ])
            ];
        } catch (Exception $e) {
            return [
                'success' => true,
                'message' => "Failed to load repo."
            ];
        }
    }

    public function contributors() {
        try {
            $client = new GuzzleHttp\Client();

            $contributors = $client->request('GET', github['api_url'].github['username']."/".github['repo']."/contributors", [
                'headers' => [
                    'Accept' => 'application/json'
                ],
                'auth' => [
                    github['client_id'],
                    github['client_secret']
                ]
            ])->getBody();

            if (!$contributors) {
                return [
                    'success' => false,
                    'message' => 'Unable to load commits.',
                ];
            }

            return [
                'success' => true,
                'message' => $this->getViewContents("pages/contributors", [
                    'contributors' => json_decode($contributors, true)
                ])
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => "No Data"
            ];
        }
    }

    public function beforeExecute() {
        if ($this->getActionName() == "commits" || $this->getActionName() == "contributors") {
            $this->disableView(true);
            return true;
        }

        return parent::beforeExecute();
    }
}
