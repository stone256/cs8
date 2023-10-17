<?php

/**
 * handle routing
 */
/***
  1. groups
  2. pre
  3. before
  4. after
  5. params /:id/...
  6. fallback
  7. * naming // only default name
  8. * throttling
  9. * listing 
 */

//grouping

//$routers =: Router::set('/sitemin/test','/sitemin/login@test')//default name: sitemin.login->test


//Router::group([$routers])->pre(['/sitemin/login@test','/sitemin/login@test2'])->before(['aaaa'])->>after(['bbbb']);

function routing($routes = false)
{
    static $_Router;
    //singleton
    $router = $_Router ?? $_Router = new Router;
    //set routes if asked
    if ($routes)
        $router->set($routes);
    return $router;
}


class Router
{
    public static $routes = [];

    public static $last_data = [];

    public function match($uri)
    {
        $matches = [];
        //find all matches
        foreach (self::$routes as $k => $v) {
            if (preg_match($v['pattern'], $uri)) {
                $matches[$k] = $v;
            }
        }
        if (!count($matches))
            return false;

        //get longest match
        ksort($matches);
        $route = array_pop($matches);

        //generate embedded params like /xxx/:id/....
        preg_match_all($route['pattern'], $uri, $vars);
        $vars = $vars ?? [[]];
        array_shift($vars);

        preg_match_all('/(?<=\:).*?(?=\/|$)/ims', $route['key'], $names);
        foreach ($names[0] ?? [] as $k => $v) {
            $route['params'][$v] = array_shift($vars)[0] ?? '';
        }
        foreach ($route['params'] ?? [] as $k => $v) {
            _request($k, $v);
        }
        return $route;
    }

    public function list()
    {
        return self::$routes;
    }

    //save routes info
    public function set($routes)
    {
        $routes = is_array($routes) ? $routes : [$routes];
        $last_data = [];
        foreach ($routes as $k => $v) {
            if ($k == 'session()') {
                if (session_status() === PHP_SESSION_ACTIVE) continue;
                session_start();
                $_SESSION['__start__'] = _time();
                continue;
            }
            $route = str_replace('//', '/', "/$k");
            $controller = $this->parse_controller($v);
            $last_data[$route] = self::$routes[$route] = [
                'controller' => $controller,
                'key' => $k,
                'pattern' => $this->match_pattern($k),
                'name' => $controller['name'],
            ];
        }
        self::$last_data = $last_data;
        return $this;
    }

    //apply to last data group
    public function prefix($pre)
    {
        if (!count(self::$last_data))
            return $this;
        $last_data = [];
        foreach (self::$last_data as $k => $v) {
            $route = str_replace('//', '/', "/{$pre}/{$k}");
            self::$routes[$k]['pattern'] = $this->match_pattern($route);
            self::$routes[$k]['key'] = $route;
            $last_data[$route] = self::$routes[$route] = self::$routes[$k];
            unset(self::$routes[$k]);
        }
        self::$last_data = $last_data;
        return $this;
    }

    //before call main controller
    public function before($model)
    {
        if (!count(self::$last_data))
            return $this;
        if (!is_array($model))
            $model = [$model];
        foreach ($model as $k => $v)
            $model[$k] = $this->parse_model($v);
        foreach (self::$last_data as $k => $v) {
            self::$routes[$k]['before'] = $model;
        }
        return $this;
    }

    //after call main controller
    public function after($model)
    {
        if (!count(self::$last_data))
            return $this;
        if (!is_array($model))
            $controllers = [$model];
        foreach ($model as $k => $v)
            $model[$k] = $this->parse_model($v);
        foreach (self::$last_data as $k => $v) {
            self::$routes[$k]['after'] = $model;
        }
        return $this;
    }


    // model and action info
    protected function parse_model($model)
    {

        if (is_array($model)) {
            $tmp = [0, ...$model];
            $type = 'namespace';
        } else {
            $type = 'model';
            preg_match('/(.*?)\@(.*)/ims', $model, $tmp);
        }
        return [
            'type' => $type,
            'class' => $tmp[1],
            'action' => $tmp[2],
        ];
    }

    // controller and action info
    protected function parse_controller($controller)
    {

        if (is_array($controller)) {
            $result = [
                'type' => 'namespace',
                'class' => $controller[0],
                'action' => $controller[1],
                'name' => $controller[2] ?? uniqid()
            ];
        } else {
            $controller = str_replace('//', '/', "/$controller");
            // welcome/test@example1|welcome.example1
            preg_match('/^(.*?)(\|([^\|].*?))?$/ims', $controller, $ctrl);

            preg_match('/(.*?([^\/]*?))\@(.*)/ims', $ctrl[1], $tmp);
            $result = [
                'type' => 'path',
                'path' => _X_MODULE . $tmp[1] . "Controller.php",
                'class' => preg_replace('/^_/ms', '', str_replace('/', '_', $tmp[1])) . 'Controller',
                'action' => $tmp[3],
                'name' => $ctrl[3] ?? uniqid()
            ];
        }
        return $result;
    }

    // create route match pattern
    protected function match_pattern($route)
    {
        $route = str_replace('//', '/', "/$route");
        $pattern = preg_replace('/\\\:.*?(?=\/|$)/ims', '(.*?)', preg_quote($route));
        $pattern = '/^' . str_replace('/', '\/', $pattern) . '(\/|\?|#|&|$)/ims';
        return $pattern;
    }
}
