<?php
	//Content-Type
    header("Content-Type: application/json");

	//methodes
    use Psr\Http\Message\ResponseInterface as Response; 
    use Psr\Http\Message\ServerRequestInterface as Request;
    use Slim\Factory\AppFactory;
    use ReallySimpleJWT\Token;

    require __DIR__ . "/../vendor/autoload.php";
    //require "model/registration.php";
    require "config/config.php";
	//require "model/database.php";

    $app = AppFactory::create();

    $app->post("/Authenticate", function (Request $request, Response $response, $args) {
		global $api_username;
		global $api_password;

		$request_body_string = file_get_contents("php://input");

		$request_data = json_decode($request_body_string, true);

		$username = $request_data["username"];
		$password = $request_data["password"];

		if ($username != $api_username || $password != $api_password) {
			$error = array("message" => "Invalid credentials.");
			echo json_encode($error);

			http_response_code(401);
			die();
		}

		$token = Token::create($username, $password, time() + 3600, "localhost");

		setcookie("token", $token);

		echo "true";

		return $response;

    });

	$app->get("/Category/{category_id}", function (Request $request, Response $response, $args) {

        //connect to the authentication
        require "controller/require_authentication.php";

        $category_id = $args["category_id"];

        $category = get_category($category_id);

        if ($category) {
            echo json_encode($category);
        }

        else if (is_string($category)) {
            error($category, 500);
        }

        else {
            error("The ID "  . $category_id . " was not found.", 404);
        }
        return $response;

    });

    $app->run();
    ?>