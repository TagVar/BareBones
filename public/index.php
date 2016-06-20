<?php

  require_once("../core/init.php");

  $app = new BareBones\App;

  $app->get("/", function() use($app) {
    if ($app->setController("ctrl"))
    {
      $app->controller->index();
    }
  });
