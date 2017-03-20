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
    private function addMiddleware($middleware)
    {
      $requestType = $_SERVER["REQUEST_METHOD"];
      end(\BareBones\Route::$registeredRoutes->$requestType);
      $routeIndex = key(\BareBones\Route::$registeredRoutes->$requestType);
      \BareBones\Route::$registeredRoutes->$requestType[$routeIndex]->middleware[] = $middleware;
    }
    function middleware($middleware)
    {
      if (!is_array($middleware))
        $this->addMiddleware($middleware);
      else
      {
        foreach($middleware as $requestedMiddleware)
        {
          $this->addMiddleware($requestedMiddleware);
        }
      }
    }
  }
