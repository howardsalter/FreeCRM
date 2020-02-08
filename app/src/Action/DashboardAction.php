<?php
namespace App\Action;

use Slim\Views\Twig;
use Psr\Log\LoggerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use PDO;

final class DashboardAction
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
        $this->logger->info("Dashboard endpoint action dispatched");
        $this->logger->info(isset($_COOKIE['userlogin']));
        $this->logger->info("// -------------------------------------------- //");

        if (!isset($_COOKIE['userlogin'])) {
            // Redirect to home action
            return $response->withRedirect('/');
        } 

        //Select User Info
        $sql = "SELECT users.*, name 
                FROM users, organizations
                WHERE users.id = :userid
                AND organizations.id = users.org_id";

        //Prepare statement.
        $usr_select = $this->db->prepare($sql);

        //Bind our values to parameters.
        $usr_select->bindValue(':userid', $_COOKIE['userlogin']);

        //Execute the statement and insert our values.
        try {
            $usr_select->execute();
        } catch(\Exception $e) {
            $error = $e->getMessage();
            return $response->write("<h1>Unable to load your information!</h1><br /><p>Error Information:<br />$error</p><button id='btnContinue' onclick='window.location.replace(\"/\")'>Return</button>");
        }

        $result = $usr_select->fetch();

        if ($result) {
            $_SESSION['userinfo'] = $result;
        }
        $numTrans = $this->getData("transactions", $_SESSION["userinfo"]["org_id"]);
        $numCust = $this->getData("customers", $_SESSION["userinfo"]["org_id"]);

        $this->view->render($response, 'dashboard.twig', ["user" => $result, "contacts" => $numCust, "transactions" => $numTrans]);

        return $response;
    }

    public function getData($table, $orgId) {

        //Select Total Number of Transactions
        $sql = "SELECT count(*) as rec_count
                FROM " . $table . "
                WHERE org_id = :orgid GROUP BY org_id";

        //Prepare statement.
        $data_select = $this->db->prepare($sql);

        //Bind our values to parameters.
        $data_select->bindValue(':orgid', $orgId);

        //Execute the statement and insert our values.
        try {
            $data_select->execute();
        } catch(\Exception $e) {
            $error = $e->getMessage();
            $this->logger->info("<h1>Unable to load your information!</h1><br /><p>Error Information:<br />$error</p><button id='btnContinue' onclick='window.location.replace(\"/\")'>Return</button>");
            return;
        }

        $result = $data_select->fetch();

        if ($result) {
            $retVal = $result["rec_count"];
        } else {
            $error = $data_select->queryString;
            $this->logger->info($error);
            $retVal = 0;
        }

        return $retVal;

    }

}
