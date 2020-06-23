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

    public static function getChartData() {
        $start = strtotime(date("Y-m-01 00:00:00"));

        $query = self::select(["date_paid", "paid"])
            ->where("date_paid", ">=", $start)
            ->orderby("date_paid", "ASC")
            ->get();

        $data = [];

        foreach ($query as $payment) {
            $date = date("M-d", $payment->date_paid);

            if (!in_array($date, array_keys($data))) {
                $data[$date] = 0;
            }

            $data[$date] = number_format($data[$date] + $payment->paid, 2);
        }

        return $data;
    }
}
