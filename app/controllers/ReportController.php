<?php
class ReportController extends Controller {

    public function index($id) {
        $server = Servers::getServer($id);

        if (!$server) {
            $this->setView("errors/show404");
            return false;
        }

        $ip_address = $this->request->getAddress();

        $report = Reports::query()
            ->where("server_id", $server->id)
            ->where("report_ip", $ip_address)
            ->where("date_reported", ">", time() - 3600)
            ->first(); 

        if ($report) {
            $this->set("error", "You've already filed a report on this server within the last hour!");
            $this->set("disable_form", true);
        } else {
            if ($this->request->isPost()) {
                $data = [
                    'server_id' => $server->id,
                    'reason'    => $this->request->getPost("reason", "string"),
                    'body'      => $this->purify($this->request->getPost("info")),
                    'report_ip' => $ip_address,
                    'date_reported' => time()
                ];
                
                $report = new Reports;
                $report->fill($data);
    
                if ($report->save()) {
                    $this->set("success", "Your report has been sent!");
                } else {
                    $this->set("error", "Failed to save report. Please try again or contact site admin.");
                }
            }
        }

        $this->set("seo_link", Functions::friendlyTitle($server->id.'-'.$server->title));
        $this->set("server", $server);
        return true;
    }

}