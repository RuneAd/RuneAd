<?php
class PagesController extends Controller {

    public function docs() {
        return true;
    }

    public function updates() {
        return true;
    }

    public function terms() {
        return true;
    }

    public function sitemap() {
        return true;
    }

    public function adinfo() {
        return true;
    }

    public function faq() {
        return true;
    }

    public function adbenners() {
        return true;
    }

    public function ads() {
        return true;
    }

    public function contact() {
        return true;
    }

    public function privacy() {
        return true;
    }

    public function stats() {
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

    public function commits() {
        try {
            $client = new GuzzleHttp\Client();

            $commits = $client->request('GET', github['api_url'].github['username']."/".github['repo']."/commits", [
                'headers' => [
                    'Accept' => 'application/json'
                ],
                'auth' => [
                    github['client_id'],
                    github['client_secret']
                ]
            ])->getBody();

            if (!$commits) {
                return [
                    'success' => false,
                    'message' => 'Unable to load commits.',
                ];
            }

            return [
                'success' => true,
                'message' => $this->getViewContents("pages/commits", [
                    'commits' => json_decode($commits, true)
                ])
            ];
        } catch (Exception $e) {
            return [
                'success' => true,
                'message' => "Failed to load repo."
            ];
        }
    }

    public function contributors() {
        try {
            $client = new GuzzleHttp\Client();

            $contributors = $client->request('GET', github['api_url'].github['username']."/".github['repo']."/contributors", [
                'headers' => [
                    'Accept' => 'application/json'
                ],
                'auth' => [
                    github['client_id'],
                    github['client_secret']
                ]
            ])->getBody();

            if (!$contributors) {
                return [
                    'success' => false,
                    'message' => 'Unable to load commits.',
                ];
            }

            return [
                'success' => true,
                'message' => $this->getViewContents("pages/contributors", [
                    'contributors' => json_decode($contributors, true)
                ])
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => "No Data"
            ];
        }
    }

    public function beforeExecute() {
        if ($this->getActionName() == "commits" || $this->getActionName() == "contributors") {
            $this->disableView(true);
            return true;
        }

        return parent::beforeExecute();
    }
}
