<?php

//base_dados_con.php
require 'vendor/autoload.php';

$dotenv = Dotenv\dotenv::createImmutable(__DIR__);
$dotenv->load();

if ($_ENV["DEV"]) {
    $connect = new PDO("mysql:host=localhost:3306;dbname=db_iseuna", "root", "123");
} else {
    $connect = new PDO('mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NOME'], $_ENV['DB_USUARIO'], $_ENV['DB_SENHA']);
}

session_start();
