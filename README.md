#BareBones
An extremely portable PHP MVC framework written to compliment front-end heavy applications.

#Purpose and Features
BareBones is a light PHP MVC framework designed to compliment front-end heavy applications, particularly Angular and other JavaScript Single-Page-Applications. BareBones reverses the typical client-server file organization. Rather than a back-end framework containing a public directory which hosts the client application, BareBones fits into a single directory on a client application. BareBones is intended for use with Apache2.

Although BareBones main intended function is to compliment JavaScript Single-Page-Applications, it works just as well as a light framework to build a standalone application in.

BareBones models extend the Eloquent ORM, and views extend the Twig templating engine. The framework incorporates user-defined routing, but also uses Easy Routing. Easy Routing is a function which provides default behavior to the router. This allows you to define complex routes, but makes it easy to establish simple routes without a manual definition.

#Installation
Installing BareBones is a very simple process.

- Clone this repository into any directory within Apache2's document root. (Yes, it really will work from any directory.)
- Edit the database configuration file located at 'config/database.ini'. Enter the desired database definition in `database.ini`

Ex)
```php
database[driver] = "mysql"
database[host] = "localhost"
database[database] = "MyDatabaseName"
database[username] = "MySqlUsername"
database[password] = "MySqlPassword"
database[charset] = "utf8"
database[collation] = "utf8_general_ci"
database[prefix] = ""
```

#Developer Documentation
Writing an application with BareBones is fairly straight forward. BareBones has an easy to understand directory structure, and simple syntax.

##System Requirements

BareBones has been tested on a Debian 8 Jessie server running PHP7, Apache 2.4 with `mod_rewrite` enabled, and a MySQL database. BareBones should work on PHP >= 5.6 and Apache 2.* as well as any distribution of Linux. It requires the `mod_rewrite` Apache module be enabled.

##File Structure

`libs`: BareBones keeps all of it's required dependencies in `libs/BareBones`. You are free to add your application's dependendies anywhere within the `libs` directory including the BareBones sub-directory so long as you do not cause path conflicts with BareBone's dependencies.
`public`: This directory contains `index.php` which will be the root of your application. All requests are forwarded to this directory. Any front-end dependencies that need to be internal to your BareBones application should go here. Keep in mind that BareBones is intended to compliment JavaScript frameworks like Angular and be contained in a sub-directory of the client application. If you're using BareBones for this purpose, you may not even need to add files to the public directory.
`config`: This directory contains BareBone's configuration files. You may use this directory for your own application's configuration files as long as they do not create a path conflict with any of BareBone's configuration files.
`assets`: This directory contains 3 sub-directories: `controllers`, `models`, and `views`. Controllers, models, and views should be contained in their related directories. You will not be able to access views in the controllers directory, etc. Each of these sub-directories can contain sub-directories containing their related files without affecting their accesibility. (I.E. You can put a controller in `controllers/main`, etc.)
`core`: This directory contains the core BareBone's classes as well as an initialization file. No editing is required.

##Namespacing

BarBones contains itself in the `BareBones` namespace. BareBones extends [Laravel's Eloquent](https://laravel.com/docs/5.1/eloquent) which lives in the `Illuminate` namesapce. The `BareBones` and `Illuminate` namespaces will therefore not be available to your application. This also means that when creating controller and model classes you will need to extend your class with `BareBones\Controller` and `BareBones\Model` respectively. You must also use `BareBones\App` when creating an instantiation of the BareBone's `App` class.

Ex)
```php
<?php

  /* Access BareBones classes using namespace path...*/
  $app = new BareBones\App;
  class BareBonesController extends BareBones\Controller
  {
    /* Methods, propoerties, etc. */
  }
  class BareBonesModel extends BareBones\Model
  {
    /* Methods, propoerties, etc. */
  }
```

Alternatively, simply decalare the namespace at the top of your file like this...

```php
<?php

  namespace BareBones;

  /* Access to all BareBones classes without namespace path...*/
  $app = new App;
  class BareBonesController extends Controller
  {
    /* Methods, propoerties, etc. */
  }
  class BareBonesModel extends Model
  {
    /* Methods, propoerties, etc. */
  }
```

##Controllers

Creating a BareBones controller is simple. To create a controller, create a file in the `controllers` directory. It is important that the name of this file be the same name as the controller class name it will contain. Class names are not case-sensitive, but filenames are.

BareBone's `easyRoute` method recognizes a default method on controllers. This method should be named `index` if you intend to use `easyRoute` and a default method. We'll talk a little more about that when we get to routing. For now, let's create a simple controller...
```php
<?php

  class BareBonesController extends BareBones\Controller
  {
    /* The "index()" method will be used by Easy Route. */
    index()
    {
      /* Method Stuff Here */
    }

    /* Non-default method. */
    method()
    {
      /* Method Stuff Here */
    }
  }
```
###Controller Methods

**NOTE:** All controller methods return `false` upon failure/error.

