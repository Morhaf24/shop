<?php
    //methodes
    use Psr\Http\Message\ResponseInterface as Response; 
    use Psr\Http\Message\ServerRequestInterface as Request;
    use Slim\Factory\AppFactory;
    use ReallySimpleJWT\Token;

    	$app->get("/Product/{product_id}", function (Request $request, Response $response, $args) {

            //connect to the authentication
            require "controller/require_authentication.php";
    
                $product_id = $args["product_id"];
    
                $product = get_product($product_id);
    
                return $response;
    
            });   

        $app->post("/Product", function (Request $request, Response $response, $args) {

            //connect to the authentication
            require "controller/require_authentication.php";

                $request_body_string = file_get_contents("php://input");
        
                $request_data = json_decode($request_body_string, true);
        
                $product_id = intval($request_data["product_id"]);
                $sku = strip_tags(addslashes($request_data["sku"]));
                $active = intval($request_data["actve"]);
                $category_id = intval($request_data["category_id"]);
                $name = strip_tags(addslashes($request_data["name"]));
                $image = strip_tags(addslashes($request_data["image"]));
                $discription = strip_tags(addslashes($request_data["discription"]));
                $price = intval($request_data["price"]);
                $stock = intval($request_data["stock"]);
        
        
                //The name can't be empty
                if (empty($name)) {
                    error("The field (Name) is empty!", 400);
                }
        
                //name-length limit
                if (strlen($name) > 500) {
                    error("Please enter less than 500 letters.", 400);
                }
        
                //The active have to be an integer
                if (!isset($request_data["active"]) || !is_numeric($request_data["active"])) {
                    error("Please choose 1 or 0 for the (active) field.", 400);
                }
        
                //The active number limit
                if ($active < 0 || $active > 1) {
                    error("Please choose 1 or 0 for the (active) field.", 400);
                }  
        
                if (create_new_Product($active, $name) === true) {
                    error("The Product was succsessfuly created.", 201);
                }
                else {
                    error("An error while saving the category.", 500);
                }
                    
                return $response;
            });

            $app->put("/Category/{category_id}", function (Request $request, Response $response, $args) {

                //connect to the authentication
                require "controller/require_authentication.php";
    
                $product_id = intval($args["product_id"]);
        
                $product = get_category($product_id);
    
                if (!$product) {
                    error("The ID " . $product_id . "has no product.", 404);
                }
        
                $request_body_string = file_get_contents("php://input");
        
                $request_data = json_decode($request_body_string, true);

                if (isset($request_data["sku"])) {
                    $sku = strip_tags(addslashes($request_data["sku"]));
        
                    if (strlen($sku) > 500) {
                        error("Please enter less than 500 letters.", 400);
                    }

                    $product["sku"] = $sku;
                    
        
                if (isset($request_data["name"])) {
                    $name = strip_tags(addslashes($request_data["name"]));
        
                    if (strlen($name) > 500) {
                        error("Please enter less than 500 letters.", 400);
                    }
        
                    $product["name"] = $name;
                        }
        
                if (isset($request_data["active"])) {
                    if (!is_numeric($request_data["active"])) {
                        error("Please provide an integer number for the \"age\" field.", 400);
                    }
        
                    $active = intval($request_data["active"]);

                if ($active < 0 || $active > 200) {
                    error("The age must be between 0 and 200 years.", 400);
                }
        
                if (is_float($active)) {
                    error("The age must not have decimals.", 400);
                }
        
                $product["active"] = $active;
                    }
        
                    if (update_product($product_id, $product["sku"], $product["active"], $product["category_id"], $product["name"], $product["image"], $product["description"], $product["price"] $product["stock"])) {
                        message("The product data were successfully updated", 200);
                    }
        
                    else {
                        error("An error occurred while saving the category data.", 500);
                    }
        
                    return $response;
        
                });
        
            
            $app->delete("/Product/{product_id}", function (Request $request, Response $response, $args) {
     
                //connect to the authentication
                require "controller/require_authentication.php";
        
                $product_id = intval($args["product_id"]);
        
                $result = delete_Product($product_id);
        
                if (!$result) {
                    error("No product found for the ID " . $product_id . ".", 404);
                }
        
             
                return $response;
        });