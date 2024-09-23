<?php
// conexão copm o banco de dados
$hostname = "localhost";
$username = "root";
$password = "root";
$database = "crud_dupla";

$conn = new mysqli($hostname, $username, $password, $database);

if($conn->connect_error){
    die("conexão falhou: ") . $conn->connect_error;
}
?>