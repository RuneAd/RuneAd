<?php
use Illuminate\Database\Eloquent\Model as Model;

class Payments extends Model {

    public $timestamps    = false;
    public $incrementing  = true;
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'user_id',
        'username',
        'ip_address',
        'sku',
        'item_name',
        'email',
        'status',
        'paid',
        'quantity',
        'currency',
        'capture_id',
        'transaction_id',
        'date_paid',
    ];

}