<?php
    //methodes
    use Psr\Http\Message\ResponseInterface as Response; 
    use Psr\Http\Message\ServerRequestInterface as Request;
    use Slim\Factory\AppFactory;
    use ReallySimpleJWT\Token;

    /**
     * @OA\Post(
     *     path="/Authenticate",
     *     summary="Use authenticate",
     *     tags={"Authenticate"},
     *     requestBody=@OA\RequestBody(
     *         request="/Authenticate",
     *         required=true,
     *         description="The information are sendet to the server",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="username", type="string", example="morhaf"),
     *                 @OA\Property(property="password", type="string", example="J2o0u0d4")
     *             )
     *         )
     *     ),
     *     @OA\Response(response="200", description="Authenticate successful"),
     *     @OA\Response(response="401", description="Invalid credentials."))
     */
    $app->post("/Authenticate", function (Request $request, Response $response, $args) {
		global $api_username;
		global $api_password;

		$request_body_string = file_get_contents("php://input");

		$request_data = json_decode($request_body_string, true);

		$username = $request_data["username"];
		$password = $request_data["password"];

		    if ($username != $api_username || $password != $api_password) {
			    error("Invalid credentials.", 401);
			
		}

		    $token = Token::create($username, $password, time() + 1800, "localhost");

		    setcookie("token", $token);

		    error("Authenticate successful", 200);

		    return $response;

        });

        /**
         * @OA\Get(
         *  path="/Category/{category_id}",
         *  summary="Used to get the data from the table category",
         *  tags={"Category"},
         *  @OA\Parameter(
         *      name="category_id",
         *      in="path",
         *      required=true,
         *      description="It bring the Category",
         *      @OA\Schema(
         *          type="integer",
         *          example="1"
         *      )
         *  ),
         *  @OA\Response(response="200", description="OK"),
         *  @OA\Response(response="404", description="The ID was not found"))
         */
	$app->get("/Category/{category_id}", function (Request $request, Response $response, $args) {

        //connect to the authentication
        require "controller/require_authentication.php";

            $category_id = $args["category_id"];

            $category = get_category($category_id);

            return $response;

        });

        /**
         *@OA\Get(
         *path="/Categorys",
         *summary="Used to bring all categorys from table",
         *tags={"Category"},
         *@OA\Parameter(
         *name="Categorys",
         *in="path",
         *required=true,
         *description="It bring all data from table category",
         *@OA\Schema(
         *type="string",
         *example="Categorys" 
         *)
         *),
         *@OA\Response(response="200", description="OK"),
         *@OA\Response(response="500", description="Fetching the Category faild."))
         */
        $app->get("/Categorys", function (Request $request, Response $response, $args) {

            require "controller/require_authentication.php";

            $category = get_all_category();

            if (is_string($category)) {
                error($category, 500);
            }
            else {
                error($category, 500);
            }   

            return $response;

        });

        /**
         * @OA\Post(
         *     path="/Category",
         *     summary="Used to bring all data from Category_database",
         *     tags={"Category"},
         *     requestBody=@OA\RequestBody(
         *         request="/Category",
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
	$app->post("/Category", function (Request $request, Response $response, $args) {

        //connect to the authentication
        require "controller/require_authentication.php";

            $request_body_string = file_get_contents("php://input");

            $request_data = json_decode($request_body_string, true);

            $name = strip_tags(addslashes($request_data["name"]));
            $active = intval($request_data["active"]);

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

            if (create_new_category($active, $name) === true) {
                error("The Category was succsessfuly created.", 201);
            }
            else {
                error("saving the category failed.", 500);
            }
            
            return $response;
        });

    /**
     * @OA\Put(
     *     path="/Category/{category_id}",
     *     summary="(Used to update data in database)",
     *     tags={"Category"},
     *     @OA\Parameter(
     *         name="category_id",
     *         in="path",
     *         required=true,
     *         description="update the category",
     *         @OA\Schema(
     *             type="integer",
     *             example="1"
     *         )
     *     ),
     *     requestBody=@OA\RequestBody(
     *         request="/Category/{category_id}",
     *         required=true,
     *         description="Write the new Data to update it",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="name", type="string", example="Clothes"),
     *                 @OA\Property(property="active", type="integer", example="0")
     *             )
     *         )
     *     ),
     *     @OA\Response(response="200", description="The data were successfully updated"),
     *     @OA\Response(response="400", description="Please choose 1 or 0 for the (active) field."),
     *     @OA\Response(response="404", description="No Product found for the ID "),
     *     @OA\Response(response="500", description="Please enter less than 500 letters.")
     * )
     */
        $app->put("/Category/{category_id}", function (Request $request, Response $response, $args) {

            //connect to the authentication
            require "controller/require_authentication.php";

            $category_id = intval($args["category_id"]);

            $category = get_category($category_id);

            if (!$category) {
                error("No Product found for the ID " . $category_id . ".", 404);
            }

                $request_body_string = file_get_contents("php://input");

                $request_data = json_decode($request_body_string, true);

                if (isset($request_data["name"])) {
                    $name = strip_tags(addslashes($request_data["name"]));

                    if (strlen($name) > 500) {
                        error("Please enter less than 500 letters.", 500);
                    }

                    $category["name"] = $name;
                }

                if (isset($request_data["active"])) {
                    if (!is_numeric($request_data["active"])) {
                            error("Please choose 1 or 0 for the (active) field.", 400);
                    }

                    $active = intval($request_data["active"]);

                if ($active < 0 || $active > 1) {
                    error("The number must be between 0 and 1.", 400);
                }

                if (is_float($active)) {
                    error("The number must not have decimals.", 400);
                }

                $category["active"] = $active;
            }

            if (update_category($category_id, $category["name"], $category["active"])) {
                error("The data were successfully updated", 200);
            }

            else {
                error("saving the category data failed.", 500);
            }

            return $response;

        });

        /**
         * @OA\Delete(
         *     path="/Category{category_id}",
         *     summary="Used to delete the data",
         *     tags={"Category"},
         *     @OA\Parameter(
         *         name="category_id",
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
        $app->delete("/Category/{category_id}", function (Request $request, Response $response, $args) {
     
            //connect to the authentication
            require "controller/require_authentication.php";
    
            $category_id = intval($args["category_id"]);
    
            $result = delete_category($category_id);
    
            if (!$result) {
                error("No category found for this ID " . $category_id . ".", 404);
            }
    
         
            return $response;
    });

?>