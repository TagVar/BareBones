<?php

  require_once("../core/init.php");

  $app = new App;

  $app->all("/foo/$", function($method) use($app) {
    if ($app->setController("test"))
    {
      $app->controller->$method();
    }
  });
  $app->get("/testing", function() {
    echo "Testing2!";
  });
  $app->easyRoute();
