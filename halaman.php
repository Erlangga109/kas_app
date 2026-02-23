<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang — SiKas</title>
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

        /* Decorative floating circles */
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

        /* Brand */
        .brand {
            text-align: center;
            margin-bottom: 2rem;
        }

        .brand-icon {
            width: 72px;
            height: 72px;
            background: linear-gradient(135deg, var(--green-accent), var(--gold));
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            font-size: 2rem;
            box-shadow: 0 12px 32px rgba(82,183,136,0.3);
            animation: fadeUp 0.7s ease 0.1s both;
        }

        .brand h1 {
            font-family: 'Playfair Display', serif;
            color: var(--cream);
            font-size: 2.4rem;
            letter-spacing: 0.02em;
            animation: fadeUp 0.7s ease 0.2s both;
        }

        .brand p {
            color: var(--green-accent);
            font-size: 0.8rem;
            font-weight: 400;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            margin-top: 0.3rem;
            animation: fadeUp 0.7s ease 0.3s both;
        }

        /* Card */
        .card {
            background: var(--cream);
            border-radius: 24px;
            padding: 2.5rem 2rem;
            box-shadow: 0 24px 64px rgba(0,0,0,0.4), 0 0 0 1px rgba(255,255,255,0.05);
            animation: fadeUp 0.7s ease 0.35s both;
        }

        .welcome-text {
            text-align: center;
            margin-bottom: 2rem;
        }

        .welcome-text h2 {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            color: var(--green-dark);
            margin-bottom: 0.5rem;
        }

        .welcome-text p {
            font-size: 0.9rem;
            color: var(--text-light);
            line-height: 1.6;
        }

        /* Feature list */
        .features {
            list-style: none;
            margin-bottom: 2rem;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .features li {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.9rem;
            color: var(--text-mid);
        }

        .features li .feat-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, rgba(45,106,79,0.12), rgba(82,183,136,0.08));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
        }

        /* Divider */
        .divider {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-top: 1px solid var(--cream-dark);
        }

        .divider span {
            font-size: 0.75rem;
            color: var(--text-light);
            letter-spacing: 0.06em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        /* Buttons */
        .btn {
            display: block;
            width: 100%;
            padding: 0.85rem;
            border-radius: 10px;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.95rem;
            font-weight: 500;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
            transition: transform 0.15s, box-shadow 0.15s, background 0.2s;
            border: none;
            letter-spacing: 0.02em;
        }

        .btn:hover { transform: translateY(-2px); }
        .btn:active { transform: translateY(0); }

        .btn-primary {
            background: linear-gradient(135deg, var(--green-mid), var(--green-dark));
            color: #fff;
            box-shadow: 0 4px 16px rgba(26,58,46,0.25);
            margin-bottom: 0.85rem;
        }

        .btn-primary:hover {
            box-shadow: 0 8px 24px rgba(26,58,46,0.4);
        }

        .btn-secondary {
            background: transparent;
            color: var(--green-mid);
            border: 1.5px solid var(--cream-dark);
        }

        .btn-secondary:hover {
            background: var(--cream-dark);
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        }

        /* Footer note */
        .footer-note {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.78rem;
            color: rgba(248,244,237,0.35);
            letter-spacing: 0.02em;
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
        <div class="welcome-text">
            <h2>Selamat Datang!</h2>
            <p>Kelola keuangan kas organisasi Anda dengan mudah, transparan, dan terorganisir.</p>
        </div>

        <ul class="features">
            <li>
                <div class="feat-icon">📊</div>
                Laporan keuangan real-time & akurat
            </li>
            <li>
                <div class="feat-icon">🧾</div>
                Pencatatan pemasukan & pengeluaran
            </li>
            <li>
                <div class="feat-icon">👥</div>
                Manajemen anggota & iuran kas
            </li>
            <li>
                <div class="feat-icon">🔐</div>
                Data aman dengan sistem multi-peran
            </li>
        </ul>

        <div class="divider"><span>Mulai sekarang</span></div>

        <a href="login.php" class="btn btn-primary">Masuk ke Akun →</a>
        <a href="register.php" class="btn btn-secondary">Daftar Akun Baru</a>
    </div>

    <p class="footer-note">&copy; <?= date('Y') ?> SiKas. Semua hak dilindungi.</p>
</div>

</body>
</html>