<?php

  namespace BareBones\ModelTraits;

  trait GetModel
  {
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
  }
