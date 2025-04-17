<?php
namespace app\controller;

class HomeController {
    public function home() {
        require __DIR__ . '/../views/home.view.php';
    }
    public function knowledgeBase() {
        require __DIR__ . '/../views/knowledgebase.view.php';
    }
    public function login() {
        require __DIR__ . '/../views/login.view.php';
    }
    public function register() {
        require __DIR__ . '/../views/register.view.php';
    }
    public function forgotpassword() {
        require __DIR__ . '/../views/forgotpassword.view.php';
    }
    public function resetpassword() {
        require __DIR__ . '/../views/resetpassword.view.php';
    }
    public function profile(){
        require __DIR__ . '/../views/profile.view.php';
    }
    public function submitTicket(){
        require __DIR__ . '/../views/submitTicket.view.php';
    }
    public function myTickets(){
        require __DIR__ . '/../views/myTickets.view.php';
    }
    public function userTicket(){
        require __DIR__ . '/../views/resetpassword.view.php';
    }
    public function adminHome(){
        require __DIR__ . '/../views/adminhome.view.php';
    }
    public function adminTicket(){
        require __DIR__ . '/../views/adminTicket.view.php';
    }
    public function errorpage(){
        require __DIR__ . '/../views/errorpage.view.php';
    }
    public function tickets(){
        require __DIR__ . '/../views/tickets.view.php';
    }
    public function adminProfile(){
        require __DIR__ . '/../views/adProfile.view.php';
    }
    public function clientTicket(){
        require __DIR__ . '/../views/clientTicket.view.php';
    }
    public function logout(){
        require __DIR__ . '/../views/logout.view.php';
    }
}
