<?php


enum RequestTypes: string
{

    case GET = 'GET';
    case POST = 'POST';
    case PUT = 'PUT';
    case DELETE = 'DELETE';



}
class ProductController
{

    private ProductGateway $productGatewaygateway; 


    private function getValidationErrors(array $data)
    {

        $errors = [];

        if (empty($data["name"]))
        {

            $errors[] = "Name is required!";

        }

        if (array_key_exists("size", $data))
        {

            if (filter_var($data["size"], FILTER_VALIDATE_INT) === false)
            {

                $errors[] = "Size must be an integer.";

            }

        return $errors;



    }}

    public function __construct(private ProductGateway $gateway)
    {
        $this->productGatewaygateway = $gateway;

    }

    private function convertRequest(?string $method): ?RequestTypes
    {

        $method = RequestTypes::tryFrom($method);

        if ($method)
            
        {

            return $method;


        }

        else
            
        {
            
            return null;


        }




    }

    public function processRequest(string $method, ?string $id, string $collectionSource, UserTypes $userType): void           // this checks to see whether the request requires a collection or resource
    {

        $confirmedMethod = $this->convertRequest($method);

        switch($confirmedMethod)
        {
        
        case RequestTypes::GET:
            echo "this is a GET method";
            break;

        case RequestTypes::POST:
            echo "this is a POST method";
            break;
        
        case RequestTypes::PUT:
            echo "this is a PUT method";
            break;

        case RequestTypes::DELETE:
            echo "this is a DELETE method";
            break;

        default:
            http_response_code(405);
            header("Allow: GET, POST, DELETE, PUT");
            exit;

    }

        
        if ($id)
        {

        $this->processResourceRequest($confirmedMethod, $id, $collectionSource, $userType);

        }

        else
        {

        $this->processCollectionRequest($confirmedMethod);

        }


    }


    private function processResourceRequest(RequestTypes $method, string $id, string $collectionSource, UserTypes $userType): void     // notice how no ? is required before id because at this point an ID is GUARANTEED
    
    {        

        $accountValidity = $this->gateway->verifyAccountExists($id);

        if (! $accountValidity)
        {

            http_response_code(404);
            echo "An account with ID" . $id . "does not exist.";
            exit;
        }

        echo json_encode($accountValidity);

        if ($collectionSource == "priv_products")
        {
            if ($userType === UserTypes::Admin)
            {
                $->processAdminRequest($method, $userType);   // priv function
            }
        }



    }

    




    private function processCollectionRequest(RequestTypes $method): void 

    {
        

    switch ($method)
    {

        case RequestTypes::GET:
            echo json_encode($this->gateway->getAll());



            break;

        case RequestTypes::POST:

            $data = (array) json_decode(file_get_contents("php://input"), true);
           
            var_dump(value: $data);

            $errors = $this->getValidationErrors($data);

            if ( ! empty($errors))
            {
                http_response_code(422);
                echo json_encode(["errors" => $errors]);
                break;
            }

            $id = (int) $this->gateway->create($data);

            if ($id == 0){

                http_response_code(404);
                echo json_encode(["message"=> "This table does not use an ID"]);
                exit;

            }

            http_response_code(201);

            echo json_encode(["message"=>"Here is the ID of your new entry.", "id"=>$id]);

            break;


        case RequestTypes::DELETE:

            $data = (array) json_decode(file_get_contents("php://input"), true);

            if (empty($data['id']))
            {
                http_response_code(403);
                echo json_encode(["message" => "The ID field MUST be entered."]);
                exit;
            }

            $id = (int) $data['id'];

            $response = $this->gateway->deleteItem($id);

            if ($response)
            {
                echo("User was successfully deleted from the database.");
            }
            else if (!$response)
            {
                echo("User doesn't exist.");
            }

            break;

        case RequestTypes::PUT:

            $data = (array) json_decode(file_get_contents("php://input"), true);

            if (empty($data['id']))
            {
                http_response_code(403);
                echo json_encode(["message" => "The ID field MUST be entered."]);
                exit;
            }

            $errors = $this->getValidationErrors($data);

            if ( ! empty($errors))
            {
                http_response_code(422);
                echo json_encode(["errors" => $errors]);
                break;
            }

            $updateResult = $this->gateway->updateItem($data);

            if ($updateResult)
            {
                echo "IT WORKED";
            }
            else if (! $updateResult)
            {
                echo "It did NOT work.";
            }

              


    }
    
    



    }



}
