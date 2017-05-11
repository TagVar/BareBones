<?php

  namespace BareBones;

  //Require Model Traits
  require_once("traits/getModel.php");
  require_once("traits/requireModel.php");

  class Model extends \Eloquent
  {
    function __construct()
    {
      parent::__construct();
    }
    use \BareBones\ModelTraits\RequireModel;
    use \BareBones\ModelTraits\GetModel;
    public $timestamps = false;
  };
