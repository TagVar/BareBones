<?php

  namespace BareBones;

  class Controller
  {
    use \BareBones\ModelTraits\RequireModel;
    use \BareBones\ModelTraits\GetModel;
    private $twigLoader;
    private $twig;
    public function __construct()
    {
      $this->twigLoader = new \Twig_Loader_Filesystem("../assets/views/");
      $this->twig = new \Twig_Environment($this->twigLoader);
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
