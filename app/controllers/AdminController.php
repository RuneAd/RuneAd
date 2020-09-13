<?php
class AdminController extends Controller {

    public function index() {
        $thisMonth = strtotime(date("Y-m-01 00:00:00"));
        
        $data = [
            'users' => [
                'total' => Users::count(),
                'month' => Users::where("join_date", ">=", $thisMonth)->count()
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

        $dates = $this->getChartDates(30);
        $votes = Votes::getChartData($dates);

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