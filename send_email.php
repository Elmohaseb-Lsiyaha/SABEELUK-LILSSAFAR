<?php
// تمكين عرض الأخطاء للتطوير
error_reporting(E_ALL);
ini_set('display_errors', 1);

// تمكين CORS للطلبات من أي مصدر (للتطوير المحلي)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json; charset=utf-8');

// معالجة طلب OPTIONS لـ CORS Preflight
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// السماح فقط بطلبات POST
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method Not Allowed: يسمح فقط بطريقة POST'
    ]);
    exit;
}

// استقبال البيانات من النموذج
$data = $_POST;

// التحقق من البيانات المطلوبة
$required_fields = ['name', 'email', 'phone', 'service', 'message'];
$missing_fields = [];
foreach ($required_fields as $field) {
    if (empty($data[$field])) {
        $missing_fields[] = $field;
    }
}

if (!empty($missing_fields)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'الحقول المطلوبة ناقصة: ' . implode(', ', $missing_fields)
    ]);
    exit;
}

// تنظيف البيانات
$name = htmlspecialchars(strip_tags($data['name']));
$email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
$phone = htmlspecialchars(strip_tags($data['phone']));
$service = htmlspecialchars(strip_tags($data['service']));
$message = htmlspecialchars(strip_tags($data['message']));

// إنشاء محتوى البريد
$to = "sabeeluk.lilssafar@gmail.com";
$subject = "طلب جديد: $service - من $name";
$headers = "From: $name <$email>\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8\r\n";

$email_content = "
<!DOCTYPE html>
<html dir='rtl'>
<head>
    <meta charset='UTF-8'>
    <title>$subject</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        h2 { color: #006494; }
        p { margin: 10px 0; }
        .footer { margin-top: 20px; font-size: 12px; color: #777; }
    </style>
</head>
<body>
    <div class='container'>
        <h2>طلب جديد من موقع سَبِيلُكَ لِلسَّفَر</h2>
        <p><strong>الاسم:</strong> $name</p>
        <p><strong>البريد الإلكتروني:</strong> $email</p>
        <p><strong>رقم الهاتف:</strong> $phone</p>
        <p><strong>الخدمة المطلوبة:</strong> $service</p>
        <p><strong>الرسالة:</strong></p>
        <p>$message</p>
        <div class='footer'>
            <p>هذه الرسالة تم إرسالها تلقائيًا من النموذج الخاص بالموقع</p>
        </div>
    </div>
</body>
</html>
";

// محاولة إرسال البريد
if (mail($to, $subject, $email_content, $headers)) {
    echo json_encode([
        'success' => true,
        'message' => 'تم إرسال رسالتك بنجاح! سنقوم بالرد عليك قريباً.'
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'حدث خطأ أثناء محاولة إرسال الرسالة. يرجى المحاولة لاحقاً.'
    ]);
}
?>