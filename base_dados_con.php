<?php

//base_dados_con.php
// require 'vendor/autoload.php';

// $dotenv = Dotenv\dotenv::createImmutable(__DIR__);
// $dotenv->load();

if (getenv("DEV")) {
    $connect = new PDO("mysql:host=localhost:3306;dbname=db_iseuna", "root", "123");
} else {
    $connect = new PDO('mysql:host=' . getenv("DB_HOST") . ';dbname=' . getenv("DB_NOME"), getenv("DB_USUARIO"), getenv("DB_SENHA"));
}

session_start();
