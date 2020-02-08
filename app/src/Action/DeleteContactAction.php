<?php
namespace App\Action;

use Slim\Views\Twig;
use Psr\Log\LoggerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use PDO;

final class DeleteContactAction
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

        // Needed to process multiple statements in a signle prepare.
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, 1);
        
        //Delete Statements
        $sql = "
            DELETE FROM customers WHERE id = :id;
            DELETE FROM contact_history WHERE customer_id = :id;
            DELETE FROM notes WHERE customer_id = :id;
            DELETE FROM transactions WHERE customer_id = :id;
        ";


        //Execute the statement and insert our values.
        try {
            $cust_delete = $this->db->prepare($sql);
            $cust_delete->bindValue(':id', $args["id"]);
            $cust_delete->execute();
        } catch(\Exception $e) {
            $error = $e->getMessage();
            return $response->withJson(["error" => $error], 500);
        }

        return $response->withJson(["error" => false], 200);

    }
}
