<?php
use Illuminate\Database\Eloquent\Model as Model;
use Rakit\Validation\Validator;

class TurboPackages extends Model {

    public $timestamps    = false;
    public $incrementing  = true;
    protected $primaryKey = 'id';

    public $table = 'turbo_packages';
    
    protected $fillable = [
        'title',
        'price',
        'duration',
        'visible',
        'features'
    ];

    public static function validate($validate){
        $validation = (new Validator)->validate($validate, [
            'title'     => 'required',
            'price'     => 'required|numeric',
            'duration'  => 'required|numeric',
            'visible'   => 'required|numeric',
            'features'   => 'required'

        ]);
        return $validation;
   }
   
}