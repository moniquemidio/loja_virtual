<?php
// Dados de configuração do PagSeguro
$email = 'seu_email@dominio.com'; // Substitua pelo seu e-mail
$token = 'seu_token'; // Substitua pelo seu token
$url = 'https://ws.pagseguro.uol.com.br/v2/checkout?email=' . $email . '&token=' . $token;

// Dados do checkout
$produto_id = $_GET['produto_id'];
$produto_nome = $_GET['produto_nome'];
$produto_preco = $_GET['produto_preco'];
$quantidade = $_GET['quantidade'];

// Dados do checkout
$xml = '<?xml version="1.0" encoding="UTF-8"?>
<checkout>
    <items>
        <item>
            <id>' . $produto_id . '</id>
            <description>' . htmlspecialchars($produto_nome) . '</description>
            <amount>' . $produto_preco . '</amount>
            <quantity>' . $quantidade . '</quantity>
        </item>
    </items>
</checkout>';

// Enviar a requisição para criar o checkout
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/xml',
));
$response = curl_exec($ch);
curl_close($ch);

// Processar a resposta
$responseXml = simplexml_load_string($response);
$code = $responseXml->code; // Código do checkout
$url_pagamento = 'https://pagseguro.uol.com.br/v2/checkout/payment.html?code=' . $code;

// Redirecionar para a página de pagamento do PagSeguro
header('Location: ' . $url_pagamento);
exit();
?>
