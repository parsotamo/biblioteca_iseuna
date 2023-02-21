<?php

//base_dados_con.php
$db_usuario = "b2230ba974860a";
$db_senha = "443fe718";
$db_host = "us-cdbr-east-06.cleardb.net";
$db_nome = "heroku_152f715103496d7";

// $connect = mysqli_connect($db_host, $db_usuario, $db_senha, $db_nome);
// // Check connection
// if (!$conn) {
//     die("Connection failed: " . mysqli_connect_error());
// }
try {
    $connect = new PDO("mysql:host=$db_host;dbname=$db_nome", $db_usuario, $db_senha);
} catch (PDOException $pe) {
    die("Erro de conexão de Base de Dados");
}


// $connect = new PDO('mysql://' . $db_host . ';' . 'dbname=' . $db_nome, $db_usuario, $db_senha);

session_start();
