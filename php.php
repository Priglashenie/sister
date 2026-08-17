<?php

header("Content-Type: application/json; charset=UTF-8");


/* ==========================================
   TELEGRAM
   ========================================== */

$botToken = "8983993848:AAEfFggQV6tfGt8dp-3WdNbyGKDewYzvKnk";
$chatId   = "919523071";


/* ==========================================
   ПОЛУЧАЕМ ДАННЫЕ ОТ САЙТА
   ========================================== */

$data = json_decode(
    file_get_contents("php://input"),
    true
);


if (!$data) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "error" => "Нет данных"
    ]);

    exit;
}


/* ==========================================
   ДАННЫЕ
   ========================================== */

$answer = $data["answer"] ?? "Ответ не указан";
$date   = $data["date"] ?? date("d.m.Y H:i");


/* ==========================================
   СООБЩЕНИЕ
   ========================================== */

$message =
    "💌 <b>Ответ на приглашение</b>\n\n" .
    "❤️ " . htmlspecialchars($answer, ENT_QUOTES, "UTF-8") . "\n" .
    "🕐 " . htmlspecialchars($date, ENT_QUOTES, "UTF-8");


/* ==========================================
   TELEGRAM API
   ========================================== */

$url =
    "https://api.telegram.org/bot" .
    $botToken .
    "/sendMessage";


$postFields = [
    "chat_id" => $chatId,
    "text" => $message,
    "parse_mode" => "HTML"
];


$ch = curl_init();


curl_setopt(
    $ch,
    CURLOPT_URL,
    $url
);

curl_setopt(
    $ch,
    CURLOPT_POST,
    true
);

curl_setopt(
    $ch,
    CURLOPT_POSTFIELDS,
    $postFields
);

curl_setopt(
    $ch,
    CURLOPT_RETURNTRANSFER,
    true
);

curl_setopt(
    $ch,
    CURLOPT_TIMEOUT,
    10
);


$response = curl_exec($ch);

$httpCode = curl_getinfo(
    $ch,
    CURLINFO_HTTP_CODE
);

$curlError = curl_error($ch);

curl_close($ch);


/* ==========================================
   ПРОВЕРКА
   ========================================== */

if ($curlError) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "error" => "cURL error"
    ]);

    exit;
}


$result = json_decode(
    $response,
    true
);


if (
    $httpCode >= 200 &&
    $httpCode < 300 &&
    isset($result["ok"]) &&
    $result["ok"] === true
) {

    echo json_encode([
        "success" => true
    ]);

} else {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "error" => "Telegram API error"
    ]);
}

?>