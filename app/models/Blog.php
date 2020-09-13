<?php
use Illuminate\Database\Eloquent\Model as Model;
use Rakit\Validation\Validator;
use Illuminate\Pagination\Paginator;

class Blog extends Model {

    public $timestamps    = false;
    public $incrementing  = true;
    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'owner', 'title', 'meta_info', 'meta_tags',
        'description', 'date_created'
    ];

   public static function validate($validate){
        $validator = new Validator;

        $validation = $validator->validate($validate, [
            'title'        => 'required|min:6|max:150',
            'meta_tags' => 'required|min:2|max:300',
            'description' => 'required|min:10'
        ]);

        return $validation;
   }

    public function user() {
        return $this->belongsTo('Users', 'owner', 'id');
    }

    public static function getAll($page = 1) {
        Paginator::currentPageResolver(function() use ($page) {
            return $page;
        });

        return Blog::where('title', '!=', null)
            ->select(
                'id',
                'title',
                'description',
                'meta_tags'
            )
            ->selectRaw(
                'IF(premium_expires > '.time().', votes + (premium_level * 1), votes) as votes')
            ->where('website', '!=', null)
            ->orderBy('is_online', 'DESC')
            ->orderBy('votes', 'DESC')
            ->orderBy('id', 'ASC')
            ->paginate(per_page);
    }

    public static function getBlog($id) {
        return Blog::where('id', $id)
            ->select('*')
            ->leftJoin('users', 'users.user_id', '=', 'servers.owner')
            ->first();
    }

    public static function getBlogByOwner($ownerId) {
        return Blog::where('owner', $ownerId)
            ->select('*')
            ->selectRaw(
                'IF(premium_expires > '.time().', votes + (premium_level * 1), votes) as votes')
            ->get();
    }

}
