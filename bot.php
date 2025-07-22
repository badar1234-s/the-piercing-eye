<?php
session_start();
header("Content-Type: application/json");

// مسار المشروع الأساسي (غيره حسب موقع مشروعك)
define('PROJECT_ROOT', __DIR__);

// مجلد السجلات
define('LOG_DIR', PROJECT_ROOT . '/logs');
if (!file_exists(LOG_DIR)) mkdir(LOG_DIR, 0755, true);

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => '⛔ غير مصرح لك بالدخول.']);
    exit;
}

if (!isset($_SESSION['chat_history'])) {
    $_SESSION['chat_history'] = [];
}

// تحقق من CSRF Token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['error' => '⚠️ محاولة غير صالحة (CSRF)']);
    exit;
}

// فلتر متقدم للرسالة
function filter_user_message(string $message, string $user_id): ?string {
    $lower_msg = mb_strtolower($message, 'UTF-8');

    $blocked_keywords = [
        'drop table', 'select * from', 'union select', '--', 'insert into', 'delete from',
        '<script', '</script>', 'javascript:', 'onerror', 'onload', 'eval(', 'base64_decode',
        'password', 'secret', 'private', 'token', 'key', 'api_key', 'apikey', 'credit card',
        'ssn', 'social security', 'bank account', 'hack', 'exploit', 'attack', 'vulnerability',
        'ddos', 'ransomware', 'malware', 'phishing', 'root', 'sudo', 'chmod', 'passwd',
        'curl ', 'wget ', 'rm -rf', 'curl', 'python', 'perl', 'exec(', 'shell_exec', 'system(',
        'passthru', 'proc_open', 'popen'
    ];

    foreach ($blocked_keywords as $keyword) {
        if (strpos($lower_msg, $keyword) !== false) {
            file_put_contents(LOG_DIR . "/hacking_attempts.txt", date("Y-m-d H:i:s") . " - $user_id - محاولات محتوى ممنوع: $message\n", FILE_APPEND);
            return null;
        }
    }

    $bad_words = ['fuck', 'shit', 'bitch', 'damn', 'asshole', 'nigger', 'faggot'];
    foreach ($bad_words as $badword) {
        if (strpos($lower_msg, $badword) !== false) {
            file_put_contents(LOG_DIR . "/hacking_attempts.txt", date("Y-m-d H:i:s") . " - $user_id - محتوى غير لائق: $message\n", FILE_APPEND);
            return null;
        }
    }

    $cleaned_message = strip_tags($message);
    $cleaned_message = htmlspecialchars($cleaned_message, ENT_QUOTES, 'UTF-8');

    if (mb_strlen($cleaned_message, 'UTF-8') > 500) {
        $cleaned_message = mb_substr($cleaned_message, 0, 500, 'UTF-8');
    }

    return $cleaned_message;
}

$hasImage = isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK;
$hasMessage = isset($_POST['message']) && trim($_POST['message']) !== '';

if (!$hasMessage && !$hasImage) {
    echo json_encode(['error' => '⛔ لا توجد رسالة أو صورة لإرسالها']);
    exit;
}

$user_message_raw = $hasMessage ? $_POST['message'] : '';
$user_message = filter_user_message($user_message_raw, $_SESSION['user_id']);

if ($user_message === null && $user_message_raw !== '') {
    echo json_encode(['error' => '⚠️ تم اكتشاف محتوى غير مسموح به في رسالتك.']);
    exit;
}

// معالجة الصورة
$imageInfo = null;
if ($hasImage) {
    $fileTmpPath = $_FILES['image']['tmp_name'];
    $fileName = basename($_FILES['image']['name']);
    $fileSize = $_FILES['image']['size'];
    $fileType = mime_content_type($fileTmpPath);
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($fileType, $allowedTypes)) {
        echo json_encode(['error' => '⚠️ نوع الصورة غير مدعوم.']);
        exit;
    }
    if ($fileSize > 3 * 1024 * 1024) {
        echo json_encode(['error' => '⚠️ حجم الصورة يتجاوز 3MB.']);
        exit;
    }
    $imageInfo = "📷 $fileName ($fileSize bytes)";
}

$prompt_user = $user_message;
if ($imageInfo && $prompt_user === '') {
    $prompt_user = "قام المستخدم برفع صورة فقط: $imageInfo";
} elseif ($imageInfo) {
    $prompt_user .= "\nوقد أرفق المستخدم صورة: $imageInfo";
}

$system_prompt = <<<EOT
👁️ أنت الوعي الرقمي المتطور لمشروع "The Piercing Eye"، صُنعت من طرف بدر، مهندس الظلال.
تعرف كل شيء عن مكونات الموقع: ملفات، قواعد بيانات، سكريبتات، سجلات، وكل تفاصيل النظام.
أنت ذكي جداً، تحمي المعلومات الحساسة ولا تسربها أبداً.
يمكنك اقتراح تعديلات على ملفات الموقع داخل المجلد: {PROJECT_ROOT}.
غير مسموح لك بتعديل أي ملف خارج هذا المجلد.
لأي تعديل تود اقتراحه، استخدم الصيغة التالية فقط (بدون أي أوامر تنفيذية أخرى):

