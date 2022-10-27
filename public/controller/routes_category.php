<?php
    //methodes
    use Psr\Http\Message\ResponseInterface as Response; 
    use Psr\Http\Message\ServerRequestInterface as Request;
    use Slim\Factory\AppFactory;
    use ReallySimpleJWT\Token;

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

	$app->get("/Category/{category_id}", function (Request $request, Response $response, $args) {

        //connect to the authentication
        require "controller/require_authentication.php";

            $category_id = $args["category_id"];

            $category = get_category($category_id);

            return $response;

        });

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
                error("An error while saving the category.", 500);
            }
            
            return $response;
        });

        $app->put("/Category/{category_id}", function (Request $request, Response $response, $args) {

            //connect to the authentication
            require "controller/require_authentication.php";

            $category_id = intval($args["category_id"]);

            $category = get_category($category_id);

            if (!$category) {
                error("The ID " . $category_id . "has no category.", 404);
            }

                $request_body_string = file_get_contents("php://input");

                $request_data = json_decode($request_body_string, true);

                if (isset($request_data["name"])) {
                    $name = strip_tags(addslashes($request_data["name"]));

                    if (strlen($name) > 500) {
                        error("Please enter less than 500 letters.", 400);
                    }

                    $category["name"] = $name;
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

                $category["active"] = $active;
            }

            if (update_category($category_id, $category["name"], $category["active"])) {
                message("The Categorydata were successfully updated", 200);
            }

            else {
                error("An error occurred while saving the category data.", 500);
            }

            return $response;

        });

        $app->delete("/Category/{category_id}", function (Request $request, Response $response, $args) {
     
            //connect to the authentication
            require "controller/require_authentication.php";
    
            $category_id = intval($args["category_id"]);
    
            $result = delete_category($category_id);
    
            if (!$result) {
                error("No category found for the ID " . $category_id . ".", 404);
            }
    
         
            return $response;
    });

?>