<?php
use Illuminate\Database\Eloquent\Model as Model;

class Votes extends Model {

    public $timestamps    = false;
    public $incrementing  = true;
    protected $primaryKey = 'id';

    protected $fillable = [
        'server_id', 
        'ip_address', 
        'incentive', 
        'voted_on'
    ];

    public static function getChartData($dates) {
        $query = self::select(["voted_on"])
            ->where("voted_on", ">=", $dates['start'])
            ->orderby("voted_on", "ASC")
            ->get();

        foreach ($query as $vote) {
            $date = date($dates['format'], $vote->voted_on);
            $dates['chart'][$date]++;
        }

        return array_values($dates['chart']);
    }

}