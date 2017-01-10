<?php

  namespace BareBones;

  class Controller
  {
    private $twigLoader;
    private $twig;
    public function __construct()
    {
      $this->twigLoader = new \Twig_Loader_Filesystem("../assets/views/");
      $this->twig = new \Twig_Environment($this->twigLoader);
    }
    protected function getModel($model, $namespace = "")
    {
      if (isset($model))
      {
        $model = trim($model, "\\");
        if (str_replace(" ", "", $namespace) != "")
          $namespace = trim($namespace, "\\") . "\\";
        if (file_exists("../assets/models/" . str_replace('\\', '/', $namespace) . "$model.php"))
        {
          require_once("../assets/models/" . str_replace('\\', '/', $namespace) . "$model.php");
          $fullyQualifiedModelName = "models\\" . $namespace . $model;
          if (class_exists($fullyQualifiedModelName))
            return new $fullyQualifiedModelName;
          else
            return false;
        }
        else
          return false;
      }
      else
        return false;
    }
    protected function renderView($viewName, $viewData = [], $path = "")
    {
      $viewData["staticPath"] = "public/";
      $path = trim($path, "/") . "/";
      if (file_exists("../assets/views/$path" . "$viewName"))
      {
        echo $this->twig->render($path . $viewName, $viewData);
      }
      else
        return false;
    }
  }
