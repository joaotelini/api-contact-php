<?php
// form-handler.php
header('Content-Type: application/json');
require 'db.php';  // Inclui a conexão com o banco de dados

// Função para validar campos obrigatórios
function validateField($field, $fieldName) {
    if (empty($field)) {
        http_response_code(400);
        echo json_encode(["message" => "$fieldName é obrigatório!"]);
        exit;
    }
}

function handleFormSubmission($conn) {
    // Obtendo os dados do formulário (espera-se que sejam enviados em JSON)
    $data = json_decode(file_get_contents('php://input'), true);

    // Verificando se o JSON é válido
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(["message" => "Erro no formato JSON."]);
        exit;
    }

    // Validando os campos obrigatórios
    $name = $data['name'] ?? null;
    $email = $data['email'] ?? null;
    $phone = $data['phone'] ?? null;

    validateField($name, 'Nome');
    validateField($email, 'Email');
    validateField($phone, 'Telefone');

    // Preparando a inserção no banco de dados
    $stmt = $conn->prepare('INSERT INTO contact (name, email, phone) VALUES (?, ?, ?)');
    if ($stmt === false) {
        http_response_code(500);
        echo json_encode(["message" => "Erro ao preparar a consulta SQL."]);
        exit;
    }

    // Vinculando os parâmetros
    $stmt->bind_param('sss', $name, $email, $phone);
    $executeResult = $stmt->execute();

    if ($executeResult) {
        // Se a execução for bem-sucedida, retorna sucesso
        echo json_encode(["message" => "Contato adicionado com sucesso!"]);
    } else {
        // Se houver erro ao executar, retorna erro
        http_response_code(500);
        echo json_encode(["message" => "Erro ao executar a consulta SQL."]);
    }
}

// processar o envio do formulário
handleFormSubmission($conn);
?>
