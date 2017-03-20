<?php

  namespace BareBones;

  class App
  {
    public $controller;
    private $relativePath;
    private $configuration = [];
    function __construct()
    {
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
    function run()
    {
      Route::run();
    }
  }
