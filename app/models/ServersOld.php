<?php
use Illuminate\Database\Eloquent\Model as Model;
use Rakit\Validation\Validator;
use Illuminate\Pagination\Paginator;

class ServersOld extends Model {

    public $timestamps    = false;
    public $incrementing  = true;
    protected $primaryKey = 'id';

    public $table = "servers_old";

}