<?php
use Illuminate\Database\Eloquent\Model as Model;
use Rakit\Validation\Validator;
use Illuminate\Pagination\Paginator;

class Servers extends Model {

    public $timestamps    = false;
    public $incrementing  = true;
    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'owner', 'title', 'revision', 'server_ip', 'server_port', 'is_online', 'votes',
        'website', 'callback_url', 'banner_url', 'discord_link', 'meta_info', 'meta_tags',
        'description', 'date_created', 'premium_level', 'premium_expires'
    ];

   public static function validate($validate){
        $validator = new Validator;

        $validation = $validator->validate($validate, [
            'revision'     => ['required', function($value) {
                $revision = Revisions::where('revision', $value)->first();
                if (!$revision) {
                    return ":attribute is not a valid revision.";
                }
            }],
            'title'        => 'required|min:6|max:150',
            'server_port'  => 'numeric|min:0|max:65535',
            'server_ip'    => 'ipv4',
            'website'      => 'url:http,https|max:255',
            'callback_url' => 'url:http,https|max:255',
            'discord_link' => 'url:https|max:255',
            'banner_url'   => ['', function($value) {
                if (substr($value, 0, 4) != "http" && !file_exists('public/img/banners/'.$value)) {
                    return 'Image does not exist.';
                }
            }],
            'meta_tags' => ['max:300', function($value) {
                if (count($value) > 15) {
                    return 'You can\'t have more than 15 meta tags.';
                }
            }],
            'description' => 'required|min:7'
        ]);

        return $validation;
   }

    public function user() {
        return $this->belongsTo('Users', 'owner', 'id');
    }

    public static function getByRevision($revision, $page = 1) {
        Paginator::currentPageResolver(function() use ($page) {
            return $page;
        });

        return Servers::where('revision', '=', $revision->revision)
            ->select(
                'id', 
                'title', 
                'revision', 
                'website',
                'banner_url', 
                'is_online',
                'ping',
                'last_ping',
                'premium_expires',
                'website',
                'discord_link'
            )
            ->selectRaw(
                'IF(premium_expires > '.time().', votes + (premium_level * 100), votes) as votes')
            ->where('website', '!=', null)
            ->orderBy('is_online', 'DESC')
            ->orderBy('votes', 'DESC')
            ->orderBy('id', 'ASC')
            ->paginate(per_page);
    }

    public static function getAll($page = 1) {
        Paginator::currentPageResolver(function() use ($page) {
            return $page;
        });

        return Servers::where('website', '!=', null)
            ->select(
                'id', 
                'title', 
                'revision', 
                'banner_url', 
                'is_online',
                'ping',
                'last_ping',
                'premium_expires',
                'website',
                'discord_link'
            )
            ->selectRaw(
                'IF(premium_expires > '.time().', votes + (premium_level * 100), votes) as votes')
            ->where('website', '!=', null)
            ->orderBy('is_online', 'DESC')
            ->orderBy('votes', 'DESC')
            ->orderBy('id', 'ASC')
            ->paginate(per_page);
    }

    public static function searchServers($name, $page = 1) {
        Paginator::currentPageResolver(function() use ($page) {
            return $page;
        });

        return Servers::where('website', '!=', null)
            ->select(
                'id', 
                'title', 
                'revision',
                'votes',
                'banner_url', 
                'is_online',
                'premium_expires'
            )
            ->where('title', 'LIKE', '%'.$name.'%')
            ->orWhere('owner', '=', ''.$name.'')
            ->orderBy('id', 'ASC')
            ->paginate(per_page);
    }

    public static function getAdminServers($page = 1) {
        Paginator::currentPageResolver(function() use ($page) {
            return $page;
        });

        return Servers::where('website', '!=', null)
            ->select(
                'id', 
                'title', 
                'revision',
                'votes',
                'banner_url', 
                'is_online',
                'premium_expires'
            )
            ->where('website', '!=', null)
            ->orderBy('id', 'ASC')
            ->paginate(per_page);
    }

    public static function getServer($id) {
        return Servers::where('id', $id)
            ->select('*')
            ->leftJoin('users', 'users.user_id', '=', 'servers.owner')
            ->first();
    }

    public static function getServersByOwner($ownerId) {
        return Servers::where('owner', $ownerId)
            ->select('*')
            ->selectRaw(
                'IF(premium_expires > '.time().', votes + (premium_level * 100), votes) as votes')
            ->get();
    }

    public static function getChartData($data) {
        $query = self::select("date_created")
            ->where('date_created', '>=', $data['start'])
            ->orderby("date_created", "ASC")
            ->get();

        foreach ($query as $user) {
            $date = date($data['format'], $user->date_created);
            $data['chart'][$date]++;
        }

        return array_values($data['chart']);
    }

}