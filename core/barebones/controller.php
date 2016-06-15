<?php

  class Controller
  {
    private $twigLoader;
    private $twig;
    public function __construct()
    {
      $this->twigLoader = new Twig_Loader_Filesystem("../assets/views/");
      $this->twig = new Twig_Environment($this->twigLoader);
    }
    protected function getModel($model, $path = "")
    {
      $path = trim($path, "/") . "/";
      if (file_exists("../assets/models/$path" . "$model.php"))
      {
        require_once("../assets/models/$path" . "$model.php");
        if (class_exists($model))
          return new $model();
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
