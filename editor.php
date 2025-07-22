<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$upload_dir = "image_save";
$data_file = $upload_dir . "/data.json";

if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}
if (!file_exists($data_file)) {
    file_put_contents($data_file, json_encode([]));
}

$images = json_decode(file_get_contents($data_file), true);

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["action"])) {
    if ($_POST["action"] === "upload" && isset($_FILES["image"])) {
        $allowed = ["jpg", "jpeg", "png", "gif"];

        $file_names = $_FILES["image"]["name"];
        $file_tmp_names = $_FILES["image"]["tmp_name"];
        $file_errors = $_FILES["image"]["error"];

        if (!is_array($file_names)) {
            $file_names = [$file_names];
            $file_tmp_names = [$file_tmp_names];
            $file_errors = [$file_errors];
        }

        $captions = $_POST["caption"] ?? [];
        if (!is_array($captions)) {
            $captions = [$captions];
        }

        $upload_count = 0;
        for ($i = 0; $i < count($file_names); $i++) {
            if ($file_errors[$i] !== 0) continue;
            $ext = strtolower(pathinfo($file_names[$i], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed)) continue;

            $new_name = uniqid("img_", true) . "." . $ext;
            $target = $upload_dir . "/" . $new_name;

            if (move_uploaded_file($file_tmp_names[$i], $target)) {
                $caption_for_image = trim($captions[$i] ?? "");
                $images[] = ["file" => $new_name, "caption" => $caption_for_image];
                $upload_count++;
            }
        }

        if ($upload_count > 0) {
            file_put_contents($data_file, json_encode($images, JSON_PRETTY_PRINT));
            $message = "✅ Uploaded $upload_count image(s) successfully!";
        } else {
            $message = "⚠️ No valid images uploaded.";
        }
    }

    if ($_POST["action"] === "delete" && isset($_POST["file"])) {
        $file_to_delete = basename($_POST["file"]);
        foreach ($images as $key => $img) {
            if ($img["file"] === $file_to_delete) {
                $path = $upload_dir . "/" . $file_to_delete;
                if (file_exists($path)) unlink($path);
                unset($images[$key]);
                $images = array_values($images);
                file_put_contents($data_file, json_encode($images, JSON_PRETTY_PRINT));
                $message = "🗑️ Image deleted.";
                break;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>🖼️ The Eye's Gallery</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    /* نفس التنسيق السابق */
    * { box-sizing: border-box; }
    body {
      margin: 0; padding: 0;
      background: radial-gradient(circle, #000000, #0a0a0a);
      font-family: 'Courier New', monospace;
      color: crimson;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: flex-start;
      padding: 40px 10px;
    }
    .container {
      width: 100%;
      max-width: 900px;
      background: rgba(0, 0, 0, 0.9);
      padding: 30px;
      border-radius: 20px;
      box-shadow: 0 0 25px crimson;
      animation: fadeIn 0.8s ease;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: scale(0.95); }
      to { opacity: 1; transform: scale(1); }
    }
    h1, h2 {
      text-align: center;
      color: crimson;
      text-shadow: 0 0 10px crimson;
      margin-bottom: 20px;
    }
    form.upload-form {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }
    .file-label {
      padding: 14px;
      background: #222;
      border: 1px solid crimson;
      border-radius: 10px;
      color: crimson;
      font-weight: bold;
      text-align: center;
      cursor: pointer;
      transition: background 0.3s;
    }
    .file-label:hover {
      background: crimson;
      color: black;
      box-shadow: 0 0 8px crimson;
    }
    .file-label input[type="file"] {
      display: none;
    }
    #file-name {
      text-align: center;
      font-size: 0.9rem;
      color: lightgray;
      margin-top: -10px;
      word-wrap: break-word;
    }
    .caption-input {
      padding: 10px;
      background: #111;
      border: 1px solid crimson;
      border-radius: 8px;
      color: white;
      font-size: 1rem;
      width: 100%;
      box-sizing: border-box;
    }
    button {
      padding: 14px;
      background: crimson;
      border: none;
      border-radius: 12px;
      font-size: 1.1rem;
      color: white;
      font-weight: bold;
      cursor: pointer;
      transition: 0.3s;
    }
    button:hover {
      background: darkred;
    }
    .message {
      text-align: center;
      color: lime;
      margin: 10px 0;
      font-weight: bold;
    }
    .gallery {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap: 20px;
      margin-top: 30px;
    }
    .item {
      background: #111;
      padding: 15px;
      border-radius: 12px;
      border: 1px solid crimson;
      box-shadow: 0 0 10px #88000040;
      display: flex;
      flex-direction: column;
      align-items: center;
    }
    .item img {
      max-width: 100%;
      border-radius: 10px;
      box-shadow: 0 0 10px crimson;
    }
    .caption {
      margin-top: 12px;
      color: #ff8080;
      font-style: italic;
      text-align: center;
      min-height: 40px;
      width: 100%;
      word-wrap: break-word;
    }
    .delete-form {
      margin-top: 12px;
      width: 100%;
    }
    .delete-button {
      background: darkred;
      color: white;
      border: none;
      padding: 10px;
      border-radius: 10px;
      width: 100%;
      cursor: pointer;
      font-weight: bold;
      transition: 0.3s;
    }
    .delete-button:hover {
      background: #a30000;
    }
    .back-link {
      text-align: center;
      margin-top: 40px;
    }
    .back-link a {
      color: crimson;
      text-decoration: none;
      font-weight: bold;
      font-size: 1.1rem;
    }
    .back-link a:hover {
      text-shadow: 0 0 10px crimson;
    }
  </style>
  <script>
    // لما تختار الصور يظهر أسماءهم
    function updateFileName(input) {
      let names = Array.from(input.files).map(f => f.name).join(', ');
      document.getElementById('file-name').textContent = names;
      generateCaptionInputs(input.files.length);
    }
    // توليد حقول التعليق بعد اختيار الصور
    function generateCaptionInputs(count) {
      const container = document.getElementById('captions-container');
      container.innerHTML = '';
      for (let i = 0; i < count; i++) {
        const label = document.createElement('label');
        label.textContent = `✍️ تعليق الصورة #${i + 1}:`;
        label.style.color = 'crimson';
        label.style.marginTop = '10px';
        const input = document.createElement('input');
        input.type = 'text';
        input.name = 'caption[]';
        input.className = 'caption-input';
        input.placeholder = 'اكتب تعليق للصورة';
        input.maxLength = 200;
        label.appendChild(input);
        container.appendChild(label);
      }
    }
  </script>
</head>
<body>
  <div class="container">
    <h1>📁 رفع صور مع تعليقات منفصلة</h1>
    <?php if ($message !== ""): ?>
      <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form class="upload-form" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="action" value="upload" />
      <label class="file-label">
        📂 اختر صور (يمكنك اختيار أكثر من 100 صورة)
        <input type="file" name="image[]" multiple required onchange="updateFileName(this)" />
      </label>
      <div id="file-name"></div>

      <div id="captions-container"></div>

      <button type="submit">📤 رفع الصور</button>
    </form>

    <h2>🖼️ المعرض</h2>
    <div class="gallery">
      <?php foreach ($images as $img): ?>
        <div class="item">
          <img src="<?= htmlspecialchars($upload_dir . "/" . $img["file"]) ?>" alt="Image" />
          <div class="caption"><?= htmlspecialchars($img["caption"]) ?></div>
          <form class="delete-form" method="POST" onsubmit="return confirm('هل تريد حذف هذه الصورة؟');">
            <input type="hidden" name="action" value="delete" />
            <input type="hidden" name="file" value="<?= htmlspecialchars($img["file"]) ?>" />
            <button class="delete-button" type="submit">🗑️ حذف الصورة</button>
          </form>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="back-link">
      <a href="secret.php">⬅️ العودة إلى الصفحة السرية</a>
    </div>
  </div>
</body>
</html>
