# LightRail

a lightweight web application framework for PHP

#### Sections

- [What is LightRail and why use it?](#what-is-lightrail-and-why-use-it)
- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Model View Controller (MVC)](#model-view-controller-mvc)
- [Getting application URLs to work](#getting-application-urls-to-work)
- [Custom URL routing](#custom-url-routing)
- [Extending LightRail](#extending-lightrail)

## What is LightRail and why use it?

LightRail is an extremely **light** web application framework for PHP which layers data, presentation and logic using the model/view/controller pattern (MVC) popularized by Ruby on **Rails**.

It was influenced by a bare-bones framework design by Chris Panique.

### Why use LightRail?

LightRail is:

- blazing fast
- simple
- flexible
- open source
- environment-friendly ;-)

There are many frameworks for PHP which employ MVC or a similar pattern, and they all have a much larger featureset than LightRail. It may very well be that you are better served by using one of them. Shop and see. But unfortunately, when frameworks are built to be comprehensive they also turn into a 10 pound Swiss army knife - feature-rich but cumbersome.

LightRail takes the opposite approach. It just handles incoming requests and organizes your application codebase. Then it lets PHP, which is itself a web application language, handle the rest. This is what makes it so light.

#### Fast

Accordingly, you will notice that LightRail is significantly faster than other PHP frameworks. Which is nice, because good performance translates into being able to handle more site traffic volume.

#### Simple

Another benefit of using a minimal framework is that there is minimal learning time needed before you can start working with it. PHP is designed to handle most web programming tasks on its own, and good third-party libraries already exist for common needs. Why re-invent the wheel?

When you examine some of the more expansive frameworks you can actually find methods in classes with multiple levels of inheritance, whose only job is to call a pre-existing PHP function! This might impress dogmatic OOP purists but it certainly doesn't impress your server. And when your server slows down, it doesn't impress your users.

#### Flexible

LightRail does not presume that you lack ideas of your own, nor does it force you down a rigid coding path. It is ammenable to your own coding decisions.

#### Open Source

LightRail is open source. You can easily modify it to suit your own needs and there is no malware hiding in it.

#### Environment-Friendly

This point was mostly tongue in cheek, but not entirely. Hardware consumes power and creates disposal issues at the end of its life cycle. By getting more out of your hardware you reduce carbon emissions, toxic waste and operating costs.

## Features

LightRail's features include:

- Standardized application code organization
- Meaningful URLs (a.k.a. "pretty urls")
- Customizable URL routing

#### Requirements

PHP version 5 or higher

While the documentation is Apache-centred, LightRail will work in other PHP environments.

## Installation

You can download or clone the LightRail project from [GitHub](https://github.com/trentreimer/LightRail) and place the contents into the server directory you wish to use.

Let's take a look at the contents.

#### The site router: 'index.php'

The file `index.php` is the site router. It handles all incoming requests.

#### The directories

The included directories are:

- `application/`
- `public/`

#### The application directory

`application/` contains the files for the application: the models, views, controllers, etc. as well as the LightRail class definition itself. ( `LightRail.php` ) The `application/` directory should not be viewable from the web since none of the files within it are meant to handle direct requests.

> The included `.htaccess` file will instruct most Apache servers not to serve content from the `application/` directory. But it is wise to test for access denial by trying to browse to it. If your server does not support .htaccess configuration you will need to secure the directory using the correct procedure for your server.

> Another option, perhaps preferrable, is to place the `application/` directory in a location outside the server's web viewable directory tree and edit the router file (`index.php`) to require `application/LightRail.php` from its new location.

The principle subdirectories of the application directory are:

- `controllers/`
- `models/`
- `views/`

#### The public directory

`public/` is used to contain static files which can be served directly, e.g. things like images, CSS stylesheets and JavaScript libraries.

## Configuration

Configuration directives for your web application can be set in `application/app-config.php` . Feel free to add any configuration options you like.

If you use the built in database handler you will need to put your database connection details in this file as `$conf['pdo_args']` . You will find database configuration instructions in the file.

## Model View Controller (MVC)

LightRail employs a model, view, controller pattern (MVC) similar to Ruby on Rails and many other frameworks. MVC is a form of layered or tiered application development. By separating development into layers, applications become more manageable, especially as they expand over time.

#### Model

The model layer handles object relational mapping (ORM). ORM converts database records to programming objects and vice-versa. If you don't need to interact with a database you don't need this feature.

#### View

The view is the presentation/interface layer, usually HTML, which is output to the user.

#### Controller

The controller is the principal logic layer which ties everything together.

As with most things in life, ASCII art will facilitate our understanding:

```
 +-------------+
 |    View     |
 +-------------+
        |
 +-------------+
 | Controller  |
 +-------------+
        |
 +-------------+
 |    Model    |
 +-------------+
        |
    (Database)             <- now it all makes sense!
```

### How MVC Works in LightRail

When a URL is passed to LightRail it is processed as an application request. i.e. A URL can request a specific **action** from your application. That action is processed in the **controller**.

In your code, the controller is a **class** and the action is a **method** of that class.

Controllers are placed in the `application/controllers/` directory.

```
<?php // file: application/controllers/FriendController.php

class FriendController extends Controller
{
    public function indexAction()
    {
        // Here we process any logic for FriendController->indexAction()
    }
}
```

> Notice that class definition file names match the case of the class name.

Maybe you need to grab some data for `FriendController->indexAction()`


```
<?php // file: application/controllers/FriendController.php

class FriendController extends Controller
{
    public function indexAction()
    {
        // Let's get a list of our favourite people.
        $fr = new FriendRecord(); // This is a model class
        $favouritePeople = $fr->getFavourites();
    }
}
```

You will notice that our controller action has called upon a **model** object, `FriendRecord`, to get some data. Let's define it.

```
<?php // file: application/models/FriendRecord.php

class FriendRecord extends Record
{
    public function getFavourites()
    {
        $sql = 'SELECT first_name, last_name FROM friends'
             . ' WHERE status = ' . $this->db()->quote('cool')
             . ' ORDER BY physical_appearance';

        // $this->db() returns a PDO instance

        $sth = $this->db()->prepare($sql);
        $sth->execute();

        return $sth->fetchAll();
    }
}
```

Often in the case of a straightforward database SELECT like this you could just query it directly in the controller and reserve model classes for more repetitive or special tasks such as validating data before inserting it.

Then again, we've always said our friends are in a class of their own.

OK, we have procured an important list using a controller and model. Time for a **view** to display it.

```
<!-- // file: application/views/friend/index.php -->

<h1>My Favourite People</h1>
<ol>
    <?php foreach ($favouritePeople as $person): ?>

    <li><?php echo htmlentities("{$person->first_name} {$person->last_name}") ?></li>

    <?php endforeach; ?>

</ol>
```

Views are named after the controller and action method. A subdirectory named after the controller is placed in the `application/views/` directory and a file named after the action is placed in that controller subdirectory.

So here we execute `FriendController->indexAction()` with its view file being `application/views/friend/index.php`

> Note: In the case of multiple word controller or action names, views use hyphens instead of camelCasing. e.g. `BestFriendController->getMyFavouritesAction()` would have the view `application/views/best-friend/get-my-favourites.php`

If this were a complete HTML document we could load it using an include statement in the controller.

```
<?php // file: application/controllers/FriendController.php

class FriendController extends Controller
{
    public function indexAction()
    {
        $fr = new FriendRecord();
        $favouritePeople = $fr->getFavourites();

        // Let's display the view.
        include $this->view();
    }
}
```

But of course this view file is not a complete HTML document. It is only a snippet. That is where layout templates come in.

Often you will have any number of views which share the same general document layout structure. Instead of building a separate and complete HTML document for each action's view, you can build a layout template which is able to include the view file for the action method.


```
<!-- // file: application/views/_layouts/main.php -->

<!doctype html>
<html>
    <head>
        <title>My Dynamic Site</title>
    </head>
    <body>
        <header>
            <img src="public/images/banner.jpg" alt="My Dynamic Site">
            <nav>
                <a href="/friend/index">my favourite people</a>
                <a href="/friend/recommend">recommend a new friend</a>
            </nav>
        </header>

        <?php
            // Include the view content here.
            @include $this->view();
        ?>

        <footer>
            &copy; 1954 My Dynamic Site
        </footer>
    </body>
</html>
```

Now we update the controller to use the view's HTML snippet within the default layout document:

```
<?php // file: application/controllers/FriendController.php

class FriendController
{
    public function indexAction()
    {
        $fr = new FriendRecord();
        $favouritePeople = $fr->getFavourites();

        // Include the layout, which in turn includes the view.
        include $this->layout();
    }
}
```

This format is extremely efficient and automatically gives the layout and view access to the variables in the action method.

It also gives the view access to the properties and methods of the controller, which will drive some OOP purists over the edge.

However, if such things cause one to lose sleep, it is not difficult to extend such a simple framework to render the view within its own scope and require variables to be passed to the view explicitly.

LightRail lets you flesh out the details the way **you** like, with just as much code as you need, and no more.

## Getting application URLs to work

### The router file: `index.php`

In LightRail all requests are handled by the file `index.php` in the top level directory. All application URLs need to get routed to that file.

It is important that your server treats `index.php` as the directory index. Most PHP enabled servers already do that but if yours does not you will need to set it. For Apache this can often be done with an `.htaccess` directive.

```
# file: .htaccess
DirectoryIndex index.php
```

### URL segments

When parsing the incoming URL for arguments, instead of relying entirely on query strings LightRail uses URL segments separated by '/' slashes to create more readable URLs.

so instead of this:

`https://example.com/index.php?controller=Catalog&action=viewDetails&item=shark-laser`

you have this:

`https://example.com/catalog/view-details/shark-laser`

In this example we have three segments: `catalog/view-details/shark-laser`

In LightRail application URLs:

1. The first segment is used to denote which **controller** class to load.
2. The second segment defines which **action** method of that controller to call.
3. The third and following segments are entirely optional, but when present they are passed as **arguments** to the action method.

Consider the following URL:

`https://example.com/catalog/view-details/shark-laser`

The first URL segment `'catalog'` calls for the `CatalogController` class and the second segment `'view-details'` calls for the `viewDetailsAction()` method.

The third segment `'shark-laser'` is passed as an argument to the `viewDetailsAction()` method.

```
<?php // file: application/controllers/CatalogController.php

class CatalogController extends Controller
{
    public function viewDetailsAction($item = null)
    {
        // $item corresponds to the third URL segment
        if ($item == 'shark-laser') {
            // This part is up to you
        }
    }
}
```

You may also have noticed that LightRail will convert hyphens ('-') to camel case for the controller and action segments, but not to any arguments supplied to the action method.

So the application URL

`catalog/view-details/shark-laser`

calls

`CatalogController->viewDetailsAction('shark-laser')`

### Directing requests to the router file

There are two ways application URL segments can be directed to the router file:

1. URL rewriting
2. Standard query string

#### 1. Using URL rewriting

If your server supports URL rewriting it will enable you to direct requests to the router file without having to name it in the URL.

For example, the following can call `index.php` without naming it in the URL:

`https://example.com/controller/action/arg`

This enables short, neat URLs.

If you have an Apache server with mod_rewrite and .htaccess configuration, the included `.htaccess` file will automatically direct application requests to `index.php` as a query string value named `--request`

```
# file: .htaccess

RewriteEngine on
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^(.*)$ index.php?--request=$1 [QSA,L]
```

The router file can then read that application request as a `$_GET` value.

```
<?php // file: index.php

$request = @$_GET['--request'];
```

#### 2. Using a standard query string

Any PHP enabled server will support arguments via query string. For example:

`https://example.com/index.php?--request=controller/action/arg`

> '--request' is an arbitrary query string key, chosen here because it is highly unlikely you would ever need to use it for anything else. But you can easily configure `.htaccess` and `index.php` to use a different key.

### Passing the application URL segments to LightRail

The router file needs to pass the application URL segments to `LightRail` when serving the wep application request. This is done with the static method `LightRail::serve()`

The application URL segments are supplied as a single argument to the `serve()` method.

The following works with either URL rewriting or a direct query string:

```
<?php // file: index.php

// Always set the default controller/action request for the site
if (empty($_GET['--request'])) {
    $request = 'site/home';
} else {
    $request = $_GET['--request'];
}

require 'application/LightRail.php';
LightRail::serve($request);
```

## Custom URL routing

If you wish to use application URLs which differ from the `controller/action/arg` pattern, you can add some logic to convert actual requests to the `controller/action/arg` format.

The most direct way to do that is to add rules to the router file.

```
<?php // file: index.php

// Always set the default controller/action request for the site
if (empty($_GET['--request'])) {
    $request = 'site/home';
} else {
    $request = $_GET['--request'];
}

// Custom routing rules
if ($request = 'shark-laser') {

    // Call CatalogController->viewDetailsAction('shark-laser');
    $request = 'catalog/view-details/shark-laser'; // That was easy

} elseif (preg_match('/^diary\/[0-9]{4}-[0-9]{2}-[0-9]{2}/', $request)) {

    // This is a request to view your personal diary entry by ISO date.
    // Call DiaryController->shareInsideVoiceAction($date);
    $request = str_replace('diary/', 'diary/share-inside-voice/', $request, 1);

}
```

## Extending LightRail

`LightRail` and its associated classes are not difficult to edit or extend for your own purposes. You are encouraged to do so.


@2019 Trent Reimer
