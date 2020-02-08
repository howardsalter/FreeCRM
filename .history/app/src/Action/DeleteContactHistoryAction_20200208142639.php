<?php
namespace App\Action;

use Slim\Views\Twig;
use Psr\Log\LoggerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use PDO;

final class DeleteContactHistoryAction
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

        //Select User Info
        $sql = "DELETE FROM contact_history
                    WHERE id = :id
                ";

        //Prepare statement.
        $cust_insert = $this->db->prepare($sql);

        //Bind our values to parameters.
        $cust_insert->bindValue(':id', $args["id"]);

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
