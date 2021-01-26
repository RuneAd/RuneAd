<?php
use Illuminate\Database\Eloquent\Model as Model;

class Turbo extends Model {

    public $timestamps    = false;
    public $incrementing  = true;
    protected $primaryKey = 'id';

    protected $fillable = [
        'server_id', 'expires', 'started'
    ];

}
