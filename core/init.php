<?php

  //Require Composer Dependencies
  require_once("../libs/BareBones/autoload.php");

  //Create Alternate Namespaces for Eloquent Classes
  class Blueprint extends \Illuminate\Database\Schema\Blueprint {};
  class Eloquent extends \Illuminate\Database\Eloquent\Model {};
  class DB extends \Illuminate\Database\Capsule\Manager {};

  //Require Core Classes
  require_once("barebones/app.php");
  require_once("barebones/router/router.php");
  require_once("barebones/controller.php");
  require_once("barebones/model/model.php");

  // Initialize Twig
  \Twig_Autoloader::register();
