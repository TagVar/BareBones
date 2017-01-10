<?php

  namespace BareBones;

  class App
  {
    public $controller;
    public $routeFound = false;
    private $uri;
    private $relativePath;
    private $configuration = [];
    private function parseUri()
    {
      if (isset($_GET["uri"]))
        return explode("/", filter_var(trim($_GET["uri"], "/"), FILTER_SANITIZE_URL));
      else
        return [];
    }
    private function matchUri($route, $request)
    {
      $route = explode("/", trim($route, "/"));
      $request = explode("/", trim($request, "/"));
      if (count($route) == count($request))
      {
        $matches = 0;
        foreach($route as $key=>$piece)
        {
          if ($piece == "$")
            $matches++;
          else if ($piece == $request[$key])
            $matches++;
        }
        if ($matches == count($route))
          return true;
        else
          return false;
      }
      else
        return false;
    }
    private function getUriVariables($route, $request)
    {
      if ($this->matchUri($route, $request))
      {
        $route = explode("/", trim($route, "/"));
        $request = explode("/", trim($request, "/"));
        $variables = [];
        foreach ($route as $key=>$piece)
        {
          if ($piece == "$")
            $variables[] = $request[$key];
        }
        return $variables;
      }
      else
        return false;
    }
    private function executeRoute($requestType, $route, $callback)
    {
      if (!$this->routeFound)
        {
        if (is_array($requestType))
        {
          if (in_array($_SERVER['REQUEST_METHOD'], $requestType))
            $matched = true;
          else
            $matched = false;
        }
        else if (($_SERVER['REQUEST_METHOD'] == $requestType) || ($requestType == "ALL"))
          $matched = true;
        else
          $matched = false;
        if ($matched)
        {
          $uriVariables = $this->getUriVariables($route, implode("/", $this->uri));
          if (is_array($uriVariables))
          {
            call_user_func_array($callback, $uriVariables);
            $this->routeFound = true;
          }
        }
      }
    }
    function controllerExists($controller, $namespace = "")
    {
      $controllerPath = "../assets/controllers/";
      $controller = trim($controller, "\\");
      if (str_replace(" ", "", $namespace) != "")
        $namespace = trim($namespace, "\\") . "\\";
      //Check if controller name provided.
      if ((strlen(trim($controller)) > 0) && (file_exists($controllerPath . str_replace('\\', '/', $namespace) . $controller . ".php")))
      {
        require_once($controllerPath . str_replace('\\', '/', $namespace) . "$controller.php");
        if (class_exists("controllers\\" . $namespace . $controller))
          return true;
        else {
          echo "controllers\\" . $namespace . $controller;
          return false;
        }
      }
      else
        return false;
    }
    function setController($controller, $namespace = "") {
      if ($this->controllerExists($controller, $namespace))
      {
        $controller = trim($controller, "\\");
        if (str_replace(" ", "", $namespace) != "")
          $namespace = trim($namespace, "\\") . "\\";
        $fullyQualifiedControllerName = "controllers\\" . $namespace . $controller;
        $this->controller = new $fullyQualifiedControllerName($this->relativePath);
        return true;
      }
      else
        return false;
    }
    function checkDefaultMethod()
    {
      if (is_object($this->controller))
      {
        if (method_exists($this->controller, "index"))
          return true;
        else
          return false;
      }
      else
        return false;
    }
    function get($uri, $callback)
    {
      $this->executeRoute("GET", $uri, $callback);
    }
    function post($uri, $callback)
    {
      $this->executeRoute("POST", $uri, $callback);
    }
    function put($uri, $callback)
    {
      $this->executeRoute("PUT", $uri, $callback);
    }
    function delete($uri, $callback)
    {
      $this->executeRoute("DELETE", $uri, $callback);
    }
    function all($uri, $callback)
    {
      $this->executeRoute("ALL", $uri, $callback);
    }
    function route($requestTypes = [], $uri, $callback)
    {
      $this->executeRoute($requestTypes, $uri, $callback);
    }
    function notFound($callback)
    {
      if (!$this->routeFound)
      {
        $callback();
        $this->routeFound = true;
      }
    }
    function easyRoute()
    {
      if (!$this->routeFound)
      {
        if ((isset($this->uri[0])) && (strlen(trim($this->uri[0])) > 0))
        {
          if ($this->controllerExists($this->uri[0]))
          {
            $this->setController($this->uri[0]);
            if ((isset($this->uri[1])) && (strlen(trim($this->uri[1])) > 0) && (method_exists($this->controller, $this->uri[1])))
            {
              $params = [];
              if (count($this->uri) > 2)
                $params = array_values(array_slice($this->uri, 2));
              call_user_func_array([$this->controller, $this->uri[1]], $params);
              $this->routeFound = true;
            }
            else if ($this->checkDefaultMethod())
            {
              $this->controller->index();
              $this->routeFound = true;
            }
          }
        }
      }
    }
    function __construct()
    {
      //Set request URI.
      $this->uri = $this->parseUri();
      //Load Configuration
      $iniFiles = array_filter(scandir("../config"), function($scanResult) {
        return ("ini" === pathinfo($scanResult, PATHINFO_EXTENSION));
      });
      foreach($iniFiles as $iniFile)
        $this->configuration[basename($iniFile, ".php")] = parse_ini_file("../config/$iniFile");
      //Boot Eloquent
      $capsule = new \DB;
      $capsule->addConnection($this->configuration["database.ini"]["database"]);
      $capsule->setAsGlobal();
      $capsule->bootEloquent();
    }
  }
