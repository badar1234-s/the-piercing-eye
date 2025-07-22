<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// منع دخول الأدمن
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header("Location: admin.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="UTF-8" />
  <title>👁️ The Piercing Eye 👁️</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      padding: 0;
      background: 
        linear-gradient(rgba(0, 0, 0, 0.85), rgba(20, 0, 0, 0.9)),
        url('assets/img/image.jpg') no-repeat center center fixed;
      background-size: cover;
      font-family: 'Courier New', monospace;
      color: crimson;
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
      position: relative;
    }

    .bg-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: 
        repeating-linear-gradient(0deg, 
          transparent, 
          transparent 2px, 
          rgba(255, 0, 0, 0.1) 3px, 
          rgba(255, 0, 0, 0.1) 4px);
      z-index: -1;
      pointer-events: none;
    }

    .content {
      position: relative;
      z-index: 1;
      text-align: center;
      padding: 30px;
      border: 2px solid crimson;
      border-radius: 20px;
      box-shadow: 0 0 30px crimson, 0 0 60px rgba(220, 20, 60, 0.5);
      background-color: rgba(0, 0, 0, 0.7);
      max-width: 90%;
      animation: fadeIn 1.2s ease;
      backdrop-filter: blur(3px);
    }

    @keyframes fadeIn {
      0% { opacity: 0; transform: scale(0.95); }
      100% { opacity: 1; transform: scale(1); }
    }

    h1 {
      font-size: 2.5rem;
      text-shadow: 0 0 10px crimson, 0 0 20px crimson;
      margin-bottom: 10px;
      animation: pulse 2s infinite;
    }

    @keyframes pulse {
      0%, 100% {
        text-shadow: 0 0 10px crimson, 0 0 20px crimson;
      }
      50% {
        text-shadow: 0 0 20px crimson, 0 0 30px crimson;
      }
    }

    p {
      margin: 15px 0;
      font-size: 1.1rem;
      color: #ffcccc;
    }

    a.button {
      display: block;
      margin: 15px auto;
      padding: 14px 28px;
      border: 2px solid crimson;
      color: crimson;
      text-decoration: none;
      font-weight: bold;
      font-size: 1.1rem;
      border-radius: 12px;
      background: transparent;
      transition: all 0.3s ease;
      width: fit-content;
    }

    a.button:hover {
      background: crimson;
      color: black;
      box-shadow: 0 0 10px crimson, 0 0 20px crimson;
      transform: scale(1.05);
    }

    audio {
      display: none;
    }

    @media (max-width: 600px) {
      h1 {
        font-size: 2rem;
      }
      a.button {
        font-size: 1rem;
        padding: 12px 20px;
      }
    }
  </style>
</head>
<body>

  <audio autoplay loop>
    <source src="assets/sound1.mp3" type="audio/mpeg" />
  </audio>

  <div class="bg-overlay"></div>
  
  <div class="content">
    <h1>👁️ You have been chosen.</h1>
    <p>The Eye sees through the lies...</p>
    <a href="editor.php" class="button">📝 Open the Editor</a>
    <a href="chat.php" class="button">💬 Talk to the AI</a>
    <a href="game.php" class="button">🎮 Enter the Game</a>
    <a href="logout.php" class="button">🚪 Exit</a>
  </div>

</body>
</html>
