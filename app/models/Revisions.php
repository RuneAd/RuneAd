<?php
use Illuminate\Database\Eloquent\Model as Model;

class Revisions extends Model {

    public $timestamps    = false;
    public $incrementing  = true;
    protected $primaryKey = 'id';

    protected $fillable = ['revision', 'visible'];

}