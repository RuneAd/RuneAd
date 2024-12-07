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
            'index' => [
                'index' => [
                    'title' => 'PlayZanaris | The Ultimate Toplist for Project Zanaris Community Servers',
                    'meta'  => 'Discover PlayZanaris, the leading toplist dedicated to Jagex-hosted Project Zanaris servers. Explore and join unique, player-hosted OSRS community servers today!'
                ]
            ],
            'pages' => [
                'docs' => [
                    'title' => 'Documentation | PlayZanaris | Jagex Community Server Toplist',
                    'meta'  => 'Learn how to integrate your community server with PlayZanaris. Get started with Project Zanaris today!'
                ],
                'updates' => [
                    'title' => 'Update Log | PlayZanaris | Community Server Toplist',
                    'meta'  => 'Stay informed with the latest updates and improvements for PlayZanaris, your go-to community server toplist.'
                ],
                'sitemap' => [
                    'title' => 'Sitemap | PlayZanaris | Community Server Directory',
                    'meta'  => 'Navigate PlayZanaris with ease. View the full directory of Project Zanaris community servers and resources.'
                ],
                'stats' => [
                    'title' => 'Voting Stats | PlayZanaris | Community Server Toplist',
                    'meta'  => 'View global statistics of PlayZanaris, including votes, users, and server counts.'
                ],
                'terms' => [
                    'title' => 'Terms of Service | PlayZanaris | Community Server Toplist',
                    'meta'  => 'Review the Terms of Service for PlayZanaris, the leading toplist for Jagex-hosted community servers.'
                ],
                'contact' => [
                    'title' => 'Contact Us | PlayZanaris | Community Server Support',
                    'meta'  => 'Reach out to PlayZanaris support for assistance with your Project Zanaris community server listing.'
                ],
                'privacy' => [
                    'title' => 'Privacy Policy | PlayZanaris | Community Server Toplist',
                    'meta'  => 'Understand how PlayZanaris protects your data. Review our privacy policy for more details.'
                ],
                'faq' => [
                    'title' => 'FAQ | PlayZanaris | Community Server Toplist',
                    'meta'  => 'Find answers to common questions about PlayZanaris and hosting Project Zanaris community servers.'
                ]
            ],
            'profile' => [
                'index' => [
                    'title' => 'My Profile | PlayZanaris',
                    'meta'  => 'Manage your PlayZanaris profile and server listings with ease.'
                ],
                'add' => [
                    'title'  => 'Add Server | PlayZanaris | Jagex Community Server Toplist',
                    'meta'  => 'List your Jagex-hosted community server on PlayZanaris and join the ultimate Project Zanaris directory.'
                ],
                'edit' => [
                    'title' => 'Edit Server | PlayZanaris | Community Server Toplist',
                    'meta'  => 'Update your server details on PlayZanaris. Keep your community server listing accurate and up-to-date.'
                ]
            ],
            'blog' => [
                'index' => [
                    'title' => 'Blog | PlayZanaris | Community Server News',
                    'meta'  => 'Read the latest news, tips, and updates about Jagex-hosted community servers on the PlayZanaris blog.'
                ],
                'add' => [
                    'title' => 'Add Blog Post | PlayZanaris | Community Server News',
                    'meta'  => 'Share your insights and stories about Project Zanaris community servers. Create a blog post on PlayZanaris.'
                ]
            ],
            'tools' => [
                'index' => [
                    'title' => 'Tools | PlayZanaris | Community Server Resources',
                    'meta' => 'Access tools and resources to enhance your Jagex-hosted community server experience on PlayZanaris.'
                ],
                'itemdb' => [
                    'title' => 'OSRS Item Database | PlayZanaris | Community Server Tools',
                    'meta'  => 'Find detailed information on OSRS items with the PlayZanaris Item Database, tailored for community servers.'
                ],
                'map' => [
                    'title' => 'Interactive OSRS Map | PlayZanaris | Community Server Tools',
                    'meta'  => 'Explore the OSRS world with the interactive map on PlayZanaris, perfect for Project Zanaris servers.'
                ],
                'xptable' => [
                    'title' => 'Experience Table | PlayZanaris | Community Server Tools',
                    'meta'  => 'Use the OSRS Experience Table on PlayZanaris to plan your leveling goals on Project Zanaris servers.'
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
            'title' => 'PlayZanaris | The Ultimate Toplist for Project Zanaris Community Servers',
            'meta'  => 'Discover PlayZanaris, the leading toplist dedicated to Jagex-hosted Project Zanaris servers. Explore and join unique, player-hosted OSRS community servers today!'
        ];
    }

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
            $config->set('HTML.SafeIframe', true);
            $config->set('URI.SafeIframeRegexp', '%^(https?:)?//(www\.youtube(?:-nocookie)?\.com/embed/|player\.vimeo\.com/video/)%'); //allow YouTube and Vimeo

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