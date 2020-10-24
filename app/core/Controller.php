<?php
use Fox\Request;
use Fox\Cookies;
use Fox\CSRF;
use Fox\Session;
use EasyCSRF\EasyCSRF;
use EasyCSRF\NativeSessionProvider;

class Controller {

	protected $view;
    protected $viewVars = array();
    protected $actionName;

    private $disableView;
    private $json_output;

    /** @var HTMLPurifier purifier */
    private $purifier;

    /** @var PageRouter */
    protected $router;

	/** @var Request $request */
	protected $request;

	/** @var Cookies $cookies */
    protected $cookies;

    /** @var Session $session */
    protected $session;

    /** @var EasyCSRF $csrf */
    protected $csrf;

    /** @var User $user */
    public $user;

    protected $access = [
        'login_required' => false,
        'roles'  => []
    ];

    public function beforeExecute() {
        $this->request  = Request::getInstance();
        $this->cookies  = Cookies::getInstance();
        $this->csrf     = new EasyCSRF(new NativeSessionProvider());
        $this->session  = Session::getInstance();

        $token = $this->cookies->get("access_token");
        $roles = ["Guest"];

        if ($token) {
            try {
                $discord = new Discord($this->filter($token));

                $discord->setEndpoint("/users/@me");
                $me = $discord->get();

                if (!$me || isset($me['code'])) {
                    $this->cookies->delete("access_token");
                    $this->redirect("");
                    exit;
                }

                $user = Users::where('user_id', $me['id'])->first();

                if (!$user) {
                    $this->cookies->delete("access_token");
                    $this->redirect("");
                    return;
                }

                $this->set("user", $user);
                $this->user = $user;
                $roles = json_decode($user->roles, true);
            } catch (Exception $e) {
                $this->cookies->delete("access_token");
                $this->redirect("");
                exit;
            }
        }

        $controller = $this->router->getController();
        $action = $this->getActionName();

        $canAccess = Security::canAccess($controller, $action, $roles);

        if (!$canAccess) {
            $this->setView("errors/show401");
            return false;
        }

        $darkMode = false;

        if ($this->cookies->has("darkmode")) {
            $this->set("darkmode", true);
            $darkMode = true;
        }

        if ($this->cookies->has("hide_sponsor")) {
            $this->set("hide_sponsor", true);
        }

        $meta = $this->getPageMeta($controller, $action);

        $this->set("page_title", $meta['title']);
        $this->set("meta_info", $meta['meta']);

        $this->set("theme", $darkMode ? "dark" : "light");
        $this->set("controller", $controller);
        $this->set("action", $action);
        $this->set("route", $this->router->getCanonical());
        return true;
    }

