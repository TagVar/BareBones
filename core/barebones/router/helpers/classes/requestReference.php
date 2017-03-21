<?php

  namespace BareBones\Router\Helpers\Classes;

  class RequestReference
  {
    private $route;
    private $requestType;
    function __construct($requestType, $route)
    {
      $this->requestType = $requestType;
      $this->route = $route;
    }
    private function addMiddleware($middleware, $parameters = null)
    {
      $requestType = $_SERVER["REQUEST_METHOD"];
      end(\BareBones\Route::$registeredRoutes->$requestType);
      $routeIndex = key(\BareBones\Route::$registeredRoutes->$requestType);
      $middlewareObject = new \stdclass;
      $middlewareObject->policy = $middleware;
      if (is_array($parameters))
        $middlewareObject->parameters = $parameters;
      \BareBones\Route::$registeredRoutes->$requestType[$routeIndex]->middleware[] = $middlewareObject;
    }
    function middleware($middleware, $parameters = null)
    {
      if (!is_array($middleware))
        $this->addMiddleware($middleware, $parameters);
      else
      {
        foreach($middleware as $requestedMiddleware)
        {
          if (!is_array($requestedMiddleware))
            $this->addMiddleware($requestedMiddleware);
          else
          {
            if (is_string($requestedMiddleware[1]))
              $requestedMiddleware[1] = array($requestedMiddleware[1]);
            $this->addMiddleware($requestedMiddleware[0], $requestedMiddleware[1]);
          }
        }
      }
    }
  }
