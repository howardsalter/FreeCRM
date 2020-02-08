<?php
namespace App\Action;

use Slim\Views\Twig;
use Psr\Log\LoggerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use PDO;

final class LogoutAction
{
    private $view;
    private $logger;

    public function __construct(Twig $view, LoggerInterface $logger)
    {
        $this->view = $view;
        $this->logger = $logger;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $this->logger->info("Login action dispatched");

        if (isset($_COOKIE['userlogin'])) {
            unset($_COOKIE['userlogin']);
            setcookie('userlogin', '', time() - 3600, '/');

            unset($_SESSION['userinfo']);
        }

        return $response->withRedirect('/');

    }
}
