<?php
use Illuminate\Database\Eloquent\Model as Model;

class Reports extends Model {

    public $timestamps    = false;
    public $incrementing  = true;
    protected $primaryKey = 'id';

    protected $fillable = [
        'server_id',
        'reason',
        'body',
        'report_ip',
        'date_reported'
    ];

}