`getModel($modelName, $path="")`: The `getModel` method takes two arguments. The first (`$modelName`) is required and is the name of the model. Like controllers, class names are not case sensitive, but filenames are. When requiring a model with this method, simply use the model name, do not use the `.php` extension. The second argument (`$path`) is an optional variable. If you wish to require a model that is not contained directly in `assets/models` provide the directory path that the model file is contained in.

**NOTE:** `$path` has `/` characters trimmed.

Ex)
```php
<?php

  class BareBonesController extends BareBones\Controller
  {
    index()
    {
      /* Get a model from "assets/models" named "firstModel.php" */
      $firstModel = $this->getModel("firstModel");

      /* Get a model from "assets/models/subdir" named "secondModel.php" */
      $secondModel = $this->getModel("secondModel", "subdir");

      /* Get a model from "assets/models/subdir" named "thirdModel.php" */
      $thirdModel = $this->getModel("thirdModel", "/subdir/");
    }
  }
```

`renderView($viewPath, $viewData = ['staticPath'], $path)`: The `renderView` method also takes two arguments. The first (`$viewPath`) is the full view filename, including the extension. The second (`$viewData`) is an optional parameter. This array will pass variables to the Twig template engine as `$key=>$value pairs`. An indexed array will be treated as an associative array. (I.E. the variable name will be the numbered indes. 0, 1, 2, etc.) One `$viewData` index is reserved. `$viewData["staticPath"]` is always included. This variable will always be available to your views and is used to provide relative links to BareBone's `public` directory. The third argument (`$path`) is an optional variable. If you wish to render a view that is not contained directly in `assets/views` provide the directory path that the view file is contained in.

**NOTE:** $path has `/` characters trimmed.

[The Documentation for the Twig Template Engine can be Found Here](http://twig.sensiolabs.org/documentation)

Ex)
```php
<?php

  class BareBonesController extends BareBones\Controller
  {
    index()
    {
      /* Render a view in "assets/views" named "firstView.html" with no data. */
      $this->renderView("firstView.html");

      /* Render a view in "assets/views" named "secondView.html" with data. */
      $this->renderView("secondView.html", ["one" => "dataOne", "two" => "dataTwo"]);

      /* Render a view in "assets/views/subdir" named "thirdView.html" with no data. */
      $this->renderView("thirdView.html", [], "subdir");

      /* Render a view in "assets/views/subdir" named "fourthView.html" with data. */
     $this->renderView("fourthView.html", ["one" => "dataOne", "two" => "dataTwo"], "subdir");
    }
  }
```
##Models

BareBones models simply extend Eloquent and set Eloquent's timestamp requirements to `false`. This is a public property of the class called `timestamps`. Let's create a simple model...

```php
<?php

  class BareBonesModel extends BareBones\Model
  {
    method()
    {
      /* Method Stuff Here */
    }
  }
```

[The Documentation for Laravel's Eloquent can be Found Here](https://laravel.com/docs/4.2/eloquent)

##The App Class and Routing

Routing takes place in `public/index.php`. The default `index.php` looks like this...
```php
<?php

  require_once("../core/init.php");

  $app = new BareBones\App;
```

You may change the name of the insantiated `App` class without consequence. (Other than having to inject a different reference into route callbacks.)

###App Setters and Getters

`controllerExists($controllerName, $userPath = "")`: The `controllerExists` method checks if a controller file exists, and that file contains the controller class. The first argument (`$controllerName`) is the name of the controller. Do not include the `.php` extension. Remember, filenames are case-sensitive, but class names are not. The second argument (`$userPath`) is an optional argument for specifying a sub-directory within `assets/controllers` to look for the controller.

**NOTE:** The `$userPath` argument has the `/` characters trimmed.

Ex)
```php
<?php

  require_once("../core/init.php");

  $app = new BareBones\App;

  /* Look for a controller in "assets/controllers" contained in "firstController.php" */
  $app->controllerExists("firstController");

  /* Look for a controller in "assets/controllers/subdir" contained in "secondController.php" */
  $app->controllerExists("secondController", "subdir");

  /* Look for a controller in "assets/controllers/subdir" contained in "thirdController.php" */
  $app->controllerExists("thirdController", "/subdir/");
```
`checkDefaultMethod()`: The `checkDefaultMethod` method returns `true` if the current controller has an `index` method, and `false` if a controller is not set, or does not contain the `index` method.

Ex)
```php
<?php

  require_once("../core/init.php");

  $app = new BareBones\App;

  /* Check if the the current controller has an "index" method. */
  $hasDefaultMethodBoolean = $app->checkDefaultMethod();
```

`setController($controllerName, $userPath = "")`: The `setController` method sets the applications current controller. Typically, this should only be done once per route, but it is possible (if avoiding class-name conflicts) to execute this method more than once per route. The first argument (`$controllerName`) is the name of the controller. Do not include the `.php` extension. Remember, filenames are case-sensitive, but class names are not. The second argument (`$userPath`) is an optional argument for specifying a sub-directory within `assets/controllers` to look for the controller. This function returns `true` if the controller was succesfully set.

