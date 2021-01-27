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
        'features',
        'features1',
        'features2',
        'features3',
        'features4',
        'features5',
        'duration',
        'level'
    ];

    public static function validate($validate){
        $validation = (new Validator)->validate($validate, [
            'title'     => 'required|min:3|max:150',
            'price'     => 'required|numeric|min:1',
            'duration'  => 'required|numeric|min:0',
            'level'     => 'required|numeric|min:1',
            'features'     => 'required|min:1',
            'features1'     => 'required|min:1',
            'features2'     => 'required|min:1',
            'features3'     => 'required|min:1',
            'features4'     => 'required|min:1',
            'features5'     => 'required|min:1',
        ]);
        return $validation;
   }

}