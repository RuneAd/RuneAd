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

        $chartData = Payments::getChartData();

        $this->set("data", $data);
        $this->set("chart_keys", array_keys($chartData));
        $this->set("chart_values", array_values($chartData));
        return true;
    }


     $this->set("chart_keys", array_keys($dates['chart']));
     $this->set("votes_data", $votes);
     $this->set("data", $data);
     return true;
 }

 public function reports($page = 1) {
     if ($this->request->hasQuery("delete")) {
         $report_id = $this->request->getQuery("delete");
         $report    = Reports::where("id", $report_id)->first();

         if ($report) {
             $report->delete();
         }

         $this->redirect("admin/reports");
         exit;
     }

     $reports = Reports::select([
         'reports.id',
         'reports.server_id',
         'reports.reason',
         'servers.title',
     ])
     ->leftJoin("servers", "servers.id", "=", "reports.server_id")
     ->orderBy("date_reported", "ASC")
     ->paginate(15);

     $this->set("reports", $reports);
     return true;
 }

 public function viewreport($id) {
     $report = Reports::select([
         'reports.id',
         'reports.server_id',
         'reports.reason',
         'reports.body',
         'servers.title',
     ])
     ->leftJoin("servers", "servers.id", "=", "reports.server_id")
     ->where("reports.id", "=", $id)
     ->first();

     if (!$report) {
         $this->setView("errors/show404");
         return false;
     }

     $this->set("report", $report);
     return true;
 }
 public function getChartDates($dayLimit = 14, $format = "m.d") {
     $start = time() - (86400 * $dayLimit);
     $end   = time();

     $data = [
         'start'  => $start,
         'format' => $format,
         'chart'  => [],
     ];

     while($start <= $end) {
         $date = date($format, $start);
         $data['chart'][$date] = 0;

         $start += 86400; // increment by 1 day until we reach today
     }

     return $data;
 }

 public function beforeExecute() {
     return parent::beforeExecute();
 }

}
