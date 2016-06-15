<?php

  use Illuminate\Database\Capsule\Manager as Capsule;

  $Capsule = new Capsule;

  $Capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => 'localhost',
    'database'  => 'DB_NAME',
    'username'  => 'USERNAME',
    'password'  => 'PASSWORD',
    'charset'   => 'utf8',
    'collation' => 'utf8_general_ci',
    'prefix'    => ''
  ]);

  $Capsule->setAsGlobal();
  $Capsule->bootEloquent();

  use Illuminate\Database\Schema\Blueprint as Blueprint;
