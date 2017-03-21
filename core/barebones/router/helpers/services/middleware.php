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
    private static function parseMembers($members)
    {
      $parsedMembers = [];
      foreach($members as $member)
      {
        $memberObject = new \stdClass;
        if (is_array($member))
        {
          if (is_string($member[1]))
            $member[1] = array($member[1]);
          $memberObject->policy = $member[0];
          $memberObject->parameters = $member[1];
        }
        else
          $memberObject->policy = $member;
        $parsedMembers[] = $memberObject;
      }
      return $parsedMembers;
    }
    private static function groupMembers($group)
    {
      if (self::exists($group))
      {
        $fullyQualifiedGroupName = "\BareBones\Middleware\\$group";
        if (isset($fullyQualifiedGroupName::$members) && is_array($fullyQualifiedGroupName::$members))
          return self::parseMembers($fullyQualifiedGroupName::$members);
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
      return self::parseMembers($globalClass::$members);
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
      if (self::exists($middleware->policy))
      {
        if (self::isGroup($middleware->policy))
        {
          $members = self::groupMembers($middleware->policy);
          foreach ($members as $member)
          {
            self::run($when, $member, $route);
          }
        }
        else
        {
          $fullyQualifiedMiddlewareName = "\BareBones\Middleware\\" . $middleware->policy;
          if (method_exists($fullyQualifiedMiddlewareName, $when))
          {
            $parameters = [$route];
            if (isset($middleware->parameters))
              $parameters = array_merge($parameters, $middleware->parameters);
            call_user_func_array([$fullyQualifiedMiddlewareName, $when], $parameters);
          }
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
