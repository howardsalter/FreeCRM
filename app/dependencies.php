<?php
// DIC configuration

$container = $app->getContainer();

// -----------------------------------------------------------------------------
// Service providers
// -----------------------------------------------------------------------------

// Twig
$container['view'] = function ($c) {
    $settings = $c->get('settings');
    $view = new Slim\Views\Twig($settings['view']['template_path'], $settings['view']['twig']);

    // Add extensions
    $view->addExtension(new Slim\Views\TwigExtension($c->get('router'), $c->get('request')->getUri()));
    $view->addExtension(new Twig_Extension_Debug());

    return $view;
};

// Flash messages
$container['flash'] = function ($c) {
    return new Slim\Flash\Messages;
};

// -----------------------------------------------------------------------------
// Service factories
// -----------------------------------------------------------------------------

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings');
    $logger = new Monolog\Logger($settings['logger']['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['logger']['path'], Monolog\Logger::DEBUG));
    return $logger;
};

$container['db'] = function ($c) {
    $db = $c['settings']['db'];
    $pdo = new PDO("mysql:host=" . $db['host'] . ";dbname=" . $db['dbname'],
        $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};

// -----------------------------------------------------------------------------
// Action factories
// -----------------------------------------------------------------------------

$container[App\Action\HomeAction::class] = function ($c) {
    return new App\Action\HomeAction($c->get('view'), $c->get('logger'));
};

$container[App\Action\AboutAction::class] = function ($c) {
    return new App\Action\AboutAction($c->get('view'), $c->get('logger'));
};

$container[App\Action\LoginAction::class] = function ($c) {
    return new App\Action\LoginAction($c->get('view'), $c->get('logger'), $c->get('db'));
};

$container[App\Action\LogoutAction::class] = function ($c) {
    return new App\Action\LogoutAction($c->get('view'), $c->get('logger'));
};

$container[App\Action\DashboardAction::class] = function ($c) {
    return new App\Action\DashboardAction($c->get('view'), $c->get('logger'), $c->get('db'));
};

$container[App\Action\SearchAction::class] = function ($c) {
    return new App\Action\SearchAction($c->get('view'), $c->get('logger'), $c->get('db'));
};

$container[App\Action\ContactsAction::class] = function ($c) {
    return new App\Action\ContactsAction($c->get('view'), $c->get('logger'), $c->get('db'));
};

$container[App\Action\TransactionsAction::class] = function ($c) {
    return new App\Action\TransactionsAction($c->get('view'), $c->get('logger'), $c->get('db'));
};

$container[App\Action\ContactDetailsAction::class] = function ($c) {
    return new App\Action\ContactDetailsAction($c->get('view'), $c->get('logger'), $c->get('db'));
};

$container[App\Action\InsertContactAction::class] = function ($c) {
    return new App\Action\InsertContactAction($c->get('view'), $c->get('logger'), $c->get('db'));
};

$container[App\Action\UpdateContactAction::class] = function ($c) {
    return new App\Action\UpdateContactAction($c->get('view'), $c->get('logger'), $c->get('db'));
};

$container[App\Action\DeleteContactAction::class] = function ($c) {
    return new App\Action\DeleteContactAction($c->get('view'), $c->get('logger'), $c->get('db'));
};

$container[App\Action\InsertContactHistoryAction::class] = function ($c) {
    return new App\Action\InsertContactHistoryAction($c->get('view'), $c->get('logger'), $c->get('db'));
};

$container[App\Action\InsertContactNotesAction::class] = function ($c) {
    return new App\Action\InsertContactNotesAction($c->get('view'), $c->get('logger'), $c->get('db'));
};

$container[App\Action\InsertContactTransAction::class] = function ($c) {
    return new App\Action\InsertContactTransAction($c->get('view'), $c->get('logger'), $c->get('db'));
};

$container[App\Action\SendEmailAction::class] = function ($c) {
    return new App\Action\SendEmailAction($c->get('view'), $c->get('logger'), $c->get('db'), $mailer);
};

$container[App\Action\RegisterAction::class] = function ($c) {
    return new App\Action\RegisterAction($c->get('view'), $c->get('logger'), $c->get('db'));
};

