<?php
namespace App\Action;

use Slim\Views\Twig;
use Psr\Log\LoggerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use PDO;

final class InsertContactTransAction
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
        $this->logger->info("Insert Contact Transaction endpoint action dispatched");

        $reqBody = $request->getParsedBody();

        if (!isset($_COOKIE['userlogin'])) {
            // Redirect to home action
            return $response->withRedirect('/');
        }

        //Select User Info
        $sql = "INSERT INTO transactions
                    (org_id, customer_id, trans_date, trans_type, trans_amt, trans_desc)
                VALUES
                    (:org_id, :customer_id, STR_TO_DATE(:trans_date, '%m/%d/%Y'), :trans_type, :trans_amt, :trans_desc)
                ";

        //Prepare statement.
        $cust_insert = $this->db->prepare($sql);

        //Bind our values to parameters.
        $cust_insert->bindValue(':org_id', $reqBody["org_id"]);
        $cust_insert->bindValue(':customer_id', $reqBody["customer_id"]);
        $cust_insert->bindValue(':trans_date', $reqBody["trans_date"]);
        $cust_insert->bindValue(':trans_type', $reqBody["trans_type"]);
        $cust_insert->bindValue(':trans_amt', $reqBody["trans_amt"]);
        $cust_insert->bindValue(':trans_desc', $reqBody["trans_desc"]);

        //Execute the statement and insert our values.
        try {
            $cust_insert->execute();
        } catch(\Exception $e) {
            $error = $e->getMessage();
            return $response->withJson(["error" => $error], 500);
        }

        return $response->withJson(["error" => false], 200);

    }
}
