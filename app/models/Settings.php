<?php
use Illuminate\Database\Eloquent\Model as Model;

class Settings extends Model {

    public $timestamps    = false;
    public $incrementing  = true;
    protected $primaryKey = 'id';

    protected $fillable = ['settings', 'visible'];

}