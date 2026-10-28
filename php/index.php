<?php
// Minimal PHP page: a form that asks Ebtekar and shows the answer. PHP 8+ with the curl extension.
// Run:  php -S localhost:8000   (with EBTEKAR_API_KEY set in your environment)
declare(strict_types=1);

function ask_ebtekar(string $question): string
{
    $key = getenv('EBTEKAR_API_KEY') ?: throw new RuntimeException('Set EBTEKAR_API_KEY');
    $base = rtrim(getenv('EBTEKAR_BASE_URL') ?: 'https://ebtekarai.ir/api/developer-gateway', '/');

    $ch = curl_init($base . '/v1/messages');
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 60,
        CURLOPT_HTTPHEADER => [
            'x-api-key: ' . $key,
            'anthropic-version: 2023-06-01',
            'content-type: application/json',
            'idempotency-key: ' . bin2hex(random_bytes(16)),
        ],
        CURLOPT_POSTFIELDS => json_encode([
            'model' => getenv('EBTEKAR_MODEL') ?: 'ebtekar-smart',
            'max_tokens' => 1024,
            'messages' => [['role' => 'user', 'content' => $question]],
        ], JSON_UNESCAPED_UNICODE),
    ]);
    $body = (string) curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    curl_close($ch);

    $data = json_decode($body, true);
    if ($status !== 200) {
        throw new RuntimeException($data['error']['message'] ?? "Gateway error $status");
    }
    return implode('', array_map(fn ($b) => $b['type'] === 'text' ? $b['text'] : '', $data['content'] ?? []));
}

$question = trim((string) ($_POST['q'] ?? ''));
$answer = $error = '';
if ($question !== '') {
    try {
        $answer = ask_ebtekar(mb_substr($question, 0, 8000));
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}
$h = fn (string $s) => htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
?>
<!doctype html>
<html lang="fa" dir="rtl">
<head><meta charset="utf-8"><title>Ebtekar · PHP</title>
<style>body{font-family:Tahoma,sans-serif;max-width:720px;margin:40px auto;padding:0 16px;background:#0A0D1C;color:#F4F6FF}
textarea{width:100%;min-height:90px;font:inherit;padding:10px;border-radius:12px}button{margin-top:8px;padding:8px 18px;border-radius:10px}
.box{white-space:pre-wrap;background:#11152b;border-radius:14px;padding:14px;margin-top:18px;line-height:1.8}.err{color:#fca5a5}</style></head>
<body>
<h1>از ابتکار بپرس</h1>
<form method="post"><textarea name="q" required><?= $h($question) ?></textarea><button>ارسال</button></form>
<?php if ($answer): ?><div class="box"><?= $h($answer) ?></div><?php endif; ?>
<?php if ($error): ?><div class="box err"><?= $h($error) ?></div><?php endif; ?>
</body>
</html>
