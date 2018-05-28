<?php
/**
 * Recipe - A recipe manager 
 * Proper API routing scheme should be like this:
 *
 * GET:/tags            - // Returns all tags
 * POST:/tags        	- // Create a new tag 
 * PUT:/tags 		- // Bulk update of tags
 * DELETE:/tags 	- // Delete all tags
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
use \Recipe\Controllers\GroupApiController;
use \Recipe\Controllers\GroupUiController;
use \Recipe\Controllers\TagApiController;
use \Recipe\Controllers\TagUiController;
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
	
        $router->respond('GET', '/entries', function ($request, $response, $service, $app, $router, $matched) {
            $controller = new EntryController();
            return $controller->GetSelectedEntries($request);
        });
        $router->respond('GET', '/entries/search', function ($request, $response, $service, $app, $router, $matched) {
            $controller = new EntryController();
            return $controller->GetFoundEntries($request);
        });
        $router->respond('GET', '/entries/all', function ($request, $response, $service, $app, $router, $matched) {
            $controller = new EntryController();
            return $controller->GetAllEntries($request);
        });
        $router->respond('GET', '/entries/[i:entry_id]', function ($request, $response, $service, $app, $router, $matched) {
            $controller = new EntryController();
            return $controller->ViewEntry($request);
        });
        $router->respond('GET', '/entries/[i:entry_id]/edit', function ($request, $response, $service, $app, $router, $matched) {
            $controller = new EntryController();
            return $controller->EditEntry($request);
        });
        $router->respond('GET', '/entries/[i:entry_id]/print', function ($request, $response, $service, $app, $router, $matched) {
            $controller = new EntryController();
            return $controller->PrintEntry($request);
        });
        $router->respond('GET', '/entries/new', function ($request, $response, $service, $app, $router, $matched) {
            $controller = new EntryController();
            return $controller->NewEntry($request);
        });

        $router->respond('GET', '/groups', function ($request, $response, $service, $app, $router, $matched) {
            $controller = new GroupUiController();
            return $controller->getGroups($request);
        });

        $router->respond('GET', '/tags', function ($request, $response, $service, $app, $router, $matched) {
            $controller = new TagUiController();
            return $controller->getTags($request);
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

		
        // Returns a list of tags
        $router->respond('GET'   , '/tags'           , function ($request, $response, $service, $app, $router, $matched) {
            $controller = new TagApiController();
            return $controller->getTags($request);
        });
        // Create a new tag 
        $router->respond('POST'  , '/tags'           , function ($request, $response, $service, $app, $router, $matched) {
            $controller = new TagApiController();
            return $controller->postTags($request);
        });
        // Bulk update of tags
        $router->respond('PUT'   , '/tags'           , function ($request, $response, $service, $app, $router, $matched) {
            $controller = new TagApiController();
            return $controller->putTags($request);
        });
        // Delete all tags
        $router->respond('DELETE', '/tags'           , function ($request, $response, $service, $app, $router, $matched) {
            $controller = new TagApiController();
            return $controller->deleteTags($request);
        });
        // Return a specified tag
        $router->respond('GET'   , '/tags/[i:tag_id]', function ($request, $response, $service, $app, $router, $matched) {
            $controller = new TagApiController();
            return $controller->getTag($request);
        });
        // Not allowed
        $router->respond('POST'  , '/tags/[i:tag_id]', function ($request, $response, $service, $app, $router, $matched) {
            $controller = new TagApiController();
            return $controller->postTag($request);
        });
        // Update a specified tag
        $router->respond('PUT'   , '/tags/[i:tag_id]', function ($request, $response, $service, $app, $router, $matched) {
            $controller = new TagApiController();
            return $controller->putTag($request);
        });
        // Delete a specified tag
        $router->respond('DELETE', '/tags/[i:tag_id]', function ($request, $response, $service, $app, $router, $matched) {
            $controller = new TagApiController();
            return $controller->deleteTag($request);
        });


        // Select a tag 
        $router->respond('PUT'   , '/tag'            , function ($request, $response, $service, $app, $router, $matched) {
            $controller = new ApiController();
            return $controller->SelectTag($request);
        });
        // Unselect a tag 
        $router->respond('GET'   , '/tag'            , function ($request, $response, $service, $app, $router, $matched) {
            $controller = new ApiController();
            return $controller->UnselectTag($request);
        });

		
        // Returns a list of Groups
        $router->respond('GET'   , '/groups'             , function ($request, $response, $service, $app, $router, $matched) {
            $controller = new GroupApiController();
            return $controller->getGroups($request);
        });
        // Create a new tag 
        $router->respond('POST'  , '/groups'             , function ($request, $response, $service, $app, $router, $matched) {
            $controller = new GroupApiController();
            return $controller->postGroups($request);
        });
        // Bulk update of tags
        $router->respond('PUT'   , '/groups'             , function ($request, $response, $service, $app, $router, $matched) {
            $controller = new GroupApiController();
            return $controller->putGroups($request);
        });
        // Delete all tags
        $router->respond('DELETE', '/groups'             , function ($request, $response, $service, $app, $router, $matched) {
            $controller = new GroupApiController();
            return $controller->deleteGroups($request);
        });
        // Return a specified tag
        $router->respond('GET'   , '/groups/[i:group_id]', function ($request, $response, $service, $app, $router, $matched) {
            $controller = new GroupApiController();
            return $controller->getGroup($request);
        });
        // Not allowed
        $router->respond('POST'  , '/groups/[i:group_id]', function ($request, $response, $service, $app, $router, $matched) {
            $controller = new GroupApiController();
            return $controller->postGroup($request);
        });
        // Update a specified tag
        $router->respond('PUT'   , '/groups/[i:group_id]', function ($request, $response, $service, $app, $router, $matched) {
            $controller = new GroupApiController();
            return $controller->putGroup($request);
        });
        // Delete a specified tag
        $router->respond('DELETE', '/groups/[i:group_id]', function ($request, $response, $service, $app, $router, $matched) {
            $controller = new GroupApiController();
            return $controller->deleteGroup($request);
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
