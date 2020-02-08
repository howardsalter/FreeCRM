<?php
namespace App\Action;

use Slim\Views\Twig;
use Psr\Log\LoggerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use PDO;

final class ContactDetailsAction
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
        $this->logger->info("Contacts endpoint action dispatched");

        if (!isset($_COOKIE['userlogin'])) {
            // Redirect to home action
            return $response->withRedirect('/');
        } else {
            $date_of_expiry = time() + 60 * 60 * 2;
            setcookie( "userlogin", $_SESSION["userinfo"]['id'], $date_of_expiry );
        }

        //Select User Info
        $sql = "SELECT customers.*
                FROM customers
                WHERE customers.id = :custId";

        //Prepare statement.
        $cust_select = $this->db->prepare($sql);

        //Bind our values to parameters.
        $cust_select->bindValue(':custId', $args["id"]);

        //Execute the statement and insert our values.
        try {
            $cust_select->execute();
        } catch(\Exception $e) {
            $error = $e->getMessage();
            return $response->write("<h1>Unable to load your information!</h1><br /><p>Error Information:<br />$error</p><button id='btnContinue' onclick='window.location.replace(\"/\")'>Return</button>");
        }

        $result = $cust_select->fetch();
        $contact = false;
        if ($result) {
            $contact = $result;
        }

        $history = $this->getData("contact_history", $args["id"], "contact_date");
        $notes = $this->getData("notes", $args["id"], "note_date");
        $transactions = $this->getData("transactions", $args["id"], "trans_date");

        $numTrans = count($transactions);
        $this->logger->info("// ---- Transactions ------------------------- //");
        $this->logger->info(print_r($transactions, true));
        $this->logger->info("// ---- Transactions ------------------------- //");

        $this->view->render($response, 'contactdetails.twig', 
            [
                "user" => $_SESSION["userinfo"], 
                "contact" => $contact, 
                "history" => $history,
                "notes" => $notes,
                "transactions" => $transactions,
                "numTrans" => $numTrans
            ]
        );

        return $response;
    }

    public function getData ($table, $id, $sort) {
        //Select User Info
        $sql = "SELECT " . $table . ".*
                FROM " . $table . "
                WHERE " . $table . ".customer_id = :custId
                ORDER BY " . $sort . " DESC";

        //Prepare statement.
        $data_select = $this->db->prepare($sql);

        //Bind our values to parameters.
        $data_select->bindValue(':custId', $id);

        //Execute the statement and insert our values.
        try {
            $data_select->execute();
        } catch(\Exception $e) {
            $error = $e->getMessage();
            return $response->write("<h1>Unable to load your information!</h1><br /><p>Error Information:<br />$error</p><button id='btnContinue' onclick='window.location.replace(\"/\")'>Return</button>");
        }

        $result = $data_select->fetchAll();
        $data = false;
        if ($result) {
            $data = $result;
        }

        return $data;

    }

}
