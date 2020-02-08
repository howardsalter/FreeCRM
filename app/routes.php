<?php
// View Routes ----------------------------------------------------------- //

$app->get('/', App\Action\HomeAction::class)
    ->setName('homepage');

$app->get('/about', App\Action\AboutAction::class)
    ->setName('aboutpage');

$app->get('/register', App\Action\RegisterAction::class)
    ->setName('registerpage');

$app->post('/login', App\Action\LoginAction::class)
    ->setName('login');

$app->get('/logout', App\Action\LogoutAction::class)
    ->setName('logout');

$app->post('/dashboard', App\Action\DashboardAction::class)
    ->setName('dashboard');

$app->get('/dashboard', App\Action\DashboardAction::class)
    ->setName('dashboard');

$app->get('/contacts', App\Action\ContactsAction::class)
    ->setName('contacts');

$app->get('/search/{token}', App\Action\SearchAction::class)
    ->setName('searchcontacts');

    $app->get('/contacts/{id}', App\Action\ContactDetailsAction::class)
    ->setName('contactdetails');

$app->get('/transactions', App\Action\TransactionsAction::class)
    ->setName('transactions');


// API Routes ------------------------------------------------------------ //

$app->post('/api/contact', App\Action\InsertContactAction::class)
    ->setName('insertcontact');

$app->put('/api/contact/{id}', App\Action\UpdateContactAction::class)
    ->setName('updatecontact');

$app->delete('/api/contact/{id}', App\Action\DeleteContactAction::class)
    ->setName('deletecontact');

$app->post('/api/contact/history', App\Action\InsertContactHistoryAction::class)
    ->setName('insertcontacthistory');

$app->post('/api/contact/notes', App\Action\InsertContactNotesAction::class)
    ->setName('insertcontactnotes');

$app->post('/api/contact/transactions', App\Action\InsertContactTransAction::class)
    ->setName('insertcontacttrans');

$app->post('/api/sendemail', App\Action\SendEmailAction::class)
    ->setName('sendemail');
