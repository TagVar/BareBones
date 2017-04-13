<?php

  namespace BareBones\Router\Helpers\Services;

  class Uri
  {
    static function parseUri()
    {
      if (isset($_SERVER['REQUEST_URI']))
        return explode("/", filter_var(trim($_SERVER['REQUEST_URI'], "/"), FILTER_SANITIZE_URL));
      else
        return [];
    }
    static function getUri()
    {
      $uriArray = self::parseUri();
      return implode("/", $uriArray);
    }
    static function matchUri($route, $request)
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
    static function getUriVariables($route, $request)
    {
      if (self::matchUri($route, $request))
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
  }
