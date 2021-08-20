<?php
declare(strict_types = 1);

final class Route {

    private static array $routes = [];

    private array $filter = [];

    private $runner;

    private static ?self $matchedRoute = null;

    private ?string $subDomain = null;

    private ?string $document = null;

    private ?string $action = null;

    private static $cb404;

    private static $cb405;

    private static array $params = [];

    private bool $all = false;

    private ?string $name = null;

    private bool $api = false;

    private static array $sdMatches = [];

    private array $sdFilter = [];

    private array $cssFiles = [];

    private array $jsFiles = [];

    private const SD_PARAM_PATT = '\{([a-zA-Z\d]+)\}';

    private const PARAM_PATT = ':([a-zA-Z]+[a-zA-Z\d_-]+)';

    private function __construct(
        private string $url,
        private string|array|null $method = null
        ) {
        self::$routes[] = $this;
    }

    public function setCssFiles(string ...$files) : self {
        $this->cssFiles = $files;
        return $this;
    }

    public function addCssFile(string $file) : self {
        $this->cssFiles[] = $file;
        return $this;
    }

    public function addJsFile(string $file) : self {
        $this->jsFiles[] = $file;
        return $this;
    }

    public function setJsFiles(string ... $files) : self {
        $this->jsFiles = $files;
        return $this;
    }

    public function getCssFiles() : array {
        return $this->cssFiles;
    }

    public function getJsFiles() : array {
        return $this->jsFiles;
    }

    public static function matched() : ?self {
        return self::$matchedRoute;
    }

    public function getMethod() : string|array|null {
        return $this->method;
    }

    public function setAll(bool $all) : self {
        $this->all = $all;
        return $this;
    }

    public function isAll() : bool {
        return $this->all;
    }

    public function hasParam(string $paramName) : bool {
        return isset(self::$params[$paramName]);
    }

    public function setApi(bool $api) : self {
        $this->api = $api;
        return $this;
    }

    public function isApi() : bool {
        return $this->api;
    }

    public function getParam(string $paramName) : ?string {
        return self::$params[$paramName] ?? null;
    }

    public function setDocument(string $document) : self {
        $this->document = $document;
        return $this;
    }

    public function setAction(string $action) : self {
        $this->action = $action;
        return $this;
    }

    public function hasAction() : bool {
        return isset($this->action);
    }

    public function getAction() : ?string {
        return $this->action ?? null;
    }

    public function hasDocument() : bool {
        return isset($this->document);
    }

    public function getDocument() : ?string {
        return $this->document;
    }

    public function getSubDomain() : ?string {
        return $this->subDomain;
    }

    public static function any(string $url) : self {
        return new self($url);
    }

    public function setSubDomain(string $sd) : self {
        $this->subDomain = $sd;
        return $this;
    }

    public function setRunner(callable $runner) : self {
        $this->runner = $runner;
        return $this;
    }

    public static function matches(array $match, string $url) : self {
        return new self($url, $match);
    }

    public function hasRunner() : bool {
        return isset($this->runner);
    }

    public function getRunner() : ?callable {
        return $this->runner;
    }

    public function hasFilter(string $key) : bool {
        return isset($this->filter[$key]);
    }

    public function isGet() : bool {
        return $this->method == 'get';
    }

    public function isPost() : bool {
        return $this->method == 'post';
    }

    public function getFilter(string $key) : ?callable {
        return $this->filter[$key] ?? null;
    }

    public function isPut() : bool {
        return $this->method == 'put';
    }

    public function isDelete() : bool {
        return $this->method == 'delete';
    }

    public function isPatch() : bool {
        return $this->method == 'patch';
    }

    public static function get(string $url) : self {
        return new self($url, 'get');
    }

    public static function post(string $url) : self {
        return new self($url, 'post');
    }

    public static function delete(string $url) : self {
        return new self($url, 'delete');
    }

    public static function put(string $url) : self {
        return new self($url, 'put');
    }

    public static function patch(string $url) : self {
        return new self($url, 'patch');
    }

    public function setFilter(array $filter) : self {
        $this->filter = $filter;
        return $this;
    }

    public function getUrl() : string {
        return $this->url;
    }

    public static function group(array $criteria, array $routes) : void {
        foreach($routes as $route) {
            if(isset($criteria['subdomain'])) $route->setSubDomain($criteria['subdomain']);
            if(isset($criteria['document'])) $route->setDocument($criteria['document']);
            if(isset($criteria['action'])) $route->setAction($criteria['action']);
        }
    }

    public function setName(string $name) : self {
        $this->name = $name;
        return $this;
    }

    public function getName() : ?string {
        return $this->name;
    }

