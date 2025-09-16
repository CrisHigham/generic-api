<?php
namespace GenericApi;

use Tina4;

/**
 * Route used to ensure that the generic api is up and running
 * @secure
 */
\Tina4\Get::add($_ENV["GENERIC_API_BASE_URL"] . "/ping", function(Tina4\Response $response){
    $version = [
        "name" => "generic-api",
        "version" => "0.0.4"
    ];
    return $response($version, HTTP_OK, APPLICATION_JSON);
});

// @todo look at naming

/**
 * Route to return a single record by id
 * @secure
 */
\Tina4\Get::add($_ENV["GENERIC_API_BASE_URL"] . "/{className}/{id}", function($className, $id, Tina4\Response $response, Tina4\Request $request){
    // Deal with any incoming - class names
    $className = RoutingHelper::pascalCase($className);
    // Check if the class name provided exists
    // @todo check if ORM or other class
    // @todo include methods
    if(class_exists($className)){
        // Get the incoming class
        $class = (new $className());

        // Load the class
        $result = $class->load("id = ?", [$id]);

        return $response($result, HTTP_OK);
    }
    // @todo uniform responses
    return $response("This request was not a valid request", HTTP_BAD_REQUEST);
});

/**
 * Route to return records of a class including query parameter filtering
 * @secure
 */
\Tina4\Get::add($_ENV["GENERIC_API_BASE_URL"] . "/{className}", function($className, Tina4\Response $response, Tina4\Request $request){
    // Deal with any incoming - class names
    $className = RoutingHelper::pascalCase($className);
    // Check if the class name provided exists
    if(class_exists($className)){
        // Get the incoming class
        $class = (new $className());

        // Set the limits
        $limit = 10;
        if(isset($request->params["limit"])){
            $limit = $request->params["limit"];
        }
        $offset = 0;
        if(isset($request->params["offset"])){
            $offset = $request->params["offset"];
        }

        $where = FilterHelper::buildFilterClause($class->fieldMapping, $request->params);

        // build return object
        $result = (new \stdClass());
        $buildData = $class->select("*", $limit, $offset);
        if(!empty($where["sql"]))
        {
            $buildData->where($where["sql"], $where["data"]);
        }
        $result->data = $buildData->asObject();
        $result->noOfRecords = count($result->data);
        $result->offset = (int) $offset;
        $result->limit = (int) $limit;

        return $response($result, HTTP_OK);
    }

    return $response("This request was not a valid request", HTTP_BAD_REQUEST);
});

/**
 * Route to create a record of a specific class
 * @secure
 */
\Tina4\Post::add($_ENV["GENERIC_API_BASE_URL"] . "/{className}", function($className, Tina4\Response $response, Tina4\Request $request){
    // Deal with any incoming - class names
    $className = RoutingHelper::pascalCase($className);
    // Check if the class name provided exists
    if(class_exists($className)){
        // Get the incoming class
        $class = (new $className($request->data));

        // Save the object based on the request
        // @todo probably need some validation here
        $result = $class->save()->asObject();

        return $response($result, HTTP_OK);
    }

    return $response("This request was not a valid request", HTTP_BAD_REQUEST);
});

/**
 * Route to update a single record of a class
 * @secure
 */
\Tina4\Patch::add($_ENV["GENERIC_API_BASE_URL"] . "/{className}/{id}", function($className, $id, Tina4\Response $response, Tina4\Request $request){
    // Deal with any incoming - class names
    $className = RoutingHelper::pascalCase($className);
    // new code
    $class = (new ClassHelper())->patchClass($className, $id, $request->data);
    // end new code
    // Check if the class name provided exists
    if($class)
    {
        $result = $class->save();
        return $response($result, HTTP_OK);
    }

    return $response("This request was not a valid request", HTTP_BAD_REQUEST);
});

/**
 * Route to delete a single record of a class
 * @secure
 */
\Tina4\Delete::add($_ENV["GENERIC_API_BASE_URL"] . "/{className}/{id}", function($className, $id, Tina4\Response $response, Tina4\Request $request){
    // Deal with any incoming - class names
    $className = RoutingHelper::pascalCase($className);
    // Check if the class name provided exists
    if(class_exists($className)){
        // Get the incoming class
        $class = (new $className());

        // Delete the object based on the request
        // @todo probably need some validation here
        $result = $class->delete("id = ?", [$id]);

        return $response($result, HTTP_OK);
    }

    return $response("This request was not a valid request", HTTP_BAD_REQUEST);
});

//@todo how do we do patch and delete
//@todo how do we deal with overwrites
//@todo how do we expose events that allow before and after changes.

//@todo simple cross table linking