    public function getPageMeta($controller, $action) {
        $pages = [
            'premium' => [
                'index' => [
                    'title' => 'Premium | The Modern RuneScape Private Server Toplist',
                    'meta'  => 'Buy premium on RuneAd to give your server a nice boost, which will increase traffic and visibility for your server!'
                ]
            ],
            'index' => [
                'index' => [
                    'title' => 'RSPS Toplist | RuneScape Private Servers',
                    'meta'  => 'Looking for the best Runescape Private Servers 2020? Come get involved in our RSPS Toplist, and increase your website traffic and attract your players today!'
                ]
            ],
            'pages' => [
                'docs' => [
                    'title' => 'Documentation | The Modern RuneScape Private Server Toplist',
                    'meta'  => 'Integrate your RSPS server with our service, receive voting callback, and more! RuneAd is the best 2020 RuneScape Private Server Toplist!'
                ],
                'updates' => [
                    'title' => 'Update Log | The Modern RuneScape Private Server Toplist',
                    'meta'  => 'All updates that have been pushed for the toplist, and a list of contributors. RuneAd is the best 2020 RuneScape Private Server Toplist!'
                ],
                'sitemap' => [
                    'title' => 'Sitemap | The Modern RuneScape Private Server Toplist',
                    'meta'  => 'A full sitemap of RuneAd RSPS Toplist displaying RSPS servers, tools, and more! RuneAd is the best 2020 RuneScape Private Server Toplist!'
                ],
                'stats' => [
                    'title' => 'Voting Stats | The Modern RuneScape Private Server Toplist',
                    'meta'  => 'Global statistics on RuneAd showing votes, user, and server counts. RuneAd is the best 2020 RuneScape Private Server Toplist!'
                ],
                'terms' => [
                    'title' => 'Terms of Service | The Modern RuneScape Private Server Toplist',
                    'meta'  => 'RuneAd Terms Of Service. Preview the Terms of Service for RuneAd RSPS Toplist! RuneAd is the best 2020 RuneScape Private Server Toplist!'
                ],
				'contact' => [
                    'title' => 'Contact Us | The Modern RuneScape Private Server Toplist',
                    'meta'  => 'Contact RuneAd support and get help on anything related to RuneAd RSPS Toplist. RuneAd is the best 2020 RuneScape Private Server Toplist!'
                ],
                'privacy' => [
                    'title' => 'Privacy Policy | The Modern RuneScape Private Server Toplist',
                    'meta'  => 'RuneAd Privacy Policy. Review the Privacy Policy for RuneAd RSPS Toplist! RuneAd is the best 2020 RuneScape Private Server Toplist!'
                ],
                'faq' => [
                    'title' => 'FAQ | The Modern RuneScape Private Server Toplist',
                    'meta'  => 'Frequently Asked Questions! Browse RuneAds FAQs to find out more about RuneAd RSPS Toplist! RuneAd is the best 2020 RuneScape Private Server Toplist!'
                ],
				'ads' => [
                    'title' => 'Store | The Modern RuneScape Private Server Toplist',
                    'meta'  => 'Infomation about Advertisements on RuneAd. RuneAd is the best 2020 RuneScape Private Server Toplist!'
                ]
            ],
            'profile' => [
                'index' => [
                    'title' => 'My Profile | The Modern RuneScape Private Server Toplist',
                     'meta'  => 'Edit and add a new server, view payment history, stats, and more on RuneAd. RuneAd is the best 2020 RuneScape Private Server Toplist!'
                ],
                'payments' => [
                    'title'  => 'My Payments | The Modern RuneScape Private Server Toplist',
                     'meta'  => 'View your payment history for RuneAd. RuneAd is the best 2020 RuneScape Private Server Toplist!'
                ],
                'add' => [
                    'title'  => 'Add Server | The Modern RuneScape Private Server Toplist',
                     'meta'  => 'Add a new server to the RuneAd toplist. RuneAd is the best 2020 RuneScape Private Server Toplist!'
                ],
                'edit' => [
                    'title'  => 'Edit Server | The Modern RuneScape Private Server Toplist',
                     'meta'  => 'Edit an existing server on the RuneAd toplist. RuneAd is the best 2020 RuneScape Private Server Toplist!'
                ]
            ],
            'sponsor' => [
                'index' => [
                    'title' => 'Sponsored Ads | The Modern RuneScape Private Server Toplist',
                    'meta' => 'Purchase sponsored ad spots on RuneAd to give your server a boost! RuneAd is the best 2020 RuneScape Private Server Toplist!'
                ]
            ],
            'tools' => [
                'itemdb' => [
                    'title' => 'OSRS Item DB | The Modern RuneScape Private Server Toplist',
                    'meta' => 'An easy to use oldschool runescape item db that\'s always up to date. RuneAd is the best 2020 RuneScape Private Server Toplist!'
                ],
				 'map' => [
                    'title' => 'OSRS Interactive Map | The Modern RuneScape Private Server Toplist',
                    'meta'  => 'An easy to use oldschool runescape map that\'s always up to date. RuneAd is the best 2020 RuneScape Private Server Toplist!'
                ],
            ],
            'downloads' => [
                'index' => [
                    'title' => 'RSPS Tool Downloads | The Modern RuneScape Private Server Toplist',
                    'meta' => 'Downloads for RSPS Tools on RuneAd. Browse our free collection of Runescape Private Server Downloads & more!'
                ],
				 'rsps' => [
                    'title' => 'RSPS Downloads | The Modern RuneScape Private Server Toplist',
                    'meta'  => 'Downloads for RSPS Sources and Clients on RuneAd. Browse our free collection of Runescape Private Server Downloads & more!'
                ],
                'web' => [
                    'title' => 'RSPS Web Downloads | The Modern RuneScape Private Server Toplist',
                    'meta'  => 'Downloads for RSPS Web-templates. Browse our free collection of Runescape Private Server Downloads & more!'
                ],
                'project51rev180' => [
                    'title' => 'Project51 Rev180 RSPS Downloads',
                    'meta'  => 'Downloads for RSPS tools, sources, clients, web-templates & more! Browse our free collection of Runescape Private Server Downloads & more!'
                ],
            ],
            'videos' => [
                'index' => [
                    'title' => 'RuneAd Videos | The Modern RuneScape Private Server Toplist',
                    'meta' => 'RuneAd Videos brought you to by RuneAd! Browse our free collection of Runescape Private Server Videos & more!'
                ]
            ],
            'blog' => [
                'index' => [
                    'title' => 'Blog | The Modern RuneScape Private Server Toplist',
                    'meta' => 'An easy to use modern RuneScape Private Server Blog. Browse our free collection of amazing blog posts & more!'
                ],
				 'add' => [
                    'title' => 'Blog | Add Post | The Modern RuneScape Private Server Toplist',
                    'meta' => 'An easy to use modern RuneScape Private Server Blog. Browse our free collection of amazing blog posts & more!'                
                ],
            ]
        ];

        if (in_array($controller, array_keys($pages))) {
            $actions = $pages[$controller];
            if (in_array($action, array_keys($actions))) {
                return $pages[$controller][$action];
            }
        }

        return [
            'title' => 'RSPS Toplist | RuneScape Private Servers',
            'meta'  => 'Looking for the best Runescape Private Servers 2020? Come get involved in our RSPS Toplist, and increase your website traffic and attract your players today!'
        ];
    }


