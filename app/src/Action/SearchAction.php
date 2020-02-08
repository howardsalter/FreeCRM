<?php
namespace App\Action;

use Slim\Views\Twig;
use Psr\Log\LoggerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use PDO;

final class SearchAction
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

        $this->logger->info("// -------------------------------------------- //");
        $this->logger->info("Search endpoint action dispatched");
        $this->logger->info(isset($_COOKIE['userlogin']));
        $this->logger->info("// -------------------------------------------- //");

        if (!isset($_COOKIE['userlogin'])) {
            // Redirect to home action
            return $response->withRedirect('/');
        } else {
            $date_of_expiry = time() + 60 * 60 * 2;
            setcookie( "userlogin", $_SESSION["userinfo"]['id'], $date_of_expiry );
        }

        $tokenArray = [];
        if (strpos($args["token"], " ") > -1) {
            $tokenArray = explode(" ", $args["token"]);
        }

        if (count($tokenArray) > 0){
            $sql = "SELECT customers.*
                    FROM customers
                    WHERE customers.org_id = :orgId
                        AND (";
            for($i = 0; $i < count($tokenArray); $i++){
                $sql .= "first_name like '%" . $tokenArray[i] ."%' OR
                last_name like '%" . $tokenArray[i] ."%' OR
                address1 like '%" . $tokenArray[i] ."%' OR
                city like '%" . $tokenArray[i] ."%' OR
                st like '%" . $tokenArray[i] ."%' OR
                zip like '%" . $tokenArray[i] ."%' OR
                email like '%" . $tokenArray[i] ."%' OR ";
            }

            $sql = substr($sql, 0, -3);

            $sql .= ")";

        } else {
            //Select User Info
            $sql = "SELECT customers.*
                    FROM customers
                    WHERE customers.org_id = :orgId
                        AND (
                                first_name like '%" . $args["token"] ."%' OR
                                last_name like '%" . $args["token"] ."%' OR
                                address1 like '%" . $args["token"] ."%' OR
                                city like '%" . $args["token"] ."%' OR
                                st like '%" . $args["token"] ."%' OR
                                zip like '%" . $args["token"] ."%' OR
                                email like '%" . $args["token"] ."%'
                            )";
        }

        //Prepare statement.
        $cust_select = $this->db->prepare($sql);

        //Bind our values to parameters.
        $cust_select->bindValue(':orgId', $_SESSION["userinfo"]["org_id"]);

        //Execute the statement and insert our values.
        try {
            $cust_select->execute();
        } catch(\Exception $e) {
            $error = $e->getMessage();
            return $response->write("<h1>Unable to load your information!</h1><br /><p>Error Information:<br />$error</p><button id='btnContinue' onclick='window.location.replace(\"/\")'>Return</button>");
        }

        $result = $cust_select->fetchAll();
        $contacts = false;
        if ($result) {
            $contacts = $result;
        }
        
        $this->logger->info($contacts);
        $this->logger->info($_SESSION["userinfo"]);

        $this->view->render($response, 'contacts.twig', ["user" => $_SESSION["userinfo"], "contacts" => $contacts, "org_id" => $_SESSION["userinfo"]["org_id"], "search" => "true", "token" => $args["token"]]);

        return $response;
    }
}
