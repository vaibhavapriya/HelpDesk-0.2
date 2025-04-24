<?php


require_once __DIR__ . '/router.php'; 


use app\controller\HomeController;
use app\controller\UserController;
use app\controller\TicketController;
use app\controller\AdminController;
use app\controller\MailController;
use app\services\AuthMiddleware;
use app\services\AuthRole;
use app\services\CheckTicket;

$router = new Router('/HelpDesk-0.2');

// Sample route
$router->add('/', HomeController::class, 'home');
$router->add('/home', HomeController::class, 'home');
$router->add('/knowledgeBase', HomeController::class,'knowledgeBase');
$router->add('/login', HomeController::class, 'login');
$router->add('/register', HomeController::class, 'register');
$router->add('/forgotPassword', HomeController::class, 'forgotpassword');
$router->add('/resetPassword', HomeController::class, 'resetpassword');
$router->add('/profile', HomeController::class, 'profile');
$router->add('/newTicket', HomeController::class, 'submitTicket');
$router->add('/myTickets', HomeController::class, 'myTickets');
$router->add('/logout', HomeController::class, 'logout');

$router->add('/adminhome', HomeController::class, 'adminHome');
$router->add('/adminTicket', HomeController::class, 'adminTicket');
$router->add('/adminProfile', HomeController::class, 'adminProfile');
$router->add('/userprofile', HomeController::class, 'userprofile');
$router->add('/users', HomeController::class, 'users');
$router->add('/tickets', HomeController::class, 'tickets');
$router->add('/errorlog', HomeController::class, 'errorpage');
$router->add('/replyTicket', HomeController::class, 'replyTicket');
$router->add('/editUser', HomeController::class, 'editUser');
$router->add('/mail', HomeController::class, 'mailerconfig');

$router->add('/register/post', UserController::class, 'register');
$router->add('/login/post', UserController::class, 'login');
$router->add('/forgotPassword/post', UserController::class, 'forgotPassword');
$router->add('/resetPassword/post', UserController::class, 'resetPassword');
$router->add('/newTicket/post',TicketController::class, 'submitTicket', [AuthMiddleware::class]);
$router->add('/myTickets/get', TicketController::class, 'myTickets', [AuthMiddleware::class]);
$router->add('/profile/get', UserController::class, 'profile', [AuthMiddleware::class]);
$router->add('/profile/post', UserController::class, 'profileChange', [AuthMiddleware::class]);
$router->add('/profile/password/post', UserController::class, 'passwordChange', [ AuthMiddleware::class]);
$router->add('/clientTicket', HomeController::class, 'clientTicket');
$router->add('/editTicket', HomeController::class, 'editTicket');
$router->add('/image', TicketController::class, 'serveAttachment');
$router->add('/clientTicket/get', TicketController::class, 'clientTicket', [ AuthMiddleware::class]);
$router->add('/editTicket/get', TicketController::class, 'ticket', [ AuthMiddleware::class]);
$router->add('/editTicket/post', TicketController::class, 'editTicket', [ AuthMiddleware::class]);

$router->add('/tickets/get', AdminController::class, 'tickets', [AuthMiddleware::class, AuthRole::class]);
$router->add('/errorlog/get', AdminController::class, 'errorlogs', [AuthMiddleware::class, AuthRole::class]);
$router->add('/users/get', AdminController::class, 'requesters', [AuthMiddleware::class, AuthRole::class]);
$router->add('/userinfo/get', AdminController::class, 'users', [AuthMiddleware::class, AuthRole::class]);
$router->add('/userprofile/get', AdminController::class, 'userprofile', [AuthMiddleware::class, AuthRole::class]);
$router->add('/adminTicket/post', AdminController::class, 'submitTicket', [AuthMiddleware::class, AuthRole::class]);
$router->add('/replyTicket/post', AdminController::class, 'replyTicket', [AuthMiddleware::class, AuthRole::class]);
$router->add('/editUser/get', AdminController::class, 'user', [AuthMiddleware::class, AuthRole::class]);
$router->add('/replyTicket/get', AdminController::class, 'ticket', [AuthMiddleware::class, AuthRole::class]);
$router->add('/editUser/post', AdminController::class, 'editUser', [AuthMiddleware::class, AuthRole::class]);
$router->add('/deleteUser/post', AdminController::class, 'deleteUser', [AuthMiddleware::class, AuthRole::class]);
$router->add('/deleteTicket/post', TicketController::class, 'deleteTicket', [AuthMiddleware::class]);
$router->add('/mc/get', MailController::class, 'fetchMailConfigs', [AuthMiddleware::class, AuthRole::class]);
$router->add('/mc/delete', MailController::class, 'deleteMailConfig', [AuthMiddleware::class, AuthRole::class]);
$router->add('/mc/post', MailController::class, 'addMailConfig', [AuthMiddleware::class, AuthRole::class]);
$router->add('/mc/activate', MailController::class, 'activateEmail', [AuthMiddleware::class, AuthRole::class]);





// Dispatch
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);


