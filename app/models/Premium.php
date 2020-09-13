<?php
use Illuminate\Database\Eloquent\Model as Model;
use Rakit\Validation\Validator;

class Premium extends Model {

    public $timestamps    = false;
    public $incrementing  = true;
    protected $primaryKey = 'id';

    public $table = 'premium_packages';
    
    protected $fillable = [
        'title',
        'price',
        'duration',
        'features',
        'duration',
        'level'
    ];

    public static function validate($validate){
        $validation = (new Validator)->validate($validate, [
            'title'     => 'required|min:3|max:150',
            'price'     => 'required|numeric|min:1',
            'duration'  => 'required|numeric|min:0',
            'level'     => 'required|numeric|min:1',
        ]);
        return $validation;
   }

}