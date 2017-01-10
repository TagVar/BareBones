<?php

  namespace BareBones;

  class Model extends \Eloquent
  {
    public $timestamps = false;
    protected function requireModel($model, $namespace = "")
    {
      function pullModel($model, $namespace = "")
      {
        if (isset($model))
        {
          $modelPath = "../assets/models/";
          $model = trim($namespace, "\\");
          if (str_replace(" ", "", $namespace) != "")
            $namespace = trim($namespace, "\\") . "\\";
          if (file_exists("" . str_replace('\\', '/', $namespace) . "$model.php"))
          {
            require_once($modelPath . str_replace('\\', '/', $namespace) . "$model.php");
            if (class_exists("models\\" . $namespace . $model))
              return true;
            else
              return false;
          }
          else
            return false;
        }
        else
          return false;
      }
      if (is_array($model))
      {
        $succesfulAddCounter = 0;
        foreach($model as $modelToPull)
        {
          if (is_array($modelToPull))
          {
            $argumentArray = array();
            if (isset($modelToPull[0]))
              $argumentArray[0] = $modelToPull[0];
            if (isset($modelToPull["model"]))
              $argumentArray[0] = $modelToPull["model"];
            if (isset($modelToPull[1]))
              $argumentArray[1] = $modelToPull[1];
            if (isset($modelToPull["namespace"]))
              $argumentArray[1] = $modelToPull["namespace"];
            if (pullModel($argumentArray[0], $argumentArray[1]))
              $succesfulAddCounter++;
          }
          else
          {
            if (pullModel($modelToPull))
              $succesfulAddCounter++;
          }
        }
        if ($succesfulAddCounter === count($model))
          return true;
        else
          return false;
      }
      else
      {
        return pullModel($model, $namespace);
      }
    }
  };
