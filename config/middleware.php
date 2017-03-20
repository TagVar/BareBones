<?php

namespace BareBones\Config

class Middleware
{
  // Register all available middleware.
  public $register = [];
  // Assign middleware to all routes.
  public $allRoutes = [];
  // Assign middleware to groups.
  public $groups = [];
}
