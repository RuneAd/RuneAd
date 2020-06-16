<?php

class LoginController extends Controller {

    public function index() {
        return true;
    }

    public function token() {
        if (!$this->request->hasPost("access_token")) {
            return [
                'success' => 'false',
                'message' => 'Invalid post data.'
            ];
        }

        try {
            $access_token = $this->request->getPost("access_token", "string");

            $client  = new GuzzleHttp\Client();
            $discord = new Discord($access_token);

            $discord->setEndpoint("/users/@me"); 
            $me = $discord->get();
            
            if (!$me || isset($me['code'])) {
                $this->cookies->remove("access_token");
                return [
                    'success' => false,
                    'message' => 'Could not fetch user data. Error '.$me['code']
                ];
            }

            $user = Users::firstOrCreate(
                ['user_id' => $me['id']],
                [
                    'discriminator' => $me['discriminator'], 
                    'username'      => $me['username'],
                    'email'         => $me['email'],
                    'avatar'        => $me['avatar'],
                    'roles'         => ['Member']
                ]
            );

            if (!$user->wasRecentlyCreated) {
                $user->username      = $me['username'];
                $user->discriminator = $me['discriminator'];
                $user->email         = $me['email'];
                $user->avatar        = $me['avatar'];
                $user->update();
            }
        
            if ($me['avatar'] != $user->avatar) {
                $user->avatar = $me['avatar'];
            }

            $discord->setEndpoint('/guilds/'.discord['guild_id'].'/members/'.$user['user_id']); 
            $discord->setIsBot(true);
            $userInfo = $discord->get();
            
            if (!$userInfo || isset($userInfo['code'])) {
                $user->roles = ["Member"];
                $user->save();
                $this->cookies->set("access_token", $access_token, 86400 * 7);
                return [
                    'success' => true,
                    'message' => 'You have successfully logged in!'
                ];
            }
            
            $discord->setEndpoint('/guilds/'.discord['guild_id']); 
            $discord->setIsBot(true);
            $server = $discord->get();

            $server_roles = $server['roles'];
            $roles = ['Member'];

            foreach ($server_roles as $sr) {
                if (in_array($sr['id'], $userInfo['roles'])) {
                    $roles[] = $sr['name'];
                }
            }

            $user->roles = $roles;
            $user->save();
            
            $this->cookies->set("access_token", $access_token, 86400 * 7);

            return [
                'success' => true,
                'message' => 'You have successfully logged in!'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => "An error occured: ".$e->getMessage()
            ];
        }
    }

    public function discord() {
        $params = array(
            'client_id'     => discord['client_id'],
            'redirect_uri'  => discord['redirect_uri'],
            'response_type' => 'token',
            'scope'         => 'identify guilds email'
        );

        return [
            'success' => true,
            'message' => 'https://discordapp.com/api/oauth2/authorize?'.http_build_query($params)
        ];
    }

    public function beforeExecute() {
        parent::beforeExecute();

        if ($this->getActionName() == "discord" || $this->getActionName() == "token") {
            $this->disableView(true);
            return true;
        }
        return true;
    }

}