Ex)
```php
<?php
  require_once("../core/init.php");

  $app = new BareBones\App;

  /* Set $App's controller to and instance of `firstController` contained in "assets/controllers/firstController.php". */
  if ($app->setController("firstController"))
    echo "Controller set!";
  else
    echo "Error: Controller not set.";

  /* Set $App's controller to and instance of `secondController` contained in "assets/controllers/subdir/firstController.php". */
  if ($app->setController("secondController", "subdir"))
    echo "Controller set!";
  else
    echo "Error: Controller not set.";
```
###App Routing Methods

**NOTE:** All `$URI` arguments have the `/` characters trimmed.

`get(), post(), put(), delete(), all()`: All five of these basic routing functions differ only in the type of request they match. A route declared with the `get` method will only match GET requests, etc. the `all` method will match all request types. Each of these methods follow the same format...

`requestType($uri, $callback)`

The first argument (`$uri`) is a string that will match to the URI that comes **after the path to your project**. Let's say that your BareBones installation is at `http://yourDomain.com/api/`. A request for `http://yourDomain/api/one/two/three` will look for a route match of `one/two/three`. A request for `http://yourDomain/api/one/two/three/` will yield the same match. The second argument (`$callback`) is an anonymous callback function.

You may also match with wildcards in your route URI, which will be passed to your callback as arguments. A wildcard is defined in the `$uri` argument with `$`.

Ex) Assume BareBone's installation at `http://yourDomain.com/BareBones`
```php
<?php

  require_once("../core/init.php");

  $app = new BareBones\App;

  /* Create a route which matches a GET request to "http://yourDomain.com/BareBones/get" and echos some text. */
  $app->get("/get", function() {
    echo "Some Text in a Simple View";
  });
  /* Create a route which matches all request types to "http://yourDomain.com/BareBones/all/(ANYTHING)", sets $App's controller to "firstController" contained in "assets/controllers/firstController.php" and calls the "index" method on it where "(ANYTHING)" is passed as the "$callbackVariable" argument. */
  $app->all("/all/$", function($callbackVariable) use($app) {
    if ($app->setController("firstController"))
    {
      $app->controller->index($callbackVariable);
    }
  });
```

`route($requestTypes = [], $uri, $callback)`: The `route` method is used to match multiple, but not all request types to a route. The first argument (`$requestTpyes`) should be an array of request types to match. The possible array values are...

- GET
- POST
- PUT
- DELETE

The second argument (`$uri`) is a string that will match to the URI that comes after the path to your project. Let's say that your BareBones installation is at `http://yourDomain.com/api/`. A request for `http://yourDomain/api/one/two/three` will look for a route match of `one/two/three`. A request for `http://yourDomain/api/one/two/three/` will yield the same match. The third argument (`$callback`) is an anonymous callback function.

You may also match with wildcards in your route URI, which will be passed to your callback as arguments. A wildcard is defined in the `$uri` argument with `$`.

Ex)
```php
<?php

  require_once("../core/init.php");

  $app = new BareBones\App;

  /* Create a route which matches GET and POST requests to "http://yourDomain.com/BareBones/getPost" and echos some text. */
  $app->route(["GET", "POST"], "/getPost", function() {
    echo "Some Text in a Simple View";
  });
  /* Create a route which matches PUT and DELETE request types to "http://yourDomain.com/BareBones/putDelete/(ANYTHING)", sets $App's controller to "firstController" contained in "assets/controllers/firstController.php" and calls the "index" method on it where "(ANYTHING)" is passed as the "$callbackVariable" argument.*/
  $app->all("/putDelete/$", function($callbackVariable) use($app) {
    if ($app->setController("secondController"))
    {
      $app->controller->index($callbackVariable);
    }
  });
```
`easyRoute()`: The `easyRoute` method, if used, should be **called after all other route methods other than `notFound`**. This method checks if a route was found, and if a route was not found attempts to use a default behavior to find a controller and method to call. The request URI will be broken up into an array (delimited by `/`). The first value of the array will be the name of the controller `easyRoute` attempts to find. If there is no second array index, `easyRoute` will look for a default index method on the controller. If the second array index is defined, it will look for a method whose name is the second value in the array. If there are more than two array values, the remaining values will be passed to the method found as arguments.

Ex)
```php
<?php

  require_once("../core/init.php");

  $app = new BareBones\App;

  $app->easyRoute();
```

`notfound($callBack)` The `notFound` method is used to specify a callback if a route has not been found. **This method should only be used after ALL other routing methods have been called.** The `notFound` method takes one argument (`$callBack`) which is an anonymous callback function.

Ex)
```php
<?php

  require_once("../core/init.php");

  $app = new BareBones\App;

  /* If a route has not been found at the time of the "notFound" method call, set $App's controller to "notFoundController" contained in "assets/controllers/notFoundController.php" and execute the "index" method of the controller.*/
  $app->notFound(function() use($app) {
    if ($app->setController("notFoundController"))
    {
      $app->controller->index($callbackVariable);
    }
  });
```

#Support
Original Author: Allen Hundley for TagVar LLC

Email: Allen@TagVar.com

#License
License Owner: TagVar LLC

This project is licensed under the MIT license.
