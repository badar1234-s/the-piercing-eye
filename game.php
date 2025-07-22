<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>🐍 أفعى الإعدادات والمتجر</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            touch-action: manipulation;
            user-select: none;
        }
        
        body {
            background: #000;
            color: #0f0;
            font-family: 'Arial', sans-serif;
            height: 100vh;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        #gameContainer {
            position: relative;
            width: 100%;
            height: 100%;
            max-width: 100%;
            max-height: 100%;
        }
        
        #gameCanvas {
            display: block;
            width: 100%;
            height: 100%;
            background: #001100;
        }
        
        #uiContainer {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }
        
        #scoreDisplay {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 1.8rem;
            color: #0f0;
            text-shadow: 0 0 10px #0f0;
            z-index: 10;
            background: rgba(0, 20, 0, 0.5);
            padding: 5px 15px;
            border-radius: 20px;
        }
        
        #startScreen {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 10, 0, 0.95);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 20;
        }
        
        #startTitle {
            font-size: 3rem;
            color: #0f0;
            text-shadow: 0 0 15px #0f0;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .menu-btn {
            background: #00aa00;
            color: white;
            border: none;
            padding: 12px 25px;
            font-size: 1.2rem;
            border-radius: 8px;
            margin: 8px 0;
            width: 80%;
            max-width: 250px;
            pointer-events: auto;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .menu-btn:active {
            transform: scale(0.95);
            box-shadow: 0 0 15px #00ff00;
        }
        
        #gameOverScreen {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.85);
            display: none;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 15;
            color: #0f0;
        }
        
        #finalScore {
            font-size: 2rem;
            margin-bottom: 20px;
            text-shadow: 0 0 10px #0f0;
        }
        
        #settingsScreen, #shopScreen {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 20, 0, 0.95);
            display: none;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 25;
            padding: 20px;
        }
        
        .settings-title {
            font-size: 2rem;
            color: #0f0;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .setting-option {
            width: 100%;
            max-width: 300px;
            margin: 10px 0;
        }
        
        .setting-option label {
            display: block;
            margin-bottom: 5px;
            color: #0f0;
        }
        
        .setting-option select, .setting-option input {
            width: 100%;
            padding: 8px;
            background: #002200;
            border: 1px solid #0f0;
            color: #0f0;
            border-radius: 5px;
        }
        
        .back-btn {
            background: #005500;
            color: white;
            border: none;
            padding: 10px 20px;
            margin-top: 20px;
            border-radius: 5px;
            cursor: pointer;
        }
        
        .shop-item {
            border: 1px solid #0f0;
            padding: 15px;
            margin: 10px 0;
            border-radius: 8px;
            width: 100%;
            max-width: 300px;
        }
        
        .shop-item h3 {
            color: #0f0;
            margin-bottom: 10px;
        }
        
        .shop-item p {
            color: #aaa;
            margin-bottom: 10px;
        }
        
        .buy-btn {
            background: #ffcc00;
            color: #000;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
        }
        
        #touchControls {
            position: absolute;
            bottom: 20px;
            left: 0;
            width: 100%;
            display: flex;
            justify-content: center;
            gap: 15px;
            z-index: 10;
            pointer-events: none;
        }
        
        .control-btn {
            width: 50px;
            height: 50px;
            background: rgba(0, 255, 0, 0.3);
            border: 2px solid #0f0;
            border-radius: 50%;
            color: white;
            font-size: 1.2rem;
            display: flex;
            justify-content: center;
            align-items: center;
            pointer-events: auto;
            cursor: pointer;
        }
        
        .control-btn:active {
            background: rgba(0, 255, 0, 0.5);
        }
    </style>
