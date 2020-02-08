<?php
namespace App\Action;

use Slim\Views\Twig;
use Psr\Log\LoggerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use PDO;

final class InsertContactNotesAction
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
        $this->logger->info("Insert Contact Notes endpoint action dispatched");

        $reqBody = $request->getParsedBody();

        if (!isset($_COOKIE['userlogin'])) {
            // Redirect to home action
            return $response->withRedirect('/');
        }

        //Select User Info
        $sql = "INSERT INTO notes
                    (customer_id, note_date, note)
                VALUES
                    (:customer_id, STR_TO_DATE(:note_date, '%m/%d/%Y'), :note)
                ";

        //Prepare statement.
        $cust_insert = $this->db->prepare($sql);

        //Bind our values to parameters.
        $cust_insert->bindValue(':customer_id', $reqBody["customer_id"]);
        $cust_insert->bindValue(':note_date', $reqBody["note_date"]);
        $cust_insert->bindValue(':note', $reqBody["note"]);

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
