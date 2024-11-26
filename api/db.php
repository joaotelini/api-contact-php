<?php

$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'contact_db';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
  die('Erro na conexao' . $conn->connect_error);
}