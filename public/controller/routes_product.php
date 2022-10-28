<?php
    //methodes
    use Psr\Http\Message\ResponseInterface as Response; 
    use Psr\Http\Message\ServerRequestInterface as Request;
    use Slim\Factory\AppFactory;
    use ReallySimpleJWT\Token;

    /**
     * @OA\Get(
     *  path="/Product/{product_id}",
     *  summary="Used to bring all products from table",
     *  tags={"Product"},
     *  @OA\Parameter(
     *      name="product_id",
     *      in="path",
     *      required=true,
     *      description="It bring all data from table product",
     *      @OA\Schema(
     *          type="integer",
     *          example="1"
     *      )
     *  ),
     *  @OA\Response(response="200", description="OK"),
     *  @OA\Response(response="404", description="The ID was not found"))
     */
    	$app->get("/Product/{product_id}", function (Request $request, Response $response, $args) {

            //connect to the authentication
            require "controller/require_authentication.php";
    
                $product_id = $args["product_id"];
    
                $product = get_product($product_id);
    
                return $response;
    
            });   

    /**
     * @OA\Get(
     *  path="/Products",
     *  summary="Used to bring all products from table",
     *  tags={"Product"},
     *  @OA\Parameter(
     *      name="Products",
     *      in="path",
     *      required=true,
     *      description="It bring all data from table product",
     *      @OA\Schema(
     *          type="string",
     *          example="Products"
     *      )
     *  ),
     *  @OA\Response(response="200", description="OK"),
     *  @OA\Response(response="500", description="Fetching the Category faild."))
     */
    $app->get("/Products", function (Request $request, Response $response, $args) {

        require "controller/require_authentication.php";

        $product = get_all_product();

        if (is_string($product)) {
            error($product, 500);

        }

        else {
            error($product, 500);

        }

 return $response;

    });

    /**
     * @OA\Post(
     *     path="/Product",
     *     summary="Used to bring all data from product_database",
     *     tags={"Product"},
     *     requestBody=@OA\RequestBody(
     *         request="/Product",
     *         required=true,
     *         description="The data are sendet to the server",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="name", type="string", example="Watch"),
     *                 @OA\Property(property="active", type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(response="201", description="The Category was successfuly created."),
     *     @OA\Response(response="400", description="Please enter less than 500 letters."),
     *     @OA\Response(response="500", description="saving the category failed."))
     */
        $app->post("/Product", function (Request $request, Response $response, $args) {

            //connect to the authentication
            require "controller/require_authentication.php";

                $request_body_string = file_get_contents("php://input");
        
                $request_data = json_decode($request_body_string, true);
        
                $sku = strip_tags(addslashes($request_data["sku"]));
                $active = intval($request_data["actve"]);
                $name = strip_tags(addslashes($request_data["name"]));
                $image = strip_tags(addslashes($request_data["image"]));
                $discription = strip_tags(addslashes($request_data["discription"]));
                $price = intval($request_data["price"]);
                $stock = intval($request_data["stock"]);

        if (empty($sku)) {
			error("The (sku) field is empty.", 400);
		}
		if (strlen($sku) > 100) {
			error("TPlease enter less than 100 letters.", 400);
		}
		//The name can not be empty
		if (empty($name)) {
			error("The (name) field must not be empty.", 400);
		}
		//Limit the length of the name.
		if (strlen($name) > 500) {
			error("Please enter between 0 and 500 letters.", 400);
		}
        if (empty($image)) {
			error("The (image) field is empty.", 400);
		}
		if (strlen($image) > 1000) {
			error("Please enter less than 500 letters.", 400);
		}
        if (empty($description)) {
			error("The (description) field is empty.", 400);
		}
		//The active have to be an integer
		if (!isset($request_data["active"]) || !is_numeric($request_data["active"])) {
			error("Please choose 1 or 0 for the (active) field.", 400);
		}
        if (!isset($request_data["price"]) || !is_numeric($request_data["price"])) {
			error("Please choose 1 or 0 for the (price) field.", 400);
		}
        if (!isset($request_data["stock"]) || !is_numeric($request_data["stock"])) {
			error("Please choose 1 or 0 for the (active) field.", 400);
		}
		//Limit the active nummber
		if ($active < 0 || $active > 1) {
			error("The number must be between 0 and 1.", 400);
		}
        if ($price < 0 || $price > 65) {
			error("The number must be between 0 and 65.", 400);
		}
        if ($stock < 0 || $stock > 11) {
			error("The number must be between 0 and 11.", 400);
		}
		//checking if allthing was good
		if (create_new_product($sku, $active, $name, $image, $description, $price, $stock) === true) {
			error("The product was successfuly created.", 201);
		}
		//an server error
		else {
			error("An error while saving the product.", 500);
		}
		return $response;		
	});

    /**
     * @OA\Put(
     *     path="/Product/{product_id}",
     *     summary="(Used to update data in database)",
     *     tags={"Product"},
     *     @OA\Parameter(
     *         name="product_id",
     *         in="path",
     *         required=true,
     *         description="update the product",
     *         @OA\Schema(
     *             type="integer",
     *             example="1"
     *         )
     *     ),
     *     requestBody=@OA\RequestBody(
     *         request="/Product/{product_id}",
     *         required=true,
     *         description="Write the new Data to update it",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="name", type="string", example="Car"),
     *                 @OA\Property(property="price", type="integer", example="50")
     *             )
     *         )
     *     ),
     *     @OA\Response(response="200", description="he data were successfully updated"),
     *     @OA\Response(response="400", description="Please choose 1 or 0 for the (active) field."),
     *     @OA\Response(response="404", description="No Product found for the ID "),
     *     @OA\Response(response="500", description="Please enter less than 500 letters.")
     * )
     */
            $app->put("/Product/{product_id}", function (Request $request, Response $response, $args) {

                require "controller/require_authentication.php";
                    
                $product_id = intval($args["product_id"]);
                    
                $product = get_product($product_id);
                    
                if (!$product) {
                error("No Product found for the ID " . $product_id . ".", 404);
                    }
                    
                    $request_body_string = file_get_contents("php://input");
                    
                    $request_data = json_decode($request_body_string, true);
            
                    if (isset($request_data["sku"])) {
                        $sku = strip_tags(addslashes($request_data["sku"]));
                    
                        if (strlen($sku) > 100) {
                            error("Please enter less than 100 letters.", 400);
                        }
                    
                        $product["sku"] = $sku;
                    }
            
                    if (isset($request_data["name"])) {
                        $name = strip_tags(addslashes($request_data["name"]));
                    
                        if (strlen($name) > 500) {
                            error("The name is too long. Please enter less than 500 letters.", 400);
                        }
                    
                        $product["name"] = $name;
                    }
            
                    if (isset($request_data["image"])) {
                        $image = strip_tags(addslashes($request_data["image"]));
                    
                        if (strlen($image) > 1000) {
                            error("Please enter less than 1000 letters.", 400);
                        }
                    
                        $product["image"] = $image;
                    }
            
                    if (isset($request_data["description"])) {
                        $description = strip_tags(addslashes($request_data["description"]));
                    
                        if (strlen($description) > 1000) {
                            error("Please enter less than 1000 letters.", 400);
                        }
                    
                        $product["description"] = $description;
                    }
            
                    if (isset($request_data["active"])) {
                        if (!is_numeric($request_data["active"])) {
                            error("Please choose 1 or 0 for the (active) field.", 400);
                        }
                    
                        $active = intval($request_data["active"]);
                    
                        if ($active < 0 || $active > 1) {
                            error("The number must be between 0 and 1 years.", 400);
                        }
                    
                        $product["active"] = $active;
                    }
            
                    if (isset($request_data["category_id"])) {
                        if (!is_numeric($request_data["category_id"])) {
                            error("Please choose 1 or 0 for the (category_id) field.", 400);
                        }
                    
                        $category_id = intval($request_data["category_id"]);
                    
                        $product["category_id"] = $category_id;
                    }
            
                    if (isset($request_data["price"])) {
                        if (!is_numeric($request_data["price"])) {
                            error("You must indicate the price.", 400);
                        }
                    
                        $price = intval($request_data["price"]);
                    
                    
                        $product["price"] = $price;
                    }
            
                    if (isset($request_data["stock"])) {
                        if (!is_numeric($request_data["stock"])) {
                            error("You must indicate the stock..", 400);
                        }
                    
                        $stock = intval($request_data["stock"]);
                    
                        $product["stock"] = $stock;
                    }
                    
                    if (update_product($product_id, $product["name"], $product["active"], $product["sku"], $product["category_id"], $product["image"], $product["description"], $product["price"], $product["stock"])) {
                        error("The product data were successfully updated", 200);
                    }
                    else {
                        error("saving the category data failed.", 500);
                    }
                    
                    return $response;
                });

    /**
     * @OA\Delete(
     *     path="/Product{product_id}",
     *     summary="Used to delete the data",
     *     tags={"Product"},
     *     @OA\Parameter(
     *         name="product_id",
     *         in="path",
     *         required=true,
     *         description="Delete the category",
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\Response(response="200", description="The data was deleted."),
     *     @OA\Response(response="404", description="No category found for this ID"))
     */
            $app->delete("/Product/{product_id}", function (Request $request, Response $response, $args) {
     
                //connect to the authentication
                require "controller/require_authentication.php";
        
                $product_id = intval($args["product_id"]);
        
                $result = delete_Product($product_id);
        
                if (!$result) {
                    error("No product found for ID " . $product_id . ".", 404);
                }
        
             
                return $response;
        });
?>