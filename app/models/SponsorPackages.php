<?php
use Illuminate\Database\Eloquent\Model as Model;
use Rakit\Validation\Validator;

class SponsorPackages extends Model {

    public $timestamps    = false;
    public $incrementing  = true;
    protected $primaryKey = 'id';

    public $table = "sponsor_packages";

    protected $fillable = [
        'title',
        'price',
        'duration',
        'visible',
        'icon'
    ];

    public static function validate($validate){
        $validation = (new Validator)->validate($validate, [
            'title'     => 'required|min:3|max:150',
            'price'     => 'required|numeric|min:1',
            'duration'  => 'required|numeric|min:0',
            'visible'   => 'required|numeric|min:0|max:1'
        ]);
        return $validation;
   }
   
}