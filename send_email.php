<?php
header('Content-Type: application/json; charset=utf-8');

// السماح بطلبات CORS (لأغراض التطوير)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// معالجة طلبات OPTIONS لـ CORS
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // جلب البيانات من النموذج
    $name = isset($_POST['name']) ? strip_tags(trim($_POST['name'])) : '';
    $email = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : '';
    $phone = isset($_POST['phone']) ? strip_tags(trim($_POST['phone'])) : '';
    $service = isset($_POST['service']) ? strip_tags(trim($_POST['service'])) : '';
    $message = isset($_POST['message']) ? strip_tags(trim($_POST['message'])) : '';
    
    // مصفوفة للرد
    $response = [
        'success' => false,
        'message' => '',
        'errors' => []
    ];
    
    // التحقق من صحة البيانات
    if (empty($name)) {
        $response['errors']['name'] = 'حقل الاسم مطلوب';
    }
    
    if (empty($email)) {
        $response['errors']['email'] = 'حقل البريد الإلكتروني مطلوب';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['errors']['email'] = 'الرجاء إدخال بريد إلكتروني صحيح';
    }
    
    if (empty($phone)) {
        $response['errors']['phone'] = 'حقل الهاتف مطلوب';
    }
    
    if (empty($service)) {
        $response['errors']['service'] = 'حقل الخدمة مطلوب';
    }
    
    if (empty($message)) {
        $response['errors']['message'] = 'حقل الرسالة مطلوب';
    }
    
    // إذا كان هناك أخطاء
    if (!empty($response['errors'])) {
        http_response_code(400);
        $response['message'] = 'الرجاء تصحيح الأخطاء في النموذج';
        echo json_encode($response);
        exit;
    }
    
    // إعداد محتوى البريد الإلكتروني
    $to = "sabeeluk.lilssafar@gmail.com";
    $subject = "طلب جديد: $service - من $name";
    
    $email_content = "
    <html dir='rtl'>
    <head>
        <meta charset='UTF-8'>
        <title>$subject</title>
        <style>
            body { font-family: 'Tajawal', sans-serif; line-height: 1.6; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background-color: #006494; color: white; padding: 10px; text-align: center; }
            .content { padding: 20px; background-color: #f9f9f9; }
            .footer { text-align: center; padding: 10px; font-size: 12px; color: #777; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>طلب جديد من موقع سَبِيلُكَ لِلسَّفَر</h2>
            </div>
            <div class='content'>
                <p><strong>الاسم:</strong> $name</p>
                <p><strong>البريد الإلكتروني:</strong> $email</p>
                <p><strong>رقم الهاتف:</strong> $phone</p>
                <p><strong>الخدمة المطلوبة:</strong> $service</p>
                <p><strong>الرسالة:</strong></p>
                <p>$message</p>
            </div>
            <div class='footer'>
                <p>هذه الرسالة تم إرسالها تلقائيًا من موقع سَبِيلُكَ لِلسَّفَر</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // إعداد رأس البريد
    $headers = "From: $name <$email>\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    // إرسال البريد الإلكتروني
    if (mail($to, $subject, $email_content, $headers)) {
        http_response_code(200);
        $response['success'] = true;
        $response['message'] = 'تم إرسال رسالتك بنجاح! سنقوم بالرد عليك في أقرب وقت ممكن.';
    } else {
        http_response_code(500);
        $response['message'] = 'عذرًا، حدث خطأ أثناء إرسال رسالتك. يرجى المحاولة مرة أخرى لاحقًا أو التواصل عبر واتساب.';
    }
    
    echo json_encode($response);
    exit;
} else {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'طريقة الإرسال غير مسموح بها. يرجى استخدام طريقة POST.'
    ]);
    exit;
}
?>