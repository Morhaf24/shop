<?php
	//Content-Type
    header("Content-Type: application/json");

	use Psr\Http\Message\ResponseInterface as Response; 
    use Psr\Http\Message\ServerRequestInterface as Request;
    use Slim\Factory\AppFactory;
    use ReallySimpleJWT\Token;

    require "../vendor/autoload.php";
    require "model/registration_category.php";
	require "model/registration_product.php";
    require "config/config.php";

    $app = AppFactory::create();

    /**
         * @OA\Info(title="Shop", version="0.1")
     */
	require "controller/routes_category.php";
	require "controller/routes_product.php";
    
	$app->run();
	
    ?>