<?php

use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;
use Twig\TemplateWrapper;
use Twig\TwigFilter;

class Template extends FilesystemLoader {

    public $path;

    public function __construct($paths = [], $rootPath = null) {
        $this->path = $paths;
        parent::__construct($paths, $rootPath);
    }

    private $cache_enabled = true;

    /**
     * @param $path
     * @return TemplateWrapper|null
     */
    public function load($path) {
        try {
            $twig = new \Twig\Environment($this, !$this->cache_enabled ? [] : ['cache' => 'app/cache']);

            $twig->addFunction(new \Twig\TwigFunction('url', function ($string, $internal = true) {
                return $internal ? web_root . $string : $string;
            }));

            $twig->addFunction(new \Twig\TwigFunction('banner', function ($banner) {
                if (substr($banner, 0, 4) == "http") {
                    return $banner;
                } else {
                    return web_root.'public/img/banners/'.$banner;
                }
            }));

            $twig->addFunction(new \Twig\TwigFunction('api', function ($string) {
                return api_url.'/'.$string;
            }));

            $twig->addFunction(new \Twig\TwigFunction('stylesheet', function ($string) {
                return web_root.'public/css/' . $string . '';
            }));

            $twig->addFunction(new \Twig\TwigFunction('javascript', function ($string) {
                return web_root . 'public/js/' . $string . '';
            }));

            $twig->addFunction(new \Twig\TwigFunction('constant', function ($string) {
                return constant($string);
            }));

            $twig->addFunction(new \Twig\TwigFunction('curdate', function ($string) {
                return date($string);
            }));

            $twig->addFunction(new \Twig\TwigFunction('debugArr', function ($string) {
                return json_encode($string, JSON_PRETTY_PRINT);
            }));

            $twig->addFunction(new \Twig\TwigFunction('in_array', function ($needle, $haystack) {
                return in_array($needle, $haystack);
            }));

            $twig->addFunction(new \Twig\TwigFunction('substr', function ($str, $start, $end) {
                return substr($str, $start, $end);
            }));

            $twig->addFilter(new TwigFilter('array_chunk', function($array, $limit) {
                return array_chunk($array, $limit);
            }));

            $twig->addFilter(new TwigFilter('json_decode', function($array, $assoc = true) {
                return json_decode($array, $assoc);
            }));


            $twig->addFilter(new TwigFilter('implode', function($array, $delimiter) {
                return implode($delimiter, $array);
            }));

            $twig->addFilter(new TwigFilter('strtotime', function($date) {
                return strtotime($date);
            }));

            $twig->addFunction(new \Twig\TwigFunction('time', function () {
                return time();
            }));

            $twig->addFunction(new \Twig\TwigFunction('avatar', function ($user_id, $avatar_hash) {
                return Functions::getAvatarUrl($user_id, $avatar_hash);
            }));

            $twig->addFunction(new \Twig\TwigFunction('friendlyTitle', function ($title) {
                return Functions::friendlyTitle($title);
            }));

            $twig->addFunction(new \Twig\TwigFunction('elapsed', function ($int) {
                return Functions::elapsed($int);
            }));

            $twig->addFunction(new \Twig\TwigFunction('timeLeft', function ($int) {
                return Functions::getTimeLeft($int);
            }));

            return $twig->load($path . '.twig');
        } catch (/*LoaderError|RuntimeError|SyntaxError*/Exception $e) {
            return null;
        }
    }

    public function setCacheEnabled($val) {
        $this->cache_enabled = $val;
    }
}
