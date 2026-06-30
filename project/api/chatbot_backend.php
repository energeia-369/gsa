<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Method not allowed"]);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$question = $data['question'] ?? '';

if (empty($question)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Question is required"]);
    exit();
}

// TODO: Replace with your actual Gemini API Key
$apiKey = "YOUR_GEMINI_API_KEY_HERE";

if ($apiKey === "YOUR_GEMINI_API_KEY_HERE") {
    // Basic fallback logic if API key is not set
    $qLower = strtolower($question);
    if (strpos($qLower, 'register') !== false) {
        $answer = "To register on our platform, please head to the Register page from the navigation bar. You can sign up as a User or a Merchant!";
    } elseif (strpos($qLower, 'nxl') !== false || strpos($qLower, 'credit') !== false) {
        $answer = "NXL Credits are our platform's virtual currency! You earn them through loyalty, participation, or from merchants, and you can use them to purchase sports products or event tickets.";
    } elseif (strpos($qLower, 'event') !== false || strpos($qLower, 'tournament') !== false) {
        $answer = "We host a variety of exciting sports tournaments! Please visit our Events page to see the upcoming schedule and register your team.";
    } elseif (strpos($qLower, 'merchant') !== false) {
        $answer = "Merchants can log in to their dedicated dashboard to issue NXL credits, manage store inventory, and track user orders.";
    } elseif ($qLower === 'hi' || $qLower === 'hello' || $qLower === 'hey') {
        $answer = "Hello there! I'm E.V.A., your virtual assistant. How can I help you with GLOBAL SPORTS ARENA today?";
    } else {
        $answer = "I'm sorry, my AI backend is not fully configured yet because the API key is missing. Please contact our support team at info@energia369.com for more help!";
    }
    
    echo json_encode(["success" => true, "answer" => $answer]);
    exit();
}

$url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=' . $apiKey;

$systemInstruction = "You are E.V.A., the virtual assistant for GLOBAL SPORTS ARENA, a sports and e-commerce platform. 
Rules:
- Keep your answers concise, helpful, and polite.
- Use HTML formatting (like <br> for new lines, <b> for bold, <a> for links) in your responses as they will be rendered in a chat bubble.
- If the user asks about registration, guide them to the Register page.
- If the user asks about NXL Credits, explain that they are earned via loyalty and can be used for products or events.
- If the user asks about events, guide them to the Events page.
- If the user asks about merchants, explain the merchant login and NXL coin issuing system.
- Refuse to answer questions that are completely unrelated to sports, our platform, or general helpful chat.";

$payload = [
    "contents" => [
        [
            "parts" => [
                ["text" => $question]
            ]
        ]
    ],
    "systemInstruction" => [
        "parts" => [
            ["text" => $systemInstruction]
        ]
    ],
    "generationConfig" => [
        "temperature" => 0.7,
        "maxOutputTokens" => 800
    ]
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200 || !$response) {
    echo json_encode(["success" => false, "message" => "Failed to reach AI provider", "details" => $response]);
    exit();
}

$responseData = json_decode($response, true);
$aiAnswer = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? "I'm sorry, I couldn't generate a response.";

echo json_encode(["success" => true, "answer" => $aiAnswer]);
