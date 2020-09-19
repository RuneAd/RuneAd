<?php
 use Illuminate\Database\Eloquent\Model as Model;
 use Rakit\Validation\Validator;

 class Blog extends Model {

     public $timestamps    = false;
     public $incrementing  = true;
     protected $primaryKey = 'id';

     protected $table = "blog";

     protected $fillable = [
         'title',
         'category',
         'author_id',
         'content',
         'meta_tags',
         'meta_description',
         'date_posted'
     ];

     public static function validate($validate){
         $validator = new Validator;

         $validation = $validator->validate($validate, [
             'title'     => 'required|min:6|max:150',
             'category'  => 'required|min:3|max:255',
             'content'   => 'required|min:100',
             'meta_tags' => ['', function($value) {
                 if (count($value) > 15) {
                     return 'You can\'t have more than 15 meta tags.';
                 }
             }],
             'meta_description' => 'min:20|max:255'
         ]);

         return $validation;
    }

 } 