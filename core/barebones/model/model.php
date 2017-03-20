<?php

  namespace BareBones;

  class Model extends \Eloquent
  {
    use \BareBones\ModelTraits\RequireModel;
    use \BareBones\ModelTraits\GetModel;
    public $timestamps = false;
  };
