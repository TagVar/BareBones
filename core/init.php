<?php

  //Require Composer Dependencies
  require_once("../libs/BareBones/autoload.php");

  //Ignition Configuration Files
  require_once("../config/database.php");

  //Require Core Classes
  require_once("barebones/app.php");
  require_once("barebones/controller.php");
  require_once("barebones/model.php");

  Twig_Autoloader::register();
