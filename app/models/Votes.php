<?php
use Illuminate\Database\Eloquent\Model as Model;

class Votes extends Model {

    public $timestamps    = false;
    public $incrementing  = true;
    protected $primaryKey = 'id';

    protected $fillable = [
        'server_id', 'ip_address', 'incentive', 'voted_on'
    ];

    public static function getChartData($server, $start_date) {
        return self::where('server_id', '=', $server->id)
            ->where("voted_on", '>', $start_date)
            ->selectRaw('COUNT(*) AS total')
            ->selectRaw("FROM_UNIXTIME(voted_on, '%m %d') AS time")
            ->groupBy("time")
            ->orderBy("time", 'ASC')
            ->get();
    }

}