</head>
<body>
    <div id="gameContainer">
        <canvas id="gameCanvas"></canvas>
        
        <div id="uiContainer">
            <div id="scoreDisplay">0</div>
            
            <div id="touchControls">
                <div class="control-btn" id="upBtn">↑</div>
                <div class="control-btn" id="leftBtn">←</div>
                <div class="control-btn" id="rightBtn">→</div>
                <div class="control-btn" id="downBtn">↓</div>
            </div>
        </div>
        
        <div id="startScreen">
            <h1 id="startTitle">🐍 أفعى الإعدادات والمتجر</h1>
            <button id="soloBtn" class="menu-btn">لعب فردي</button>
            <button id="settingsBtn" class="menu-btn">الإعدادات</button>
            <button id="shopBtn" class="menu-btn">المتجر</button>
        </div>
        
        <div id="gameOverScreen">
            <h2>انتهت اللعبة!</h2>
            <div id="finalScore">النقاط: 0</div>
            <button id="restartBtn" class="menu-btn">إعادة المحاولة</button>
            <button id="mainMenuBtn" class="menu-btn">القائمة الرئيسية</button>
        </div>
        
        <div id="settingsScreen">
            <h2 class="settings-title">إعدادات اللعبة</h2>
            
            <div class="setting-option">
                <label for="snakeColor">لون الأفعى:</label>
                <select id="snakeColor">
                    <option value="#00ff00">أخضر</option>
                    <option value="#ff0000">أحمر</option>
                    <option value="#0000ff">أزرق</option>
                    <option value="#ffff00">أصفر</option>
                </select>
            </div>
            
            <div class="setting-option">
                <label for="snakeSpeed">سرعة اللعبة:</label>
                <select id="snakeSpeed">
                    <option value="150">عادي</option>
                    <option value="120">سريع</option>
                    <option value="180">بطيء</option>
                </select>
            </div>
            
            <div class="setting-option">
                <label for="controlType">نوع التحكم:</label>
                <select id="controlType">
                    <option value="touch">اللمس</option>
                    <option value="buttons">أزرار</option>
                </select>
            </div>
            
            <button id="saveSettings" class="back-btn">حفظ الإعدادات</button>
            <button id="settingsBack" class="back-btn">رجوع</button>
        </div>
        
        <div id="shopScreen">
            <h2 class="settings-title">متجر الأفعى</h2>
            
            <div class="shop-item">
                <h3>مظهر الأفعى الذهبية</h3>
                <p>أفعى بلون ذهبي مميز مع تأثيرات خاصة</p>
                <button class="buy-btn">شراء (100 نقطة)</button>
            </div>
            
            <div class="shop-item">
                <h3>خلفية الفضاء</h3>
                <p>خريطة فضائية مع نجوم متلألئة</p>
                <button class="buy-btn">شراء (150 نقطة)</button>
            </div>
            
            <div class="shop-item">
                <h3>طعام خاص</h3>
                <p>يظهر طعام خاص يعطي نقاط أكثر</p>
                <button class="buy-btn">شراء (200 نقطة)</button>
            </div>
            
            <button id="shopBack" class="back-btn">رجوع</button>
        </div>
    </div>

    <script>
        // ===== العناصر الأساسية =====
        const canvas = document.getElementById('gameCanvas');
        const ctx = canvas.getContext('2d');
        const scoreDisplay = document.getElementById('scoreDisplay');
        const startScreen = document.getElementById('startScreen');
        const soloBtn = document.getElementById('soloBtn');
        const settingsBtn = document.getElementById('settingsBtn');
        const shopBtn = document.getElementById('shopBtn');
        const gameOverScreen = document.getElementById('gameOverScreen');
        const finalScore = document.getElementById('finalScore');
        const restartBtn = document.getElementById('restartBtn');
        const mainMenuBtn = document.getElementById('mainMenuBtn');
        const settingsScreen = document.getElementById('settingsScreen');
        const shopScreen = document.getElementById('shopScreen');
        const saveSettings = document.getElementById('saveSettings');
        const settingsBack = document.getElementById('settingsBack');
        const shopBack = document.getElementById('shopBack');
        const snakeColorSelect = document.getElementById('snakeColor');
        const snakeSpeedSelect = document.getElementById('snakeSpeed');
        const controlTypeSelect = document.getElementById('controlType');
        const upBtn = document.getElementById('upBtn');
        const leftBtn = document.getElementById('leftBtn');
        const rightBtn = document.getElementById('rightBtn');
        const downBtn = document.getElementById('downBtn');
        
        // ===== إعدادات اللعبة =====
        let canvasWidth, canvasHeight;
        let gridSize = 20;
        let tileCountX, tileCountY;
        let snake = [];
        let direction = 'right';
        let nextDirection = 'right';
        let food = {};
        let score = 0;
        let gameSpeed = 150;
        let lastUpdateTime = 0;
        let lag = 0;
        let isGameRunning = false;
        let animationFrameId;
        
        // الإعدادات الحالية
        let currentSettings = {
            snakeColor: '#00ff00',
            snakeSpeed: 150,
            controlType: 'touch'
        };
        
        // العناصر المشتراة من المتجر
        let purchasedItems = {
            goldenSkin: false,
            spaceBg: false,
            specialFood: false
        };
        
        // ===== تهيئة اللعبة =====
        function initGame() {
            resizeCanvas();
            
            // إعداد الأفعى
            const startX = Math.floor(tileCountX / 3);
            const startY = Math.floor(tileCountY / 2);
            
            snake = [
                {x: startX, y: startY},
                {x: startX - 1, y: startY},
                {x: startX - 2, y: startY}
            ];
            
            direction = 'right';
            nextDirection = 'right';
            score = 0;
            gameSpeed = currentSettings.snakeSpeed;
            lastUpdateTime = 0;
            lag = 0;
            
            spawnFood();
            updateScore();
            
            // إظهار/إخفاء أزرار التحكم حسب الإعدادات
            if (currentSettings.controlType === 'buttons') {
                document.getElementById('touchControls').style.display = 'flex';
            } else {
                document.getElementById('touchControls').style.display = 'none';
            }
            
            startScreen.style.display = 'none';
            settingsScreen.style.display = 'none';
            shopScreen.style.display = 'none';
            gameOverScreen.style.display = 'none';
            isGameRunning = true;
            
            if (animationFrameId) {
                cancelAnimationFrame(animationFrameId);
            }
            animationFrameId = requestAnimationFrame(gameLoop);
        }
        
        function resizeCanvas() {
            canvasWidth = window.innerWidth;
            canvasHeight = window.innerHeight;
            
            canvas.width = canvasWidth;
            canvas.height = canvasHeight;
            
            gridSize = Math.max(20, Math.min(30, Math.floor(Math.min(canvasWidth, canvasHeight) / 25)));
            
            tileCountX = Math.floor(canvasWidth / gridSize);
            tileCountY = Math.floor(canvasHeight / gridSize);
        }
        
        function spawnFood() {
            food = {
                x: Math.floor(Math.random() * tileCountX),
                y: Math.floor(Math.random() * tileCountY),
                type: purchasedItems.specialFood && Math.random() < 0.2 ? 'special' : 'normal'
            };
            
            // التأكد من عدم ظهور الطعام على الأفعى
            while (snake.some(segment => segment.x === food.x && segment.y === food.y)) {
                food.x = Math.floor(Math.random() * tileCountX);
                food.y = Math.floor(Math.random() * tileCountY);
            }
        }
        
        function updateScore() {
            scoreDisplay.textContent = score;
        }
        
        // ===== رسم اللعبة =====
        function drawGame() {
            // الخلفية
            ctx.fillStyle = purchasedItems.spaceBg ? '#000033' : '#001100';
            
            // إذا كانت خلفية الفضاء مشتراة، نرسم النجوم
            if (purchasedItems.spaceBg) {
                ctx.fillRect(0, 0, canvasWidth, canvasHeight);
                ctx.fillStyle = '#ffffff';
                for (let i = 0; i < 100; i++) {
                    const x = Math.random() * canvasWidth;
                    const y = Math.random() * canvasHeight;
                    const size = Math.random() * 2;
                    ctx.beginPath();
                    ctx.arc(x, y, size, 0, Math.PI * 2);
                    ctx.fill();
                }
            } else {
                ctx.fillRect(0, 0, canvasWidth, canvasHeight);
            }
            
            // الطعام
            if (food.type === 'special') {
                ctx.fillStyle = '#ffff00';
                ctx.shadowColor = '#ffff00';
                ctx.shadowBlur = 10;
            } else {
                ctx.fillStyle = '#ff0000';
                ctx.shadowBlur = 0;
            }
            
            ctx.beginPath();
            ctx.arc(
                food.x * gridSize + gridSize / 2,
                food.y * gridSize + gridSize / 2,
                gridSize / 2 - 2,
                0,
                Math.PI * 2
            );
            ctx.fill();
            ctx.shadowBlur = 0;
            
            // الأفعى
            snake.forEach((segment, index) => {
                const isHead = index === 0;
                
                // إذا كان المظهر الذهبي مشترى والرأس
                if (purchasedItems.goldenSkin && isHead) {
                    ctx.fillStyle = '#ffcc00';
                    ctx.shadowColor = '#ffcc00';
                    ctx.shadowBlur = 10;
                } 
                // إذا كان المظهر الذهبي مشترى والجسم
                else if (purchasedItems.goldenSkin) {
                    ctx.fillStyle = '#ffaa00';
                }
                // إذا كانت الرأس (بدون مظهر ذهبي)
                else if (isHead) {
                    ctx.fillStyle = currentSettings.snakeColor;
                }
                // الجسم العادي
                else {
                    ctx.fillStyle = '#009900';
                }
                
                ctx.beginPath();
                ctx.roundRect(
                    segment.x * gridSize,
                    segment.y * gridSize,
                    gridSize,
                    gridSize,
                    [isHead ? 8 : 5]
                );
                ctx.fill();
                ctx.shadowBlur = 0;
                
                // عيون الرأس
                if (isHead) {
                    const eyeSize = gridSize / 5;
                    const eyeOffsetX = gridSize / 3;
                    const eyeOffsetY = gridSize / 4;
                    
                    ctx.fillStyle = 'white';
                    if (direction === 'right') {
                        ctx.beginPath();
                        ctx.arc(
                            segment.x * gridSize + gridSize - eyeOffsetX,
                            segment.y * gridSize + eyeOffsetY,
                            eyeSize, 0, Math.PI * 2
                        );
                        ctx.arc(
                            segment.x * gridSize + gridSize - eyeOffsetX,
                            segment.y * gridSize + gridSize - eyeOffsetY,
                            eyeSize, 0, Math.PI * 2
                        );
                        ctx.fill();
                        
                        // بؤبؤ العين
                        ctx.fillStyle = 'black';
                        ctx.beginPath();
                        ctx.arc(
                            segment.x * gridSize + gridSize - eyeOffsetX + 2,
                            segment.y * gridSize + eyeOffsetY,
                            eyeSize / 2, 0, Math.PI * 2
                        );
                        ctx.arc(
                            segment.x * gridSize + gridSize - eyeOffsetX + 2,
                            segment.y * gridSize + gridSize - eyeOffsetY,
                            eyeSize / 2, 0, Math.PI * 2
                        );
                        ctx.fill();
                    }
                    // ... (رسم العيون للاتجاهات الأخرى)
                }
            });
        }
        
        // ===== حركة اللعبة =====
        function gameLoop(timestamp) {
            if (!lastUpdateTime) {
                lastUpdateTime = timestamp;
            }
            
            const deltaTime = timestamp - lastUpdateTime;
            lastUpdateTime = timestamp;
            lag += deltaTime;
            
            // تحديث اللعبة بسرعة ثابتة
            while (lag >= gameSpeed) {
                updateGame();
                lag -= gameSpeed;
            }
            
            drawGame();
            
            if (isGameRunning) {
                animationFrameId = requestAnimationFrame(gameLoop);
            }
        }
        
        function updateGame() {
            const head = {...snake[0]};
            
            // تحديث الاتجاه
            direction = nextDirection;
            
            // حركة الرأس حسب الاتجاه
            switch (direction) {
                case 'up': head.y--; break;
                case 'down': head.y++; break;
                case 'left': head.x--; break;
                case 'right': head.x++; break;
            }
            
            // الاصطدام بالجدران
            if (head.x < 0 || head.x >= tileCountX || head.y < 0 || head.y >= tileCountY) {
                gameOver();
                return;
            }
            
            // الاصطدام بالنفس
            if (snake.some((segment, index) => index > 0 && segment.x === head.x && segment.y === head.y)) {
                gameOver();
                return;
            }
            
            snake.unshift(head);
            
            // الأكل
            if (head.x === food.x && head.y === food.y) {
                score += food.type === 'special' ? 30 : 10;
                updateScore();
                
                // إذا كان الطعام خاصاً، نزيد الطول أكثر
                if (food.type === 'special') {
                    snake.push({...snake[snake.length - 1]});
                    snake.push({...snake[snake.length - 1]});
                } else {
                    snake.push({...snake[snake.length - 1]});
                }
                
                spawnFood();
            } else {
                snake.pop();
            }
        }
        
        function gameOver() {
            isGameRunning = false;
            finalScore.textContent = `النقاط: ${score}`;
            gameOverScreen.style.display = 'flex';
        }
        
        // ===== التحكم =====
        function handleDirectionChange(newDirection) {
            if (
                (direction === 'up' && newDirection !== 'down') ||
                (direction === 'down' && newDirection !== 'up') ||
                (direction === 'left' && newDirection !== 'right') ||
                (direction === 'right' && newDirection !== 'left')
            ) {
                nextDirection = newDirection;
            }
        }
        
        // ===== الأحداث =====
        window.addEventListener('resize', () => {
            resizeCanvas();
        });
        
        soloBtn.addEventListener('click', () => {
            initGame();
        });
        
        settingsBtn.addEventListener('click', () => {
            // تحميل الإعدادات الحالية عند فتح الشاشة
            snakeColorSelect.value = currentSettings.snakeColor;
            snakeSpeedSelect.value = currentSettings.snakeSpeed;
            controlTypeSelect.value = currentSettings.controlType;
            
            startScreen.style.display = 'none';
            settingsScreen.style.display = 'flex';
        });
        
        shopBtn.addEventListener('click', () => {
            startScreen.style.display = 'none';
            shopScreen.style.display = 'flex';
        });
        
        saveSettings.addEventListener('click', () => {
            // حفظ الإعدادات الجديدة
            currentSettings = {
                snakeColor: snakeColorSelect.value,
                snakeSpeed: parseInt(snakeSpeedSelect.value),
                controlType: controlTypeSelect.value
            };
            
            alert('تم حفظ الإعدادات بنجاح!');
            settingsScreen.style.display = 'none';
            startScreen.style.display = 'flex';
        });
        
        settingsBack.addEventListener('click', () => {
            settingsScreen.style.display = 'none';
            startScreen.style.display = 'flex';
        });
        
        shopBack.addEventListener('click', () => {
            shopScreen.style.display = 'none';
            startScreen.style.display = 'flex';
        });
        
        // أحداث المتجر
        document.querySelectorAll('.buy-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const item = this.parentElement.querySelector('h3').textContent;
                
                if (item.includes('ذهبية')) {
                    if (score >= 100) {
                        purchasedItems.goldenSkin = true;
                        score -= 100;
                        alert('تم شراء المظهر الذهبي بنجاح!');
                    } else {
                        alert('ليس لديك نقاط كافية! تحتاج إلى 100 نقطة');
                    }
                } else if (item.includes('فضاء')) {
                    if (score >= 150) {
                        purchasedItems.spaceBg = true;
                        score -= 150;
                        alert('تم شراء خلفية الفضاء بنجاح!');
                    } else {
                        alert('ليس لديك نقاط كافية! تحتاج إلى 150 نقطة');
                    }
                } else if (item.includes('طعام')) {
                    if (score >= 200) {
                        purchasedItems.specialFood = true;
                        score -= 200;
                        alert('تم شراء الطعام الخاص بنجاح!');
                    } else {
                        alert('ليس لديك نقاط كافية! تحتاج إلى 200 نقطة');
                    }
                }
                
                updateScore();
            });
        });
        
        restartBtn.addEventListener('click', () => {
            initGame();
        });
        
        mainMenuBtn.addEventListener('click', () => {
            gameOverScreen.style.display = 'none';
            startScreen.style.display = 'flex';
        });
        
        // التحكم بالأزرار
        upBtn.addEventListener('click', () => handleDirectionChange('up'));
        leftBtn.addEventListener('click', () => handleDirectionChange('left'));
        rightBtn.addEventListener('click', () => handleDirectionChange('right'));
        downBtn.addEventListener('click', () => handleDirectionChange('down'));
        
        // التحكم باللمس
        let touchStartX = 0;
        let touchStartY = 0;
        
        canvas.addEventListener('touchstart', (e) => {
            if (!isGameRunning || currentSettings.controlType === 'buttons') return;
            
            const touch = e.touches[0];
            touchStartX = touch.clientX;
            touchStartY = touch.clientY;
        }, { passive: true });
        
        canvas.addEventListener('touchmove', (e) => {
            if (!isGameRunning || currentSettings.controlType === 'buttons') return;
            
            e.preventDefault();
            const touch = e.touches[0];
            const diffX = touch.clientX - touchStartX;
            const diffY = touch.clientY - touchStartY;
            
            if (Math.abs(diffX) > Math.abs(diffY)) {
                if (diffX > 10) handleDirectionChange('right');
                if (diffX < -10) handleDirectionChange('left');
            } else {
                if (diffY > 10) handleDirectionChange('down');
                if (diffY < -10) handleDirectionChange('up');
            }
        }, { passive: false });
        
        // التحكم بلوحة المفاتيح
        document.addEventListener('keydown', (e) => {
            if (!isGameRunning) return;
            
            switch (e.key) {
                case 'ArrowUp': handleDirectionChange('up'); break;
                case 'ArrowDown': handleDirectionChange('down'); break;
                case 'ArrowLeft': handleDirectionChange('left'); break;
                case 'ArrowRight': handleDirectionChange('right'); break;
            }
        });
        
        // البدء الأولي
        resizeCanvas();
    </script>
</body>
</html>
