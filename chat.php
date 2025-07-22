<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: index.php");
  exit;
}

if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="<?= $_SESSION['csrf_token'] ?>">
  <title>🧠 AI Chat - The Piercing Mind</title>
  <style>
    html, body { margin: 0; padding: 0; height: 100%; width: 100%; background-color: #000; font-family: monospace; color: white; overflow: hidden; }
    #chat-box { display: flex; flex-direction: column; height: 100vh; width: 100vw; background: #0a0a0a; padding: 15px 20px; box-sizing: border-box; }
    #chat-header { text-align: center; color: crimson; font-size: 1.8rem; font-weight: bold; margin-bottom: 12px; user-select: none; }
    #chat-messages { flex: 1; background: #111; border-radius: 10px; padding: 15px; overflow-y: auto; font-size: 1.1rem; line-height: 1.4; box-shadow: 0 0 15px crimson; }
    #chat-messages::-webkit-scrollbar { width: 8px; }
    #chat-messages::-webkit-scrollbar-thumb { background-color: crimson; border-radius: 10px; }
    .message { max-width: 70%; margin: 8px 0; padding: 10px 14px; border-radius: 16px; word-wrap: break-word; white-space: pre-wrap; box-shadow: 0 0 5px #222; }
    .user { background-color: #004d4d; color: #00ffcc; align-self: flex-end; text-align: right; }
    .bot { background-color: #4d0000; color: #ff4d4d; align-self: flex-start; text-align: left; }
    .message img { max-width: 100%; border-radius: 8px; margin-top: 8px; }
    #chat-input-area { display: flex; align-items: center; justify-content: space-between; gap: 10px; flex-wrap: wrap; margin-top: 12px; }
    #chat-input { flex: 1; font-size: 1.1rem; padding: 12px 15px; border-radius: 12px; border: 1px solid crimson; background-color: #000; color: white; outline: none; min-width: 200px; }
    #chat-input::placeholder { color: #666; }
    #send-btn { background: crimson; color: white; font-weight: bold; border: none; padding: 12px 22px; border-radius: 12px; cursor: pointer; user-select: none; transition: background-color 0.3s; }
    #send-btn:hover { background: darkred; }
    #image-upload { display: none; }
    label[for="image-upload"] { cursor: pointer; font-size: 1.7rem; color: crimson; user-select: none; }
    label[for="image-upload"]:hover { color: darkred; }
    #clear-btn { background: #333; color: #ccc; border: none; padding: 10px 16px; border-radius: 12px; cursor: pointer; font-size: 0.9rem; user-select: none; transition: background-color 0.3s; }
    #clear-btn:hover { background: #555; }
    a.back-link { text-align: center; margin-top: 15px; color: crimson; text-decoration: none; font-weight: bold; user-select: none; }
    a.back-link:hover { text-decoration: underline; }
  </style>
</head>
<body>
  <div id="chat-box">
    <div id="chat-header">🤖 دردشة The Piercing Mind</div>
    <div id="chat-messages"></div>
    <div id="chat-input-area">
      <input type="text" id="chat-input" placeholder="اكتب رسالتك هنا..." onkeydown="if(event.key === 'Enter') sendMessage()" autocomplete="off" />
      <label for="image-upload" title="رفع صورة">📷</label>
      <input type="file" id="image-upload" accept="image/png, image/jpeg, image/webp" />
      <button id="send-btn" onclick="sendMessage()">إرسال</button>
      <button id="clear-btn" onclick="clearChat()">مسح المحادثة</button>
    </div>
    <a href="secret.php" class="back-link">⬅️ العودة إلى العين</a> | <a href="logout.php" class="back-link">🚪 تسجيل الخروج</a>
  </div>
  <script>
    function appendMessage(content, sender, isImage = false) {
      const chat = document.getElementById("chat-messages");
      const div = document.createElement("div");
      div.className = "message " + sender;
      const time = new Date().toLocaleTimeString("ar-MA", { hour: '2-digit', minute: '2-digit' });
      const timeTag = `<span style="font-size: 0.8rem; color: gray;">[${time}]</span> `;
      if (isImage) {
        const label = document.createElement("span");
        label.innerHTML = timeTag + (sender === "user" ? "👤: " : "🤖: ");
        div.appendChild(label);
        const img = document.createElement("img");
        img.src = content;
        div.appendChild(img);
      } else {
        div.innerHTML = timeTag + (sender === "user" ? "👤: " : "🤖: ") + content;
      }
      chat.appendChild(div);
      chat.scrollTop = chat.scrollHeight;
      saveChat();
    }

    function sendMessage() {
      const input = document.getElementById("chat-input");
      const imgInput = document.getElementById("image-upload");
      const msg = input.value.trim();
      if (msg.length > 500) {
        appendMessage("⚠️ الرسالة طويلة جدًا.", "bot");
        return;
      }
      if (msg === "" && imgInput.files.length === 0) return;
      if (msg !== "" && imgInput.files.length > 0) {
        appendMessage("⚠️ لا يمكنك إرسال رسالة وصورة معًا.", "bot");
        return;
      }

      if (msg !== "") {
        appendMessage(msg, "user");
      } else {
        appendMessage("📤 جارٍ إرسال الصورة...", "user");
      }

      const formData = new FormData();
      formData.append("message", msg);
      formData.append("csrf_token", document.querySelector('meta[name="csrf-token"]').content);
      if (imgInput.files.length > 0) {
        if (imgInput.files[0].size > 3 * 1024 * 1024) {
          appendMessage("⚠️ حجم الصورة كبير جدًا. الحد الأقصى 3MB.", "bot");
          return;
        }
        formData.append("image", imgInput.files[0]);
      }

      fetch("bot.php", {
        method: "POST",
        body: formData,
        credentials: "include"
      })
      .then(res => res.json())
      .then(data => {
        if (data.error) {
          appendMessage("⚠️ " + data.error, "bot");
        } else if (data.reply) {
          if (data.is_image) {
            appendMessage(data.reply, "bot", true);
          } else {
            appendMessage(data.reply, "bot");
          }
        } else {
          appendMessage("⚠️ رد غير متوقع من السيرفر.", "bot");
        }
        input.value = "";
        imgInput.value = "";
      })
      .catch(() => appendMessage("⚠️ حدث خطأ أثناء الاتصال بالسيرفر.", "bot"));
    }

    function clearChat() {
      if(confirm("هل أنت متأكد من مسح المحادثة؟")) {
        document.getElementById("chat-messages").innerHTML = "";
        localStorage.removeItem("chatHistory");
      }
    }

    function saveChat() {
      localStorage.setItem("chatHistory", document.getElementById("chat-messages").innerHTML);
    }

    function loadChat() {
      const saved = localStorage.getItem("chatHistory");
      if (saved) {
        document.getElementById("chat-messages").innerHTML = saved;
      }
    }

    window.onload = loadChat;
  </script>
</body>
</html>
