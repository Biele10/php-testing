<?php

enum UserTypes
    {
        case Customer;

        case Admin;

    }

class AccessController
{

    private ?UserTypes $userType;

    public function __construct(string $bearer, ProductGateway $gateway)
    {
        echo $bearer;

        $this->userType = $this->validateToken($bearer, $gateway);

    }

    private function validateToken(string $bearer, ProductGateway $gateway): ?UserTypes
    {

        if (preg_match('/Bearer\s(\S+)/', $bearer, $match))
        {
            $token = $match[1];
        }
        else
        {
            echo("Authorization header does not contain a bearer heading.");
            return null;
        }

        $result = $gateway->verifyToken($token);

        if ($result === false)      // user is listed as a customer within the database
        {
            return UserTypes::Customer;
        }
        else if ($result === true)
        {
            echo("this works no?");
            return UserTypes::Admin;
        }
        else
        {
            return null;
        }

    }

    public function getUserType()
    {

        return $this->userType;

    }


}