   /**
     * Displays the necessary template using Twig
     */
	public function show() {
	    if ($this->disableView) {
	        return;
        }

	    $loader = new Template('app/views');
        $loader->setCacheEnabled(false);

        if (!file_exists($loader->path.'/'.$this->view.".twig")) {
            $this->view = "errors/missing";
        }

	    try {
            $template = $loader->load($this->view);
            echo $template->render($this->viewVars);
        } catch (Exception $e) {
            
        }
    }
    
    /**
     * Gets the name of the action
     * @return mixed
     */
	public function getActionName() {
		return $this->actionName;
	}

    /**
     * Sets the action to be used.
     * @param $name
     */
	public function setActionName($name) {
		$this->actionName = $name;
	}

    /**
     * Sets a specific variable for the view with a value
     * @param $variableName
     * @param $value
     */
	public function set($variableName, $value) {
		$this->viewVars[$variableName] = $value;
	}

    /**
     * Sets variables to be used in the view
     * @param $params
     */
	public function setVars($params) {
		$this->viewVars = $params;
	}

    /**
     * Sets which view to use.
     * @param $view
     */
	public function setView($view) {
		$this->view = $view;
    }
    
    public function getView() {
        return $this->view;
    }

    /**
     * @param $router PageRouter
     */
	public function setRouter(PageRouter $router) {
	    $this->router = $router;
    }

    /**
     * Filters a string.
     * @param $str
     * @return mixed
     */
    public function filter($str) {
        return filter_var($str, FILTER_SANITIZE_STRING,
            FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
    }

    /**
     * Filters an integer.
     * @param $int
     * @return mixed
     */
    public function filterInt($int) {
        return filter_var($int, FILTER_SANITIZE_NUMBER_INT,
            FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
    }

    public static function debug($array) {
        echo "<pre>".htmlspecialchars(json_encode($array, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES))."</pre>";
    }

    public static function printStr($str) {
        echo "<pre>".$str."</pre>";
    }

    public function disableView($is_json = false) {
        $this->disableView = true;
        $this->json_output = $is_json;
    }

    public function isJson() {
        return $this->json_output;
    }

    public function isViewDisabled() {
        return $this->disableView;
    }

    public function getCookies() {
        return $this->cookies;
    }

    public function getRequest() {
        return $this->request;
    }

    public function getRouter() {
        return $this->router;
    }

    public function getCsrf() {
        return $this->csrf;
    }

    public function redirect($location, $internal = true) {
        $this->request->redirect($location, $internal);
    }

    public function delayedRedirect($url, $time, $internal = false) {
        $this->request->delayedRedirect($url, $time, $internal);
    }

    public function getViewContents($view, $vars = []) {
        $loader = new Template('app/views');
        $loader->setCacheEnabled(false);

        try {
            $template = $loader->load($view);
            return $template->render($vars);
        } catch (Exception $e) {
            return null;
        }
    }

    public function getPurifier() {
        
        $allowed_html = [
            'div[class]',
            'span[style]',
            'a[href|class|target]',
            'img[src|class|data-src]',
            'h1','h2','h3',
            'p[class]',
            'strong','em',
            'ul','u','ol','li',
            'table[class]','tr','td','th','thead','tbody'
        ];

        if (!$this->purifier) {
            $config = HTMLPurifier_Config::createDefault();
            $config->set("Core.Encoding", 'utf-8');
            $config->set('AutoFormat.RemoveEmpty', true);
            $config->set("HTML.Allowed", implode(',', $allowed_html));
            $config->set('HTML.AllowedAttributes', 'src, height, width, alt, href, class, style, data-src');

            $def = $config->getHTMLDefinition(true);
            $def->addAttribute('img', 'data-src', 'Text');

            $this->purifier = new HTMLPurifier($config);
        }

        return $this->purifier;
    }

    public function purify($text) {
        $text  = $this->getPurifier()->purify($text);
        $text  = preg_replace( "/\r|\n/", "", $text);
        $text  = preg_replace('/[^\00-\255]+/u', '', $text);
        return $text;
    }
}