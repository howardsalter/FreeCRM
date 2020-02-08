<?php
namespace App\Action;

use Slim\Views\Twig;
use Psr\Log\LoggerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use PDO;

final class UpdateContactAction
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

        $data = [
                    $reqBody["first_name"],
                    $reqBody["last_name"],
                    $reqBody["address1"],
                    $reqBody["city"],
                    $reqBody["st"],
                    $reqBody["zip"],
                    $reqBody["phone"],
                    $reqBody["email"],
                    $reqBody["birthday"],
                    $reqBody["spouse_first"],
                    $reqBody["spouse_last"],
                    $args["id"]
        ];

        if (!isset($_COOKIE['userlogin'])) {
            // Redirect to home action
            return $response->withRedirect('/');
        }

        //Select User Info
        $sql = "UPDATE customers SET 
                    first_name=?, 
                    last_name=?, 
                    address1=?, 
                    city=?, 
                    st=?, 
                    zip=?, 
                    phone=?, 
                    email=?, 
                    birthday=?, 
                    spouse_first=?, 
                    spouse_last=?
                WHERE id = ?
                ";

        //Prepare statement.
        $cust_update = $this->db->prepare($sql);

        //Execute the statement and insert our values.
        try {
            $cust_update->execute($data);
        } catch(\Exception $e) {
            $error = $e->getMessage();
            $this->logger->info($error);
            return $response->withJson(["error" => $error], 500);
        }

        return $response->withJson(["error" => false], 200);

    }
}
