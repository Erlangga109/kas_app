<?php
$errors = [];
$success = false;

// Koneksi database
$host = 'localhost';
$dbname = 'db_kas';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username)) $errors['username'] = 'Username wajib diisi.';
    if (empty($password)) $errors['password'] = 'Password wajib diisi.';

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user_data && password_verify($password, $user_data['password'])) {
            session_start();
            $_SESSION['user_id']  = $user_data['id'];
            $_SESSION['nama']     = $user_data['nama'];
            $_SESSION['username'] = $user_data['username'];
            $_SESSION['role']     = $user_data['role'];

            // Arahkan ke dashboard setelah login berhasil
            header("Location: dashboard.php");
            exit;
        } else {
            $errors['login'] = 'Username atau password salah.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — SiKas</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --green-dark: #1a3a2e;
            --green-mid: #2d6a4f;
            --green-accent: #52b788;
            --gold: #c9a84c;
            --cream: #f8f4ed;
            --cream-dark: #ede8df;
            --text-dark: #1a1a1a;
            --text-mid: #4a4a4a;
            --text-light: #8a8a8a;
            --error: #c0392b;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background-color: var(--green-dark);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            top: -100px; right: -100px;
            width: 400px; height: 400px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(82,183,136,0.15), transparent 70%);
            pointer-events: none;
        }

        body::after {
            content: '';
            position: fixed;
            bottom: -80px; left: -80px;
            width: 300px; height: 300px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(201,168,76,0.12), transparent 70%);
            pointer-events: none;
        }

        .deco {
            position: fixed;
            border-radius: 50%;
            pointer-events: none;
            opacity: 0.06;
        }
        .deco-1 {
            width: 200px; height: 200px;
            border: 2px solid var(--gold);
            top: 10%; left: 5%;
            animation: float 8s ease-in-out infinite;
        }
        .deco-2 {
            width: 120px; height: 120px;
            border: 2px solid var(--green-accent);
            bottom: 20%; right: 8%;
            animation: float 6s ease-in-out infinite reverse;
        }
        .deco-3 {
            width: 60px; height: 60px;
            background: var(--gold);
            top: 60%; left: 12%;
            animation: float 10s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-18px); }
        }

        .container {
            width: 100%;
            max-width: 440px;
            animation: fadeUp 0.7s ease both;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(28px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .brand {
            text-align: center;
            margin-bottom: 2rem;
        }

        .brand-icon {
            width: 72px; height: 72px;
            background: linear-gradient(135deg, var(--green-accent), var(--gold));
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            font-size: 2rem;
            box-shadow: 0 12px 32px rgba(82,183,136,0.3);
        }

        .brand h1 {
            font-family: 'Playfair Display', serif;
            color: var(--cream);
            font-size: 2.4rem;
            letter-spacing: 0.02em;
        }

        .brand p {
            color: var(--green-accent);
            font-size: 0.8rem;
            font-weight: 400;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            margin-top: 0.3rem;
        }

        .card {
            background: var(--cream);
            border-radius: 24px;
            padding: 2.5rem 2rem;
            box-shadow: 0 24px 64px rgba(0,0,0,0.4), 0 0 0 1px rgba(255,255,255,0.05);
        }

        .card-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.4rem;
            color: var(--green-dark);
            margin-bottom: 0.3rem;
        }

        .card-subtitle {
            font-size: 0.875rem;
            color: var(--text-light);
            margin-bottom: 1.75rem;
        }

        /* Error global */
        .alert-error {
            background: #fdecea;
            border: 1px solid #f5c6cb;
            border-radius: 10px;
            padding: 0.85rem 1rem;
            color: var(--error);
            font-size: 0.875rem;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-group { margin-bottom: 1.1rem; }

        label {
            display: block;
            font-size: 0.8rem;
            font-weight: 500;
            color: var(--text-mid);
            margin-bottom: 0.4rem;
            letter-spacing: 0.03em;
            text-transform: uppercase;
        }

        .input-wrap { position: relative; }

        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1rem;
            pointer-events: none;
            opacity: 0.45;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 0.7rem 1rem 0.7rem 2.5rem;
            border: 1.5px solid var(--cream-dark);
            border-radius: 10px;
            background: #fff;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.95rem;
            color: var(--text-dark);
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        input:focus {
            outline: none;
            border-color: var(--green-accent);
            box-shadow: 0 0 0 3px rgba(82,183,136,0.15);
        }

        input.is-error { border-color: var(--error); }

        .error-msg {
            font-size: 0.775rem;
            color: var(--error);
            margin-top: 0.3rem;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        .toggle-pw {
            position: absolute;
            right: 12px; top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            opacity: 0.4;
            transition: opacity 0.2s;
            padding: 0;
        }
        .toggle-pw:hover { opacity: 0.8; }

        input.has-toggle { padding-right: 2.5rem; }

        .forgot-link {
            text-align: right;
            margin-top: 0.4rem;
        }
        .forgot-link a {
            font-size: 0.8rem;
            color: var(--green-mid);
            text-decoration: none;
        }
        .forgot-link a:hover { text-decoration: underline; }

        .btn-submit {
            width: 100%;
            padding: 0.85rem;
            background: linear-gradient(135deg, var(--green-mid), var(--green-dark));
            color: #fff;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.95rem;
            font-weight: 500;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: transform 0.15s, box-shadow 0.15s;
            margin-top: 0.75rem;
            letter-spacing: 0.02em;
        }
        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(26,58,46,0.4);
        }
        .btn-submit:active { transform: translateY(0); }

        .register-link {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.875rem;
            color: var(--text-light);
        }
        .register-link a {
            color: var(--green-mid);
            font-weight: 500;
            text-decoration: none;
        }
        .register-link a:hover { text-decoration: underline; }

        .back-link {
            text-align: center;
            margin-top: 0.75rem;
        }
        .back-link a {
            font-size: 0.8rem;
            color: rgba(248,244,237,0.45);
            text-decoration: none;
            transition: color 0.2s;
        }
        .back-link a:hover { color: var(--cream); }

        .footer-note {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.78rem;
            color: rgba(248,244,237,0.35);
        }

        @media (max-width: 480px) {
            .card { padding: 2rem 1.25rem; }
            .brand h1 { font-size: 2rem; }
        }
    </style>
</head>
<body>

<div class="deco deco-1"></div>
<div class="deco deco-2"></div>
<div class="deco deco-3"></div>

<div class="container">
    <div class="brand">
        <div class="brand-icon">💰</div>
        <h1>SiKas</h1>
        <p>Sistem Informasi Kas</p>
    </div>

    <div class="card">
        <h2 class="card-title">Masuk ke Akun</h2>
        <p class="card-subtitle">Silakan masukkan kredensial Anda</p>

        <?php if (isset($errors['login'])): ?>
            <div class="alert-error">⚠ <?= htmlspecialchars($errors['login']) ?></div>
        <?php endif; ?>

        <form method="POST" action="" novalidate>

            <div class="form-group">
                <label for="username">Username</label>
                <div class="input-wrap">
                    <span class="input-icon">🪪</span>
                    <input type="text" id="username" name="username"
                        placeholder="Masukkan username"
                        value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                        class="<?= isset($errors['username']) ? 'is-error' : '' ?>">
                </div>
                <?php if (isset($errors['username'])): ?>
                    <div class="error-msg">⚠ <?= $errors['username'] ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrap">
                    <span class="input-icon">🔒</span>
                    <input type="password" id="password" name="password"
                        placeholder="Masukkan password"
                        class="has-toggle <?= isset($errors['password']) ? 'is-error' : '' ?>">
                    <button type="button" class="toggle-pw" onclick="togglePw('password', this)">👁</button>
                </div>
                <?php if (isset($errors['password'])): ?>
                    <div class="error-msg">⚠ <?= $errors['password'] ?></div>
                <?php endif; ?>
                <div class="forgot-link"><a href="lupa_password.php">Lupa password?</a></div>
            </div>

            <button type="submit" class="btn-submit">Masuk →</button>
        </form>

        <p class="register-link">Belum punya akun? <a href="register.php">Daftar di sini</a></p>
    </div>

    <div class="back-link"><a href="halaman.php">← Kembali ke beranda</a></div>
    <p class="footer-note">&copy; <?= date('Y') ?> SiKas. Semua hak dilindungi.</p>
</div>

<script>
function togglePw(id, btn) {
    const input = document.getElementById(id);
    input.type = input.type === 'password' ? 'text' : 'password';
    btn.textContent = input.type === 'password' ? '👁' : '🙈';
}
</script>

</body>
</html>