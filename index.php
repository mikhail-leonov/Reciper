<?php
/**
 * Recipe - A recipe manager 
 * Proper API routing scheme should be like this:
 *
 * GET:/tags            - // Returns all tags
 * POST:/tags        	- // Create a new tag 
 * PUT:/tags 			- // Bulk update of tags
 * DELETE:/tags 		- // Delete all tags
 * GET:/tags/tag_id 	- // Return a specified tag
 * POST:/tags/tag_id 	- // Not allowed
 * PUT:/tags/tag_id 	- // Update a specified tag
 * DELETE:/tags/tag_id 	- // Delete a specified tag
 *
 * But Klein does not support parameters for PUT and DELETE parameters natively.
 * So we have to attach crutches to a bicycle for PUT and DELETE 
 * and implement these methods inside other class OR use hacks like runkit_method_add 
 * to attach these functions to object from outside using Dependency Injection
 *
 * @author      Mikhail Leonov <mikecommon@gmail.com>
 * @copyright   (c) Mikhail Leonov
 * @link        https://github.com/mikhail-leonov/recipe
 * @license     MIT
 */

/**
 *
 * Require class loaders
 *
 */
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/vendor/recipe/recipe/src/Recipe/bootstrap.php';

/**
 *
 * Name space usage section
 *
 */
use \Recipe\Util;
use \Recipe\Controllers\ImportController;
use \Recipe\Controllers\HomeController;
use \Recipe\Controllers\EntryController;
use \Recipe\Controllers\ApiController;
use \Klein\Klein;

/**
 *
 * Define section
 *
 */
define("CONFIG", __DIR__ . '/config.cfg' );
define("TEMP", __DIR__ . '/temp/' );
define("DEBUG", 1 );

/**
 * Routing section
 */
$router = new Klein();

/**
 * UI section routing
 */
$router->with('/ui', function () use ($router) {

	/**
	 * UI versions support
	 */
    $router->with('/v1', function () use ($router) {
	
        $controller = new EntryController();

        $router->respond('GET', '/entries', function ($request, $response, $service, $app, $router, $matched) use ($controller) {
            return $controller->GetSelectedEntries($request);
        });
        $router->respond('GET', '/entries/search', function ($request, $response, $service, $app, $router, $matched) use ($controller) {
            return $controller->GetFoundEntries($request);
        });
        $router->respond('GET', '/entries/all', function ($request, $response, $service, $app, $router, $matched) use ($controller) {
            return $controller->GetAllEntries($request);
        });
        $router->respond('GET', '/entries/[i:entry_id]', function ($request, $response, $service, $app, $router, $matched) use ($controller) {
            return $controller->ViewEntry($request);
        });
        $router->respond('GET', '/entries/[i:entry_id]/edit', function ($request, $response, $service, $app, $router, $matched) use ($controller) {
            return $controller->EditEntry($request);
        });
        $router->respond('GET', '/entries/[i:entry_id]/print', function ($request, $response, $service, $app, $router, $matched) use ($controller) {
            return $controller->PrintEntry($request);
        });
        $router->respond('GET', '/entries/new', function ($request, $response, $service, $app, $router, $matched) use ($controller) {
            return $controller->NewEntry($request);
        });
    });

});

/**
 * API section routing
 */
$router->with('/api', function () use ($router) {

	/**
	 * API versions support
	 */
    $router->with('/v1', function () use ($router) {

   	    $controller = new ApiController();
		
        // Returns a list of tags
        $router->respond('GET'   , '/tags'           , function ($request, $response, $service, $app, $router, $matched) use ($controller) {
			return '';
		});
        // Create a new tag 
        $router->respond('POST'  , '/tags'           , function ($request, $response, $service, $app, $router, $matched) use ($controller) {
			return '';
		});
        // Bulk update of tags
        $router->respond('PUT'   , '/tags'           , function ($request, $response, $service, $app, $router, $matched) use ($controller) {
			return '';
		});
        // Delete all tags
        $router->respond('DELETE', '/tags'           , function ($request, $response, $service, $app, $router, $matched) use ($controller) {
			return '';
		});
        // Return a specified tag
        $router->respond('GET'   , '/tags/[i:tag_id]', function ($request, $response, $service, $app, $router, $matched) use ($controller) {
			return '';
		});
        // Not allowed
        $router->respond('POST'  , '/tags/[i:tag_id]', function ($request, $response, $service, $app, $router, $matched) use ($controller) {
			return '';
		});
        // Update a specified tag
        $router->respond('PUT'   , '/tags/[i:tag_id]', function ($request, $response, $service, $app, $router, $matched) use ($controller) {
			return '';
		});
        // Delete a specified tag
        $router->respond('DELETE', '/tags/[i:tag_id]', function ($request, $response, $service, $app, $router, $matched) use ($controller) {
			return '';
		});
        // Select a tag 
        $router->respond('PUT'   , '/tag'            , function ($request, $response, $service, $app, $router, $matched) use ($controller) {
			return $controller->SelectTag($request);
		});
        // Unselect a tag 
        $router->respond('GET'   , '/tag'            , function ($request, $response, $service, $app, $router, $matched) use ($controller) {
			return $controller->UnselectTag($request);
		});

    });

});
  
/**
 * Default redirection to main page if nothing else matched 
 */
$router->respond(function ($request, $response, $service, $app, $router, $matched) {
     if (count($matched->all()) === 0) {
        $response->redirect("/ui/v1/entries");
        $response->send();
        $router::skipRemaining();
    }
});

/**
 * Dispatch routing settings
 */
$router->dispatch();
