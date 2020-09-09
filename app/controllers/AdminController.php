<?php
class AdminController extends Controller {

    public function index() {
        $thisMonth = strtotime(date("Y-m-01 00:00:00"));
        $lastMonth = strtotime("first day of last month");
        $lastWeek = strtotime("-1 week +1 day");
        $day = strtotime("today");
        $hour = strtotime("-1 hour");

        $data = [
            'users' => [
                'total' => Users::count(),
                'month' => Users::where("join_date", ">=", $thisMonth)->count()
            ],
            'votes' => [
                'total' => Votes::count(),
                'month' => Votes::where("voted_on", ">=", $thisMonth)->count(),
                'lastmonth' => Votes::where("voted_on", ">=", $lastMonth)->count(),
                'week' => Votes::where("voted_on", ">=", $lastWeek)->count(),
                'day' => Votes::where("voted_on", ">=", $day)->count(),
                'hour' => Votes::where("voted_on", ">=", $hour)->count()

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


        public function users() {
            $thisMonth = strtotime(date("Y-m-01 00:00:00"));
            $lastMonth = strtotime("first day of last month");
            $lastWeek = strtotime("-1 week +1 day");
            $day = strtotime("today");
            $hour = strtotime("-1 hour");

            $data = [
                'users' => [
                    'total' => Users::results(),
                ],
                'votes' => [
                    'total' => Votes::count(),
                    'month' => Votes::where("voted_on", ">=", $thisMonth)->count(),
                    'lastmonth' => Votes::where("voted_on", ">=", $lastMonth)->count(),
                    'week' => Votes::where("voted_on", ">=", $lastWeek)->count(),
                    'day' => Votes::where("voted_on", ">=", $day)->count(),
                    'hour' => Votes::where("voted_on", ">=", $hour)->count()

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
