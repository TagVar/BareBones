<?php

  namespace BareBones\Router\Helpers\Services;

  class Middleware
  {
    private static function load($middleware)
    {
      if (file_exists("../assets/middleware/" . str_replace('\\', '/', $middleware) . ".php"))
      {
        require_once("../assets/middleware/" . str_replace('\\', '/', $middleware) . ".php");
        return true;
      }
      else
        return false;
    }
    private static function exists($middleware)
    {
      if (self::load($middleware) != false)
      {
        if (class_exists("\BareBones\Middleware\\" . str_replace('/', '\\', $middleware)))
          return true;
        else
          return false;
      } else
      {
        echo "\nBareBones: Unable to find middleware: $middleware\n";
        return false;
      }
    }
    private static function isGroup($middleware)
    {
      $namespaceParts = explode("\\", $middleware);
      if (strtolower($namespaceParts[0] == "groups"))
        return true;
    }
    private static function groupMembers($group)
    {
      if (self::exists($group))
      {
        $fullyQualifiedGroupName = "\BareBones\Middleware\\$group";
        if (isset($fullyQualifiedGroupName::$members) && is_array($fullyQualifiedGroupName::$members))
          return $fullyQualifiedGroupName::$members;
        else
          return false;
      }
      else
        return false;
    }
    private static function globalMembers()
    {
      require_once("../assets/middleware/global.php");
      $globalClass = "\BareBones\Middleware\GlobalMiddleware";
      return $globalClass::$members;
    }
    private static function runGlobal($when, $route)
    {
      $members = self::globalMembers();
      foreach($members as $member)
      {
        if ($when == "before")
          self::runBefore($member, $route);
        else if ($when == "after")
          self::runAfter($member, $route);
      }
    }
    private static function run($when, $middleware, $route)
    {
      if (self::exists($middleware))
      {
        if (self::isGroup($middleware))
        {
          $members = self::groupMembers($middleware);
          foreach ($members as $member)
          {
            self::run($when, $member, $route);
          }
        }
        else
        {
          $fullyQualifiedMiddlewareName = "\BareBones\Middleware\\" . $middleware;
          if (method_exists($fullyQualifiedMiddlewareName, $when))
            $fullyQualifiedMiddlewareName::$when($route);
        }
      }
      else
        return false;
    }
    static function runGlobalBefore($route)
    {
      self::runGlobal("before", $route);
    }
    static function runGlobalAfter($route)
    {
      self::runGlobal("after", $route);
    }
    static function runBefore($middleware, $route)
    {
      self::run("before", $middleware, $route);
    }
    static function runAfter($middleware, $route)
    {
      self::run("after", $middleware, $route);
    }
  }
