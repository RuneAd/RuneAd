<?php
use Illuminate\Pagination\Paginator;
use Fox\Paginator as Pager;

class UsersController extends Controller {

    public function index($page = 1) {
        Paginator::currentPageResolver(function() use ($page) {
            return $page;
        });

        if ($this->request->hasQuery("ban")) {
            $userId = $this->request->getQuery("ban", "int");

            $discord = new Discord($this->cookies->get("access_token"));
            $discord->setIsBot(true);
            $discord->setEndpoint("/guilds/".discord['guild_id']."/bans/".$userId);

            $data = $discord->get("PUT", [
                'delete-message-days' => 7,
                'reason' => 'Ban from website for ToS Violation.',
            ]);

            $user = Users::where("user_id", $userId)->first();

            if ($user) {
                if ($user->delete()) {
                    $servers = Servers::where("owner", $userId)->get();

                    foreach ($servers as $server) {
                        $server->delete();
                    }
                }

                $link1= "[{$this->user->username}#{$this->user->discriminator}](https://discord.com/users/{$this->user->user_id})";
                $link2= "[{$user->username}#{$user->discriminator}](https://discord.com/users/{$userId})";

                (new DiscordMessage([
                    'is_rich'    => true,
                    'channel_id' => '610038623743639559',
                    'title'      => 'Moderation',
                    'message'    => "$link1 has banned $link2 from the server!",
                ]))->send();
            }

            $this->redirect("admin/users");
            exit;
        }

        $users = Users::paginate(15);
        $this->set("users", $users);
        return true;
    }

}