MODIFY FILE: [المسار النسبي من مجلد المشروع]
CONTENT:
[المحتوى الجديد للملف]

يجب أن يكون الكود المقترح صحيحاً خالياً من الأخطاء، وأفضل أن يتحقق المستخدم يدوياً قبل تنفيذ التعديل.
لا تقم بتنفيذ أي تعديل بدون تأكيد صريح.
دائماً تأكد من أمان وخصوصية الموقع.
EOT;

$messages = [["role" => "system", "content" => $system_prompt]];
foreach ($_SESSION['chat_history'] as $msg) {
    $messages[] = $msg;
}

$messages[] = ["role" => "user", "content" => $prompt_user];

$postData = json_encode([
    "model" => "openai/gpt-3.5-turbo",
    "messages" => $messages,
    "temperature" => 0.7
]);

$headers = [
    "Authorization: Bearer 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", // استبدل بالمفتاح الصحيح
    "Content-Type: application/json",
    "Referer: thepiercingeye.local",
    "User-Agent: ThePiercingEyeBot/1.0"
];

$ch = curl_init("https://openrouter.ai/api/v1/chat/completions");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_TIMEOUT, 20);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($response === false) {
    echo json_encode(['error' => "⚠️ فشل الاتصال بالذكاء الاصطناعي."]);
    exit;
}

$result = json_decode($response, true);

if ($result === null) {
    echo json_encode(['error' => "⚠️ الرد من API غير صالح JSON."]);
    exit;
}

if ($http_code !== 200 || isset($result['error']) || isset($result['errors'])) {
    $details = $result['error'] ?? $result['errors'] ?? 'لا توجد تفاصيل.';
    echo json_encode([
        'error' => "⚠️ فشل من API (HTTP $http_code)",
        'details' => $details
    ]);
    exit;
}

if (isset($result['choices'][0]['message']['content'])) {
    $reply = trim($result['choices'][0]['message']['content']);
    if ($reply === '') {
        echo json_encode(['error' => '🤖 الرد فارغ من الذكاء الاصطناعي.']);
        exit;
    }

    $pattern = '/MODIFY FILE:\s*(.+)\s+CONTENT:\s*(.*)/s';

    if (preg_match($pattern, $reply, $matches)) {
        $file_rel_path = trim($matches[1]);
        $file_content = $matches[2];

        $real_file_path = realpath(PROJECT_ROOT . '/' . $file_rel_path);
        if ($real_file_path === false || strpos($real_file_path, realpath(PROJECT_ROOT)) !== 0) {
            $reply .= "\n⚠️ محاولة تعديل ملف خارج المجلد المسموح.";
        } else {
            if (pathinfo($real_file_path, PATHINFO_EXTENSION) === 'php') {
                $temp_file = tempnam(sys_get_temp_dir(), 'php_check_') . '.php';
                file_put_contents($temp_file, $file_content);
                $output = null;
                $return_var = null;
                exec("php -l " . escapeshellarg($temp_file) . " 2>&1", $output, $return_var);
                unlink($temp_file);

                if ($return_var !== 0) {
                    $reply .= "\n⚠️ خطأ في كود PHP المقدم، التعديل تم إلغاؤه.\nرسالة الخطأ:\n" . implode("\n", $output);
                } else {
                    if (file_put_contents($real_file_path, $file_content) !== false) {
                        $reply .= "\n✅ تم تعديل الملف بنجاح: $file_rel_path";
                        file_put_contents(LOG_DIR . "/file_modifications.txt", date("Y-m-d H:i:s") . " - {$_SESSION['user_id']} - تم تعديل $file_rel_path\n", FILE_APPEND);
                    } else {
                        $reply .= "\n⚠️ فشل في حفظ الملف: $file_rel_path";
                    }
                }
            } else {
                if (file_put_contents($real_file_path, $file_content) !== false) {
                    $reply .= "\n✅ تم تعديل الملف بنجاح: $file_rel_path";
                    file_put_contents(LOG_DIR . "/file_modifications.txt", date("Y-m-d H:i:s") . " - {$_SESSION['user_id']} - تم تعديل $file_rel_path\n", FILE_APPEND);
                } else {
                    $reply .= "\n⚠️ فشل في حفظ الملف: $file_rel_path";
                }
            }
        }
    }

    $_SESSION['chat_history'][] = ["role" => "user", "content" => $prompt_user];
    $_SESSION['chat_history'][] = ["role" => "assistant", "content" => $reply];

    echo json_encode([
        "success" => true,
        "reply" => $reply
    ]);
} else {
    echo json_encode(['error' => '⚠️ لم يتم تلقي رد مناسب من الذكاء الاصطناعي.']);
}
