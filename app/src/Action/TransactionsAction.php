<?php
namespace App\Action;

use Slim\Views\Twig;
use Psr\Log\LoggerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use PDO;

final class TransactionsAction
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
        $this->logger->info("Transactions endpoint action dispatched");

        if (!isset($_COOKIE['userlogin'])) {
            // Redirect to home action
            return $response->withRedirect('/');
        } else {
            $date_of_expiry = time() + 60 * 60 * 2;
            setcookie( "userlogin", $_SESSION["userinfo"]['id'], $date_of_expiry );
        }

        //Select User Info
        $sql = "SELECT customers.first_name, customers.last_name, customers.spouse_first, customers.email, transactions.*
                FROM customers, transactions
                WHERE customers.id = transactions.customer_id
                AND transactions.org_id = :orgId";


        //Prepare statement.
        $trans_select = $this->db->prepare($sql);

        //Bind our values to parameters.
        $trans_select->bindValue(':orgId', $_SESSION["userinfo"]["org_id"]);

        //Execute the statement and insert our values.
        try {
            $trans_select->execute();
        } catch(\Exception $e) {
            $error = $e->getMessage();
            return $response->write("<h1>Unable to load your information!</h1><br /><p>Error Information:<br />$error</p><button id='btnContinue' onclick='window.location.replace(\"/\")'>Return</button>");
        }

        $result = $trans_select->fetchAll();
        $transactions = false;
        if ($result) {
            $transactions = $result;
        }
        
        $this->logger->info($_SESSION["userinfo"]["org_id"]);
        $this->logger->info($_SESSION["userinfo"]);

        $this->view->render($response, 'transactions.twig', ["user" => $_SESSION["userinfo"], "transactions" => $transactions, "org_id" => $_SESSION["userinfo"]["org_id"]]);

        return $response;
    }
}
