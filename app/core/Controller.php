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
                    return;
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
             return true;
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
        $this->set("route", $this->router->getCanonical());
        return true;
    }

    public function getPageMeta($controller, $action) {
        $pages = [
            'premium' => [
                'index' => [
                    'title' => 'Premium',
                    'meta'  => 'Buy premium on RuneAd to give your server a nice boost, which will increase traffic and visibility for your server!'
                ]
            ],
            'pages' => [
                'docs' => [
                    'title' => 'Documentation',
                    'meta'  => 'Integrate your website with our service, receive voting callback, and more!'
                ],
                'updates' => [
                    'title' => 'Update Log',
                    'meta'  => 'All updates that have been pushed for the toplist, and a list of contributors.'
                ],
                'stats' => [
                    'title' => 'Stats',
                    'meta'  => 'Global statistics showing votes, user, and server counts.'
                ],
                'terms' => [
                    'title' => 'Terms of Service',
                    'meta'  => 'Our terms of service.'
                ],
								'sponsor' => [
		                 'index' => [
		                     'title' => 'Sponsored Ads',
		                     'meta' => 'Sponsored ad spots that place you above all other on every page of the main listing.'
		                 ]
		             ]
		         ];

        if (in_array($controller, array_keys($pages))) {
            $actions = $pages[$controller];
            if (in_array($action, array_keys($actions))) {
                return $pages[$controller][$action];
            }
        }

        return [
            'title' => 'Servers',
            'meta'  => 'The most modern runescape private server toplist built to-date. Come join your favorite RSPS, or add your server today to start advertising with us!'
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
