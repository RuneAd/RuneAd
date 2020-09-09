<?php
class AdminController extends Controller {

    public function index() {
        $thisMonth = strtotime(date("Y-m-01 00:00:00"));

        $thisDay = strtotime(date("Y-m-01 00:00:00"));
        $thisHour = strtotime(date("Y-m-01 00:00:00"));

        $data = [
            'users' => [
                'total' => Users::count(),
                'month' => Users::where("join_date", ">=", $thisMonth)->count()
            ],
            'votes' => [
                'total' => Votes::count(),
                'month' => Votes::where("voted_on", ">=", $thisMonth)->count()
                
            ],
            'reports' => [
                'total' => Reports::count(),
                'month' => Reports::where("date_reported", ">=", $thisMonth)->count()
            ],
            'payments' => [
                'total' => Payments::sum('paid'),
                'month' => Payments::where("date_paid", ">=", $thisMonth)->sum('paid'),
            ],
            'servers' => [
                'total' => Servers::count(),
                'month' => Servers::where("date_created", ">=", $thisMonth)->count(),
            ]
        ];

        $chartData = Payments::getChartData();

        $this->set("data", $data);
        $this->set("chart_keys", array_keys($chartData));
        $this->set("chart_values", array_values($chartData));
        return true;
    }

    public function beforeExecute() {
        $this->set("page_title", "Admin");
        return parent::beforeExecute();
    }

}