    public static function findRouteByName(string $name) : ?self {
        foreach(self::$routes as $route) {
            if($route->getName() == $name) return $route;
        }
        return null;
    }

    public static function error404(?callable $cb = null) : void {
        self::$cb404 = $cb;
    }

    public static function error405(?callable $cb = null) : void {
        self::$cb405 = $cb;
    }

    public static function handle(callable $cb) : void {
        $res = null;
        foreach(self::$routes as $route) {
            $matched = self::isMatched($route);
            if($matched === true) {
                $res = $route;
                self::$matchedRoute = $res;
                break;
            }
            if($matched === 405) {
                $res = ['action' => 'error405'];
                if(isset(self::$cb405)) $res['cb'] = self::$cb405;
                break;
            }
        }
        if(is_null($res)) {
            $res = ['action' => 'error404'];
            if(isset(self::$cb404)) $res['cb'] = self::$cb404;
        }
        call_user_func($cb, $res);
    }

    public function hasSubDomain() : bool {
        return isset($this->subDomain);
    }

    public function setSubDomainFilter(array $filter) : self {
        $this->sdFilter = $filter;
        return $this;
    }

    public function getSubDomainFilter(string $filter) : ?callable {
        return $this->sdFilter[$filter] ?? null;
    }

    public function hasSubDomainFilter(string $filter) : bool {
        return isset($this->sdFilter[$filter]);
    }

    public function getSubDomainParam(string $param) : ?string {
        return self::$sdMatches[$param] ?? null;
    }

    private static function subDomainMatches(self $sd, array &$matches = []) : bool {
        $sdVal = $sd->getSubDomain();
        $rsdVal = getSubDomain();
        $sdSplit = explode('.', $sdVal);
        $rsdSplit = explode('.', $rsdVal);
        if(count($rsdSplit) != count($sdSplit)) return false;
        $res = '';
        $params = [];
        foreach($sdSplit as $k => $param) {
            if(preg_match('/' . self::SD_PARAM_PATT . '/', $param, $m)) {
                if($sd->hasSubDomainFilter($m[1])) {
                    if(!call_user_func($sd->getSubDomainFilter($m[1]), $rsdSplit[$k])) return false;
                }
                $value = $rsdSplit[$k];
                $params[$m[1]] = $value;
            }
            else $value = $param;
            if($res) $res .= '.';
            $res .= $value;
        }
        if(!empty($params)) $matches = $params;
        return $res == $rsdVal;
    }

    private static function isMatched(self $route) : bool|int {
        $reqUri = $_SERVER['REQUEST_URI'];
        $uri = $route->getUrl();
        $sdMatches = [];
        if(!$route->isAll()) {
            if($route->hasSubDomain() && (!hasSubDomain() || !self::subDomainMatches($route, $sdMatches))) return 404;
            if(hasSubDomain() && (!$route->hasSubDomain() || !self::subDomainMatches($route, $sdMatches))) return 404;
        }
        if(str_contains($reqUri, '?')) $reqUri = substr($reqUri, 0, strpos($reqUri, '?'));
        if($reqUri == $uri) {
            if(!self::methodMatches($route->getMethod())) return 405;
            if(!empty($sdMatches)) self::$sdMatches = $sdMatches;
            return true;
        }
        if($reqUri == '/' || $uri == '/') return 404;
        $uri = substr($uri, 1);
        $reqUri = substr($reqUri, 1);
        $uriSplit = explode('/', $uri);
        $reqSplit = explode('/', $reqUri);
        if(count($reqSplit) != count($uriSplit)) return 404;
        $res = '';
        $params = [];
        foreach($uriSplit as $k => $curl) {
            if(preg_match('/' . self::PARAM_PATT . '/', $curl, $matches)) {
                $value = $reqSplit[$k];
                $paramName = $matches[1];
                if($route->hasFilter($paramName)) {
                    $filter = $route->getFilter($paramName);
                    if(!call_user_func($filter, $value)) return 404;
                }
                $params[$paramName] = $value;
            }
            else $value = $curl;
            if($res) $res .= '/';
            $res .= $value;
        }
        if($res != $reqUri) return 404;
        if(!self::methodMatches($route->getMethod())) return 405;
        if(!empty($params)) self::$params = $params;
        if(!empty($sdMatches)) self::$sdMatches = $sdMatches;
        return true;
    }

    private static function methodMatches(string|array|null $method) : bool {
        if(is_string($method)) return strtolower($method) == strtolower($_SERVER['REQUEST_METHOD']);
        else if(is_array($method)) return in_array(strtolower($_SERVER['REQUEST_METHOD']), array_map('strtolower', $method));
        return true;
    }

}