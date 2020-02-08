<?php
namespace App\Action;

use Slim\Views\Twig;
use Psr\Log\LoggerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use PDO;

final class InsertContactHistoryAction
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
        $this->logger->info("Insert Contact History endpoint action dispatched");

        $reqBody = $request->getParsedBody();

        if (!isset($_COOKIE['userlogin'])) {
            // Redirect to home action
            return $response->withRedirect('/');
        }

        //Select User Info
        $sql = "INSERT INTO contact_history
                    (customer_id, contact_date, contact_type, contact_notes)
                VALUES
                    (:customer_id, STR_TO_DATE(:contact_date, '%m/%d/%Y'), :contact_type, :contact_notes)
                ";

        //Prepare statement.
        $cust_insert = $this->db->prepare($sql);

        //Bind our values to parameters.
        $cust_insert->bindValue(':customer_id', $reqBody["customer_id"]);
        $cust_insert->bindValue(':contact_date', $reqBody["contact_date"]);
        $cust_insert->bindValue(':contact_type', $reqBody["contact_type"]);
        $cust_insert->bindValue(':contact_notes', $reqBody["contact_notes"]);

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
