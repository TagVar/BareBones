<?php

  use BareBones\Route as Route;

  require_once("../core/init.php");

  $app = new BareBones\App;

  Route::get("test/$", function($test) {
    echo $test;
  })->middleware(["policies\\testTwo"]);

  $app->run();
