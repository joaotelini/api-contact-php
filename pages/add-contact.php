<?php
$apiUrl = 'http://localhost/api-contact/api/';

$name = $_POST['name'];
$email = $_POST['email'];
$phone = $_POST['phone'];

$data = [
  'name' => $name,
  'email' => $email,
  'phone' => $phone,
];

$jsonData = json_encode($data);

$curl = curl_init();

// Configurações do cURL
curl_setopt_array($curl, [
  CURLOPT_URL => $apiUrl,              // URL da API
  CURLOPT_RETURNTRANSFER => true,      // Retorna a resposta como string
  CURLOPT_POST => true,                // Define o método HTTP como POST
  CURLOPT_HTTPHEADER => [              // Cabeçalhos HTTP
      'Content-Type: application/json', // Especifica que os dados são JSON
  ],
  CURLOPT_POSTFIELDS => $jsonData,     // Dados da requisição
]);


$response = curl_exec($curl);

if (curl_errno($curl)) {
    echo 'Erro na requisição: ' . curl_error($curl);
    curl_close($curl);
    exit;
}

curl_close($curl);

$responseData = json_decode($response, true);

// Resposta da API

if (isset($responseData['message'])) {
  echo 'Resposta da API: ' . $responseData['message'];
  header('Location: ' . 'list-contacts.html');
} else {
  echo 'Erro na resposta da API.';
}
