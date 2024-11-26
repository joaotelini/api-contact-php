<?php

header('Content-Type: application/json');
require 'db.php';

$method = $_SERVER['REQUEST_METHOD'];

// Função para validar campos obrigatórios
function validateField($field, $fieldName) {
  if (empty($field)) {
    http_response_code(400);
    echo json_encode(["message" => "$fieldName é obrigatório!"]);
    exit;
  }
}

// Função para validar se o ID é numérico
function validateId($id) {
  if (!isset($id) || !is_numeric($id)) {
    http_response_code(400);
    echo json_encode(["message" => "ID inválido."]);
    exit;
  }
}

// Função para verificar se a requisição contém dados válidos
function getDataFromRequest() {
  $data = json_decode(file_get_contents('php://input'), true);
  if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(["message" => "Erro no formato JSON."]);
    exit;
  }
  return $data;
}

switch ($method) {
  case 'GET':

    $result = $conn->query('SELECT * FROM contact');
    $contact = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode($contact);
    break;

  case 'POST':

    $data = getDataFromRequest();
    $name = $data['name'] ?? null;
    $email = $data['email'] ?? null;
    $phone = $data['phone'] ?? null;

    validateField($name, 'Nome');
    validateField($email, 'Email');
    validateField($phone, 'Telefone');


    $stmt = $conn->prepare('INSERT INTO contact (name, email, phone) VALUES (?,?,?)');
    $stmt->bind_param('sss', $name, $email, $phone);
    $stmt->execute();
    echo json_encode(["message" => "Contato adicionado com sucesso!"]);
    break;

  case 'PUT':

    $data = getDataFromRequest();
    $id = $data['id'] ?? null;
    validateId($id);

    $fieldsToUpdate = [];
    $values = [];


    if (isset($data['name'])) {
      $name = $data['name'];
      validateField($name, 'Nome');
      $fieldsToUpdate[] = "name = ?";
      $values[] = $name;
    }

    if (isset($data['email'])) {
      $email = $data['email'];
      validateField($email, 'Email');
      $fieldsToUpdate[] = "email = ?";
      $values[] = $email;
    }

    if (isset($data['phone'])) {
      $phone = $data['phone'];
      validateField($phone, 'Telefone');
      $fieldsToUpdate[] = "phone = ?";
      $values[] = $phone;
    }


    if (empty($fieldsToUpdate)) {
      http_response_code(400);
      echo json_encode(["message" => "Nada para atualizar. Envie pelo menos 'name', 'email' ou 'phone'."]);
      exit;
    }


    $stmt = $conn->prepare("UPDATE contact SET " . implode(", ", $fieldsToUpdate) . " WHERE id = ?");
    $values[] = $id;
    $stmt->bind_param(str_repeat("s", count($values)-1) . "i", ...$values);
    $stmt->execute();
    echo json_encode(["message" => "Contato atualizado com sucesso!"]);
    break;

  case 'DELETE':

    $data = getDataFromRequest();
    $id = $data['id'] ?? null;
    validateId($id);


    $stmt = $conn->prepare('DELETE FROM contact WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();

    echo json_encode(["message" => "Contato excluído com sucesso!"]);
    break;

  default:
  
    // Caso o método HTTP não seja suportado
    http_response_code(405);
    echo json_encode(['message' => 'Método não suportado']);
    break;
}
