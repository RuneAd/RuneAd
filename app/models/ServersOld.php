<?php
use Illuminate\Database\Eloquent\Model as Model;

class SponsorPackages extends Model {

    public $timestamps    = false;
    public $incrementing  = true;
    protected $primaryKey = 'id';

    public $table = "SponsorPackages";

}
