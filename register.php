<?php
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? '';

    // Validasi
    if (empty($nama)) $errors['nama'] = 'Nama lengkap wajib diisi.';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Email tidak valid.';
    if (empty($username) || strlen($username) < 4) $errors['username'] = 'Username minimal 4 karakter.';
    if (empty($password) || strlen($password) < 6) $errors['password'] = 'Password minimal 6 karakter.';
    if ($password !== $confirm_password) $errors['confirm_password'] = 'Konfirmasi password tidak cocok.';
    if (empty($role)) $errors['role'] = 'Pilih peran pengguna.';

    if (empty($errors)) {
        // Simulasi penyimpanan (ganti dengan logika database di sini)
        // $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        // Contoh: $pdo->prepare("INSERT INTO users (nama, email, username, password, role) VALUES (?,?,?,?,?)")
        $success = true;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi — SiKas</title>
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
            overflow-x: hidden;
        }

        /* Background decorative elements */
        body::before {
            content: '';
            position: fixed;
            top: -100px;
            right: -100px;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(82,183,136,0.15), transparent 70%);
            pointer-events: none;
        }

        body::after {
            content: '';
            position: fixed;
            bottom: -80px;
            left: -80px;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(201,168,76,0.12), transparent 70%);
            pointer-events: none;
        }

        .container {
            width: 100%;
            max-width: 480px;
            animation: fadeUp 0.6s ease both;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(24px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Brand header */
        .brand {
            text-align: center;
            margin-bottom: 2rem;
        }

        .brand-icon {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, var(--green-accent), var(--gold));
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.75rem;
            font-size: 1.5rem;
        }

        .brand h1 {
            font-family: 'Playfair Display', serif;
            color: var(--cream);
            font-size: 1.8rem;
            letter-spacing: 0.02em;
        }

        .brand p {
            color: var(--green-accent);
            font-size: 0.85rem;
            font-weight: 300;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-top: 0.2rem;
        }

        /* Card */
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

        /* Success state */
        .success-box {
            background: #d4edda;
            border: 1px solid #a8d5b5;
            border-radius: 12px;
            padding: 1.25rem 1.5rem;
            color: #155724;
            text-align: center;
            animation: fadeUp 0.4s ease;
        }

        .success-box .success-icon {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }

        .success-box h3 {
            font-family: 'Playfair Display', serif;
            margin-bottom: 0.4rem;
        }

        .success-box p { font-size: 0.875rem; }

        /* Form groups */
        .form-group {
            margin-bottom: 1.1rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        label {
            display: block;
            font-size: 0.8rem;
            font-weight: 500;
            color: var(--text-mid);
            margin-bottom: 0.4rem;
            letter-spacing: 0.03em;
            text-transform: uppercase;
        }

        .input-wrap {
            position: relative;
        }

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
        input[type="email"],
        input[type="password"],
        select {
            width: 100%;
            padding: 0.7rem 1rem 0.7rem 2.5rem;
            border: 1.5px solid var(--cream-dark);
            border-radius: 10px;
            background: #fff;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.95rem;
            color: var(--text-dark);
            transition: border-color 0.2s, box-shadow 0.2s;
            appearance: none;
            -webkit-appearance: none;
        }

        input:focus, select:focus {
            outline: none;
            border-color: var(--green-accent);
            box-shadow: 0 0 0 3px rgba(82,183,136,0.15);
        }

        input.is-error, select.is-error {
            border-color: var(--error);
        }

        .error-msg {
            font-size: 0.775rem;
            color: var(--error);
            margin-top: 0.3rem;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        select {
            cursor: pointer;
        }

        .select-arrow {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            font-size: 0.7rem;
            opacity: 0.4;
        }

        /* Password toggle */
        .toggle-pw {
            position: absolute;
            right: 12px;
            top: 50%;
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

        input[type="password"].no-icon,
        input[type="text"].no-icon {
            padding-right: 2.5rem;
        }

        /* Submit button */
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
            margin-top: 0.5rem;
            letter-spacing: 0.02em;
        }

        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(26,58,46,0.4);
        }

        .btn-submit:active { transform: translateY(0); }

        /* Login link */
        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.875rem;
            color: var(--text-light);
        }

        .login-link a {
            color: var(--green-mid);
            font-weight: 500;
            text-decoration: none;
        }

        .login-link a:hover { text-decoration: underline; }

        /* Divider */
        .divider {
            border: none;
            border-top: 1px solid var(--cream-dark);
            margin: 1.25rem 0;
        }

        @media (max-width: 480px) {
            .form-row { grid-template-columns: 1fr; gap: 0; }
            .card { padding: 2rem 1.25rem; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="brand">
        <div class="brand-icon">💰</div>
        <h1>SiKas</h1>
        <p>Sistem Informasi Kas</p>
    </div>

    <div class="card">
        <?php if ($success): ?>
            <div class="success-box">
                <div class="success-icon">✅</div>
                <h3>Registrasi Berhasil!</h3>
                <p>Akun Anda telah dibuat. Silakan <a href="login.php">masuk ke aplikasi</a>.</p>
            </div>
        <?php else: ?>
            <h2 class="card-title">Buat Akun Baru</h2>
            <p class="card-subtitle">Lengkapi data berikut untuk mendaftar</p>

            <form method="POST" action="" novalidate>

                <div class="form-group">
                    <label for="nama">Nama Lengkap</label>
                    <div class="input-wrap">
                        <span class="input-icon">👤</span>
                        <input type="text" id="nama" name="nama"
                            placeholder="Nama lengkap Anda"
                            value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>"
                            class="<?= isset($errors['nama']) ? 'is-error' : '' ?>">
                    </div>
                    <?php if (isset($errors['nama'])): ?>
                        <div class="error-msg">⚠ <?= $errors['nama'] ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <div class="input-wrap">
                        <span class="input-icon">✉️</span>
                        <input type="email" id="email" name="email"
                            placeholder="email@domain.com"
                            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                            class="<?= isset($errors['email']) ? 'is-error' : '' ?>">
                    </div>
                    <?php if (isset($errors['email'])): ?>
                        <div class="error-msg">⚠ <?= $errors['email'] ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <div class="input-wrap">
                            <span class="input-icon">🪪</span>
                            <input type="text" id="username" name="username"
                                placeholder="min. 4 karakter"
                                value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                                class="<?= isset($errors['username']) ? 'is-error' : '' ?>">
                        </div>
                        <?php if (isset($errors['username'])): ?>
                            <div class="error-msg">⚠ <?= $errors['username'] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="role">Peran</label>
                        <div class="input-wrap">
                            <span class="input-icon">🏷</span>
                            <select id="role" name="role" class="<?= isset($errors['role']) ? 'is-error' : '' ?>">
                                <option value="">-- Pilih --</option>
                                <option value="bendahara" <?= ($_POST['role'] ?? '') === 'bendahara' ? 'selected' : '' ?>>Bendahara</option>
                                <option value="anggota" <?= ($_POST['role'] ?? '') === 'anggota' ? 'selected' : '' ?>>Anggota</option>
                                <option value="admin" <?= ($_POST['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
                            </select>
                            <span class="select-arrow">▼</span>
                        </div>
                        <?php if (isset($errors['role'])): ?>
                            <div class="error-msg">⚠ <?= $errors['role'] ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <hr class="divider">

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrap">
                        <span class="input-icon">🔒</span>
                        <input type="password" id="password" name="password"
                            placeholder="min. 6 karakter"
                            class="no-icon <?= isset($errors['password']) ? 'is-error' : '' ?>">
                        <button type="button" class="toggle-pw" onclick="togglePw('password', this)">👁</button>
                    </div>
                    <?php if (isset($errors['password'])): ?>
                        <div class="error-msg">⚠ <?= $errors['password'] ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Konfirmasi Password</label>
                    <div class="input-wrap">
                        <span class="input-icon">🔒</span>
                        <input type="password" id="confirm_password" name="confirm_password"
                            placeholder="Ulangi password"
                            class="no-icon <?= isset($errors['confirm_password']) ? 'is-error' : '' ?>">
                        <button type="button" class="toggle-pw" onclick="togglePw('confirm_password', this)">👁</button>
                    </div>
                    <?php if (isset($errors['confirm_password'])): ?>
                        <div class="error-msg">⚠ <?= $errors['confirm_password'] ?></div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn-submit">Daftar Sekarang →</button>
            </form>

            <p class="login-link">Sudah punya akun? <a href="login.php">Masuk di sini</a></p>
        <?php endif; ?>
    </div>
</div>

<script>
function togglePw(id, btn) {
    const input = document.getElementById(id);
    if (input.type === 'password') {
        input.type = 'text';
        btn.textContent = '🙈';
    } else {
        input.type = 'password';
        btn.textContent = '👁';
    }
}
</script>

</body>
</html> 