
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Hogwarts Parking</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #2563eb; 
            --royal-blue: #1e40af;
            --bg-gradient: linear-gradient(135deg, #f8fafc 0%, #eff6ff 100%); 
            --text-main: #1e293b;
            --white: #ffffff;
        }

        * { box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
        
        body { 
            margin: 0; 
            background: var(--bg-gradient); 
            display: flex; justify-content: center; align-items: center; 
            min-height: 100vh;
        }

        .login-container {
            width: 100%; max-width: 900px; 
            background: var(--white); border-radius: 32px;
            display: flex; overflow: hidden;
            box-shadow: 0 20px 45px -10px rgba(59, 130, 246, 0.1);
            margin: 20px;
        }

        /* Sisi Kiri - Logo & Branding */
        .brand-side {
            flex: 1; background: #f1f5f9;
            display: flex; flex-direction: column; justify-content: center; align-items: center;
            padding: 60px; border-right: 1px solid rgba(226, 232, 240, 0.8);
        }

        .brand-side img { width: 180px; margin-bottom: 25px; }
        .brand-side h2 { color: #0f172a; font-weight: 800; margin: 0; font-size: 24px; }
        .brand-side p { color: #475569; font-size: 14px; margin-top: 10px; }

        /* Sisi Kanan - Form Login */
        .form-side {
            flex: 1; padding: 60px;
            display: flex; flex-direction: column; justify-content: center;
        }

        .form-side h1 { font-size: 28px; font-weight: 800; color: #0f172a; margin-bottom: 10px; }
        .form-side p { color: #475569; font-size: 14px; margin-bottom: 35px; }

        .input-group { margin-bottom: 20px; }
        .input-group label { display: block; font-size: 13px; font-weight: 700; color: #0f172a; margin-bottom: 8px; }
        
        .input-group input {
            width: 100%; padding: 14px 20px; border-radius: 16px;
            border: 2px solid #f1f5f9; background: #f1f5f9;
            font-size: 14px; color: #0f172a; transition: 0.3s;
        }

        .input-group input:focus {
            outline: none; border-color: #2563eb; background: white;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        .btn-login {
            width: 100%; padding: 16px; border-radius: 16px;
            background: #2563eb; color: white; border: none;
            font-size: 16px; font-weight: 700; cursor: pointer;
            transition: 0.3s; margin-top: 10px;
            box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.3);
        }

        .btn-login:hover { background: var(--royal-blue); transform: translateY(-2px); }

        /* Responsive */
        @media (max-width: 768px) {
            .login-container { flex-direction: column; max-width: 450px; }
            .brand-side { padding: 40px; border-right: none; border-bottom: 1px solid #f1f5f9; }
            .form-side { padding: 40px; }
        }
    </style>
</head>
<body>

    <div class="login-container">
        <div class="brand-side">
            <img src="../public/hogwarts-removebg-preview.png" alt="Logo">
            <h2>PARLINE</h2>
            <p>Smart Parking Solution</p>
        </div>

        <div class="form-side">
            <h1>Selamat Datang</h1>
            <p>Silakan login untuk mengelola Website Parline</p>

            <form action="proses_login.php" method="POST">
                <div class="input-group">
                    <label>Username</label>
                    <input type="text" name="username" placeholder="Masukkan username" required autocomplete="off">
                </div>

                <div class="input-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="••••••••" required>
                </div>

                <button type="submit" name="login" class="btn-login">Masuk ke Sistem</button>
            </form>
        </div>
    </div>

</body>
</html>
/
