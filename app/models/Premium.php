<?php
use Illuminate\Database\Eloquent\Model as Model;

class Premium extends Model {

    public $timestamps    = false;
    public $incrementing  = true;
    protected $primaryKey = 'id';

    public $table = 'premium_packages';
    
    protected $fillable = [
        'title',
        'duration',
        'price',
        'features',
        'duration',
        'level'
    ];

}