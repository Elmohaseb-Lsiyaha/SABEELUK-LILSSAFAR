<?php
header('Content-Type: text/html; charset=utf-8');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // جلب البيانات من النموذج
    $name = isset($_POST['name']) ? strip_tags(trim($_POST['name'])) : '';
    $email = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : '';
    $phone = isset($_POST['phone']) ? strip_tags(trim($_POST['phone'])) : '';
    $service = isset($_POST['service']) ? strip_tags(trim($_POST['service'])) : '';
    $message = isset($_POST['message']) ? strip_tags(trim($_POST['message'])) : '';
    
    // التحقق من صحة البيانات
    if (empty($name) || empty($email) || empty($phone) || empty($service) || empty($message)) {
        http_response_code(400);
        echo "<p style='text-align:center;font-family:Tajawal;direction:rtl;'>الرجاء ملء جميع الحقول المطلوبة</p>";
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo "<p style='text-align:center;font-family:Tajawal;direction:rtl;'>الرجاء إدخال بريد إلكتروني صحيح</p>";
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
        // إعادة التوجيه إلى صفحة الشكر عند النجاح
        header("Location: thank-you.html");
        exit;
    } else {
        http_response_code(500);
        echo "<div style='text-align:center;font-family:Tajawal;direction:rtl;margin-top:50px;'>
                <h2 style='color:#d32f2f;'>عذرًا، حدث خطأ أثناء إرسال رسالتك</h2>
                <p>يرجى المحاولة مرة أخرى لاحقًا أو التواصل عبر واتساب</p>
                <a href='https://wa.me/message/FIW5CFX2HH2HF1' style='background:#25D366;color:white;padding:10px 20px;border-radius:5px;text-decoration:none;margin-top:20px;display:inline-block;'>
                    التواصل عبر واتساب
                </a>
              </div>";
    }
} else {
    http_response_code(403);
    echo "<p style='text-align:center;font-family:Tajawal;direction:rtl;'>عفواً، طريقة الإرسال غير مسموح بها</p>";
}
?>