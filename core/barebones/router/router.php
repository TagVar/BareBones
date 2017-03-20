<?php

  namespace BareBones;

  //Load Router Dependencies
  require_once("helpers/classes/requestReference.php");
  require_once("helpers/services/middleware.php");
  require_once("helpers/services/uri.php");

  class Route
  {
    static $registeredRoutes;
    static $notFoundCallback = false;
    static $routeFound = false;
    private static function registerRoute($requestType, $route, $callback)
    {
      $requestType = strtoupper($requestType);
      if (is_array($requestType))
      {
        foreach($requestType as $type)
          self::registerRoute($type, $route, $callback);
      }
      if (!isset(self::$registeredRoutes))
        self::$registeredRoutes = new \stdClass;
      $routeObject = new \stdClass;
      $routeObject->route = $route;
      $routeObject->function = $callback;
      $routeObject->middleware = [];
      if (isset(self::$registeredRoutes->$requestType))
        self::$registeredRoutes->$requestType[] = $routeObject;
      else
        self::$registeredRoutes->$requestType = array($routeObject);
      return new Router\Helpers\Classes\RequestReference($requestType, $route);
    }
    private static function executeRoute($route, $middleware, $callback)
    {
      $uriVariables = Router\Helpers\Services\Uri::getUriVariables($route, implode("/", Router\Helpers\Services\Uri::parseUri()));
      if (is_array($uriVariables))
      {
        Router\Helpers\Services\Middleware::runGlobalBefore($route);
        foreach($middleware as $requestedMiddleware)
          Router\Helpers\Services\Middleware::runBefore($requestedMiddleware, Router\Helpers\Services\Uri::getUri());
        call_user_func_array($callback, $uriVariables);
        self::$routeFound = true;
        Router\Helpers\Services\Middleware::runGlobalAfter($route);
        foreach($middleware as $requestedMiddleware)
        {
          Router\Helpers\Services\Middleware::runAfter($requestedMiddleware, Router\Helpers\Services\Uri::getUri());
        }
      }
    }
    static function get($uri, $callback)
    {
      return self::registerRoute("GET", $uri, $callback);
    }
    static function post($uri, $callback)
    {
      return self::registerRoute("POST", $uri, $callback);
    }
    static function put($uri, $callback)
    {
      return self::registerRoute("PUT", $uri, $callback);
    }
    static function delete($uri, $callback)
    {
      return self::registerRoute("DELETE", $uri, $callback);
    }
    static function route($requestTypes = [], $uri, $callback)
    {
      return self::registerRoute($requestTypes, $uri, $callback);
    }
    static function all($uri, $callback)
    {
      return self::registerRoute(["GET", "POST", "PUT", "DELETE"], $uri, $callback);
    }
    static function notFound($callback)
    {
      self::$notFoundCallback = $callback;
    }
    static function easyRoute(&$appReference)
    {
      if (!self::$routeFound)
      {
        $uri = Router\Helpers\Services\Uri::parseUri();
        $params = [];
        if ((isset($uri[0])) && (strlen(trim($uri[0])) > 0))
        {
          if ($appReference->controllerExists($uri[0]))
          {
            $appReference->setController($uri[0]);
            if ((isset($uri[1])) && (strlen(trim($uri[1])) > 0) && (method_exists($appReference->controller, $uri[1])))
            {
              if (count($uri) > 2)
                $params = array_values(array_slice($uri, 2));
              call_user_func_array([$appReference->controller, $uri[1]], $params);
              self::$routeFound = true;
            }
            else
            {
              if (count($uri) > 1)
                $params = array_values(array_slice($uri, 1));
              if (method_exists($appReference->controller, strtolower($_SERVER["REQUEST_METHOD"])))
              {
                $method = $_SERVER["REQUEST_METHOD"];
                $appReference->controller->$method($params);
                self::$routeFound = true;
              }
              else if (method_exists($appReference->controller, "index"))
              {
                $appReference->controller->index($params);
                self::$routeFound = true;
              }
            }
          }
        }
      }
    }
    static function run()
    {
      $requestMethod = $_SERVER['REQUEST_METHOD'];
      if (!self::$routeFound)
      {
        if (isset(self::$registeredRoutes->$requestMethod))
        {
          foreach(self::$registeredRoutes->$requestMethod as $registeredRoute) {
            if (Router\Helpers\Services\Uri::matchUri($registeredRoute->route, Router\Helpers\Services\Uri::getUri()))
            {
              self::executeRoute($registeredRoute->route, $registeredRoute->middleware, $registeredRoute->function);
              self::$routeFound = true;
              break;
            }
          }
          if (self::$routeFound != true && self::$notFoundCallback != false)
          {
            $callback = self::$notFoundCallback;
            $callback();
          }
        }
      }
    }
  }
