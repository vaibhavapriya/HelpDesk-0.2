<?php
use Dotenv\Dotenv;

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ );
$dotenv->load();


ob_start();

session_start();
// Load the router router.php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/router/routes.php';
ob_end_flush();


