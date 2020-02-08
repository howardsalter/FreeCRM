<?php
namespace App\Action;

use Slim\Views\Twig;
use Psr\Log\LoggerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use PDO;

final class InsertContactAction
{
    private $view;
    private $logger;
    private $db;

    public function __construct(Twig $view, LoggerInterface $logger, PDO $db)
    {
        $this->view = $view;
        $this->logger = $logger;
        $this->db = $db;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $this->logger->info("Insert Contact endpoint action dispatched");

        $reqBody = $request->getParsedBody();

        if (!isset($_COOKIE['userlogin'])) {
            // Redirect to home action
            return $response->withRedirect('/');
        }

        //Select User Info
        $sql = "INSERT INTO customers
                    (org_id, first_name, last_name, address1, city, st, zip, phone, email)
                VALUES
                    (:org_id, :first_name, :last_name, :address1, :city, :st, :zip, :phone, :email)
                ";

        //Prepare statement.
        $cust_insert = $this->db->prepare($sql);

        //Bind our values to parameters.
        $cust_insert->bindValue(':org_id', $reqBody["org_id"]);
        $cust_insert->bindValue(':first_name', $reqBody["first_name"]);
        $cust_insert->bindValue(':last_name', $reqBody["last_name"]);
        $cust_insert->bindValue(':address1', $reqBody["address1"]);
        $cust_insert->bindValue(':city', $reqBody["city"]);
        $cust_insert->bindValue(':st', $reqBody["st"]);
        $cust_insert->bindValue(':zip', $reqBody["zip"]);
        $cust_insert->bindValue(':phone', $reqBody["phone"]);
        $cust_insert->bindValue(':email', $reqBody["email"]);

        //Execute the statement and insert our values.
        try {
            $cust_insert->execute();
        } catch(\Exception $e) {
            $error = $e->getMessage();
            return $response->withJson(["error" => $error], 500);
        }

        $customer_id = $this->db->lastInsertId();
        
        return $response->withJson(["error" => false, "id" => $customer_id], 200);

    }
}
