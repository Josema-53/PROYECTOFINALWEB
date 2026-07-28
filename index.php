<?php
session_start();
if (isset($_SESSION['usuario_activo'])) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Radio FM - Acceso</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@700;900&display=swap');

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background: #0d0d0d;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow: hidden;
        }

        .login-bg {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background:
                radial-gradient(ellipse at 20% 50%, rgba(229,22,63,0.15) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 20%, rgba(255,108,17,0.1) 0%, transparent 50%),
                radial-gradient(ellipse at 50% 80%, rgba(123,47,247,0.08) 0%, transparent 50%);
            z-index: 0;
        }

        .login-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 420px;
            padding: 20px;
        }

        .login-card {
            background: rgba(26, 26, 46, 0.95);
            backdrop-filter: blur(20px);
            border: 2px solid rgba(229, 22, 63, 0.2);
            border-radius: 20px;
            padding: 40px 32px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.5), 0 0 40px rgba(229,22,63,0.1);
        }

        .login-logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-logo .icon {
            font-size: 3em;
            display: block;
            margin-bottom: 8px;
            animation: pulse-glow 2s ease-in-out infinite;
        }

        @keyframes pulse-glow {
            0%, 100% { text-shadow: 0 0 5px rgba(229,22,63,0.5); }
            50% { text-shadow: 0 0 30px rgba(229,22,63,1), 0 0 60px rgba(255,108,17,0.4); }
        }

        .login-logo h1 {
            font-family: 'Orbitron', monospace;
            color: #fff;
            font-size: 1.8em;
            font-weight: 900;
            letter-spacing: 3px;
        }

        .login-logo h1 span {
            background: linear-gradient(135deg, #e5163f, #ff6c11);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .login-logo .subtitle {
            color: #888;
            font-size: 0.85em;
            letter-spacing: 4px;
            text-transform: uppercase;
            margin-top: 4px;
        }

        .form-floating-custom {
            position: relative;
            margin-bottom: 20px;
        }

        .form-floating-custom label {
            color: #888;
            font-size: 0.85em;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 6px;
            display: block;
        }

        .form-floating-custom input {
            width: 100%;
            background: rgba(42, 42, 62, 0.8);
            border: 2px solid rgba(229,22,63,0.1);
            color: #fff;
            border-radius: 12px;
            padding: 14px 18px;
            font-size: 1em;
            transition: all 0.3s;
        }

        .form-floating-custom input:focus {
            outline: none;
            border-color: #e5163f;
            box-shadow: 0 0 0 3px rgba(229,22,63,0.15);
            background: rgba(42, 42, 62, 1);
        }

        .form-floating-custom input::placeholder {
            color: #555;
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #e5163f, #ff6c11);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 1.05em;
            font-weight: 800;
            letter-spacing: 2px;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(229,22,63,0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .alert-rock {
            background: rgba(229, 22, 63, 0.15);
            border: 1px solid rgba(229, 22, 63, 0.3);
            color: #ff6b6b;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 0.9em;
            margin-bottom: 20px;
            text-align: center;
        }

        .login-footer {
            text-align: center;
            margin-top: 24px;
            color: #555;
            font-size: 0.75em;
        }

        /* Floating notes animation */
        .floating-notes {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            pointer-events: none;
            z-index: 0;
            overflow: hidden;
        }

        .note {
            position: absolute;
            font-size: 1.5em;
            opacity: 0.06;
            animation: float-note linear infinite;
        }

        @keyframes float-note {
            0% { transform: translateY(100vh) rotate(0deg); opacity: 0; }
            10% { opacity: 0.06; }
            90% { opacity: 0.06; }
            100% { transform: translateY(-10vh) rotate(360deg); opacity: 0; }
        }
    </style>
</head>
<body>
    <div class="login-bg"></div>

    <div class="floating-notes">
        <span class="note" style="left:10%; animation-duration:15s; animation-delay:0s;">&#9835;</span>
        <span class="note" style="left:25%; animation-duration:18s; animation-delay:2s;">&#9833;</span>
        <span class="note" style="left:40%; animation-duration:12s; animation-delay:4s;">&#9834;</span>
        <span class="note" style="left:60%; animation-duration:20s; animation-delay:1s;">&#9835;</span>
        <span class="note" style="left:75%; animation-duration:16s; animation-delay:3s;">&#9833;</span>
        <span class="note" style="left:90%; animation-duration:14s; animation-delay:5s;">&#9834;</span>
        <span class="note" style="left:15%; animation-duration:22s; animation-delay:6s;">&#9836;</span>
        <span class="note" style="left:80%; animation-duration:17s; animation-delay:7s;">&#9835;</span>
    </div>

    <div class="login-container">
        <div class="login-card">
            <div class="login-logo">
                <span class="icon">&#9889;</span>
                <h1><span>RADIO</span> FM</h1>
                <div class="subtitle">Sistema de Gestion Musical</div>
            </div>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert-rock">&#9888; Usuario o contrasena incorrectos</div>
            <?php endif; ?>

            <form method="POST" action="backend/procesar_login.php">
                <div class="form-floating-custom">
                    <label for="usuario">Usuario</label>
                    <input type="text" id="usuario" name="usuario" placeholder="Tu nombre de DJ..." required autocomplete="username">
                </div>
                <div class="form-floating-custom">
                    <label for="password">Contrasena</label>
                    <input type="password" id="password" name="password" placeholder="Tu clave secreta..." required autocomplete="current-password">
                </div>
                <button type="submit" class="btn-login">&#9889; ENTRAR A LA ONDA</button>
            </form>

            <div class="login-footer">
                Rock Radio FM v1.0 &copy; 2026
            </div>
        </div>
    </div>
</body>
</html>
