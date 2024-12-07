<?php
use Fox\Request;

class VoteController extends Controller {
    

    public function index($serverId, $incentive) {
        $server = Servers::getServer($serverId);

        if (!$server) {
           $this->setView("errors", "show404");
           return false;
        }

        $ip   = $this->request->getAddress();

        $vote = Votes::query()
            ->where("server_id", $server->id)
            ->where(function($query) use ($ip, $incentive) {
                $query
                    ->where("ip_address", $ip)
                    ->orWhere("incentive", $incentive);
            })
            ->whereRaw(time()." - voted_on < 43000")
            ->first();
    

        $this->set("incentive", $incentive);
        $this->set("vote", $vote);
        $this->set("server", $server);
        $this->set("server_url", Functions::friendlyTitle($server->id.'-'.$server->title));
        return true;
    }

    public function addvote() {
        $id        = $this->request->getPost("server_id", "int");
        $token     = $this->request->getPost("token");
        $incentive = $this->request->getPost("incentive", "string");


        $server = Servers::getServer($id);

        if (!$server) {
            return [
                'success' => false,
                'message' => 'Invalid server id'
            ];
        }

        $ip   = $this->request->getAddress();

        $vote = Votes::query()
            ->where("server_id", $server->id)
            ->where(function($query) use ($ip, $incentive) {
                if ($incentive) {
                $query
                    ->where("ip_address", $ip)
                    ->orWhere("incentive", $incentive);
                } else {
                    $query
                        ->where("ip_address", $ip);
                }
            })
            ->whereRaw(time()." - voted_on < 43000")
            ->first();

        if ($vote) {
            return [
                'success' => false,
                'message' => 'You have already voted within the last 12 hours!',
                'votes'   => $server->votes
            ];
        }

        $vote = new Votes();

        $vote->fill([
            'server_id'  => $server->id,
            'ip_address' => $ip,
            'incentive'  => $incentive,
            'voted_on'   => time()
        ]);

        $saved = $vote->save();

        if (!$saved) {
            return [
                'success' => false,
                'message' => 'Vote failed to register.',
                'votes' => $server->votes
            ];
        }

        $server->votes += ($server->premium_expires > time() ? 1 : 1);
        $server->save();

        $votes = $server->votes;

        if ($server->premium_expires > time()) {
            $votes = ($votes + ($server->premium_level * 1));
        }

        if ($incentive != null && $server->callback_url) {
            $callback = $this->sendIncentive($server->callback_url, $incentive);

            if (isset($callback['success']) && isset($callback['message'])) {
                return [
                    'success' => $callback['success'],
                    'message' => $callback['message']
                ];
            }
        }

        return [
            'success' => true,
            'message' => 'Thank you, your vote has been registered!',
            'votes'   => $votes
        ];
    }

    /**
     * @var string $token
     * @return json
     * Send post request containing token to validate request.
     */
    private function verifyReCaptcha($token){
        $client = new GuzzleHttp\Client();

        $response = $client->request('POST', "https://www.google.com/recaptcha/api/siteverify", [
            'headers' => [
                'Accept' => 'application/json'
            ],
            'form_params' => [
                "secret"   => recaptcha['private'],
                "response" => $token
            ]
        ]);

        $json = json_decode($response->getBody(), true);
        return $json;
    }

    /**
     * Sends a get request to specified callback url.
     * Used primarily for vote scripts.
     */
    public function sendIncentive($url, $incentive) {
        // if url ends with an = (means it's expecting a value right after, then just append the incentive)
        if ($this->endsWith($url, '=')) {
			$url = $url.$incentive;
	    } else {
	    	$isFile = substr($url, strlen($url) - 4, strlen($url)) == ".php";

	    	if ($isFile) {
	    		$url = $url.'?postback='.$incentive;
	    	} else {
	    		$hasSep = substr($url, strlen($url) - 1, strlen($url)) == "/";
	    		$url = $url.($hasSep ? '' : '/').$incentive;
	    	}
        }

        try {
            $client = new GuzzleHttp\Client();

            $response = $client->request('GET', $url, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Upgrade-Insecure-Requests' => 1,
                    'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.100 Safari/537.36',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',
                    'Accept-Language' => 'en-US,en;q=0.9'
                ],
            ]);

            $json = $response->getBody();

            return [
                'http_code' => $response->getStatusCode(),
                'response' => json_decode($json, true)
            ];
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * @var $url
     * return bool
     * Returns true if url contains a get query.
     */
    private function hasQuery($url) {
    	return strpos($url, '?') !== false || strpos($url, '&') !== false;
    }

    /**
     * @var string $string
     * @var string $search
     * Returns true if givvn string contains search param
     */
    private function endsWith($string, $search) {
	    $length = strlen($string);

	    if ($length == 0) {
	        return false;
	    }

	    return substr($string, $length - 1, $length) == $search;
    }

    public function beforeExecute() {
        if ($this->getActionName() == "addvote") {
            $this->request = Request::getInstance();
            $this->disableView(true);
            return true;
        }

        return parent::beforeExecute();
    }
}
