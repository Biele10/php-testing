<?php

	declare(strict_types=1);

	spl_autoload_register(function ($class){

		require __DIR__ . "/src/$class.php";


	});

	set_error_handler("ErrorHandler::handleError");

	set_exception_handler("ErrorHandler::handleException");

	header("Content-type: application/json; charset=UTF-8");

	$parts = explode("/", $_SERVER["REQUEST_URI"]);

	$request_type = $_SERVER["REQUEST_METHOD"];

	if ( $parts[1] != "products" )
	{

		http_response_code(404);
		exit;


	}
	
	
	$id = $parts[2] ?? null;

	$database = new Database("localhost", "product_db", "root", "");

	$gateway = new ProductGateway($database);

	$headerInfo = getallheaders();	// returns everything in the https header and stores it as an associative array

	if (!isset($headerInfo['Authorization']))
	{
		echo("No authorization token was sent out.");
	}

	else
	{
		$authHeader = (string) $headerInfo['Authorization'];		// therefore you can access the Authorization
	
		$accessController = new AccessController($authHeader, $gateway);	// defines the type of user

		$userType = $accessController->getUserType();

		if (! $userType)
		{
			http_response_code(404);
			echo("Invalid token.");
			exit;

		}
		else if ($userType == UserTypes::Admin)
		{
			echo("this person is an admin");
		}
		else if ($userType == UserTypes::Customer)
		{
			echo("\nthis person is a Customer");
		}
		else
		{
			echo("something is not working");
		}


	}

	$collectionSource = $parts[1];

	$controller = new ProductController($gateway);

	$controller->processRequest($_SERVER["REQUEST_METHOD"], $id, $collectionSource);

	// $controller now represents an object meaning it can have methods called from it, so you pass in the server request method as well as the id




