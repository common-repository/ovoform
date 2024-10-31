<?php

namespace Ovoform\Hook;

use Ovoform\BackOffice\MiddlewareHandler;
use Ovoform\BackOffice\Router\Router;

class ExecuteRouter{
    
    public function execute()
    {
        $routes = Router::$routes;
        foreach ($routes as $routeKey => $routeValue) {
            if (array_key_exists('uri',reset($routeValue))) {
                $route = reset($routeValue);
                $uri = $route['uri'];
                $regex = '/\{.*?\}/';
                preg_match_all($regex,$uri,$params);
                
                $paramMatch = '';
                $uri = preg_replace($regex, '', $uri);
                $exactUri = $uri;
                $exactUri = rtrim($exactUri,'/');
                foreach (reset($params) as $key => $param) {
                    $exactUri .= '/([a-z0-9]+)[/]?';
                    $paramMatch .= '$matches['.($key + 1).']/';
                }
                $exactUri = str_replace('//','/',$exactUri);
    
                if (empty($params)) {
                    add_rewrite_rule($route['uri'], 'index.php?ovoform_page='.$route['uri'], 'top');
                }else{
                    add_rewrite_rule($exactUri.'$', 'index.php?ovoform_page='.str_replace('//','/',$uri).$paramMatch, 'top' );
                }
            }
        }
        flush_rewrite_rules();
    }

    public function includeTemplate($template)
    {
        $noMatch = true;
        if (get_query_var('ovoform_page')) {
            $routes = Router::$routes;
            foreach ($routes as $routeKey => $routeValue) {
                if (array_key_exists('uri',reset($routeValue))) {
                    $currentUri = get_query_var('ovoform_page');
                    $route = reset($routeValue);
                    $regex = '/\{.*?\}/';
                    $uriExceptParam = preg_replace($regex, '', $route['uri']);
                    $uriExceptParam = str_replace('//','/',$uriExceptParam);
                    $params = [];
                    if (rtrim($uriExceptParam,'/') == rtrim($currentUri,'/')) {

                        $handler = new MiddlewareHandler();
                        $handler->filterGlobalMiddleware();

                        self::validateMethod($route['method']);
                        $noMatch = false;
                        if (array_key_exists('middleware',$route)) {
                            $middleware = $route['middleware'];
                            $handler = new MiddlewareHandler();
                            $handler->handle($middleware);
                        }
                        $action = $route['action'];
                        if(is_callable($action)){
                            $action(...$params);
                        }else{
                            $controller = new $action[0];
                            $method = $action[1];
                            $controller->$method(...$params);
                        }
                    }
                }
            }
            if ($noMatch) {
                global $wp_query;
                $wp_query->set_404();
                status_header(404);
                get_template_part(404);
                exit();
            }
        }
        return $template;
    }

    public function setQueryVar($vars)
    {
        $vars[] = 'ovoform_page';
        return $vars;
    }

    public static function executeAdminRouter()
    {
        if (ovoform_request()->page && ovoform_request()->module) {
            
            $action = ovoform_request()->module;
            $routes = Router::$routes;
            foreach ($routes as $routeKey => $routeValue) {
                foreach ($routeValue as $routerKey => $router) {
                    $handler = new MiddlewareHandler();
                    $handler->filterGlobalMiddleware();
                    if (array_key_exists('query_string',$router)) {
                        $route = $router;
                            if ($route['query_string'] == $action) {
                                self::validateMethod($route['method']);
                                if (array_key_exists('middleware',$route)) {
                                    $middleware = $route['middleware'];
                                    $handler->handle($middleware);
                                }
                                $controller = $route['action'][0];
                                $method = $route['action'][1];
                                return [$controller,$method];
                            }
                        }
                    }
                }
        }
        return [];
    }

    public static function validateMethod($methodName)
    {
        if ($methodName != 'any') {
            $reqMethod = sanitize_text_field($_SERVER['REQUEST_METHOD']);
            if ($reqMethod != strtoupper($methodName)) {
                throw new \Exception("$reqMethod method doesn't support for this route", 1);
            }
        }
    }
}