<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>AZ Tournée — Connexion</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #0f1923 0%, #1a2b4a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }
        .login-card {
            background: #1a2535;
            border-radius: 20px;
            padding: 40px 32px;
            width: 100%;
            max-width: 380px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.5);
        }
        .login-logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-logo .icon {
            font-size: 3rem;
            margin-bottom: 10px;
        }
        .login-logo h4 {
            color: #60a5fa;
            font-weight: 700;
            margin: 0;
        }
        .login-logo p {
            color: #4a6080;
            font-size: 0.82rem;
            margin: 4px 0 0;
        }
        .form-label {
            color: #a0aec0;
            font-size: 0.82rem;
            font-weight: 600;
            margin-bottom: 6px;
        }
        .form-control {
            background: #0d1a2e;
            border: 1.5px solid #2d4a8a;
            color: #e8ecf0;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 1rem;
        }
        .form-control:focus {
            background: #0d1a2e;
            border-color: #0d6efd;
            color: #e8ecf0;
            box-shadow: 0 0 0 3px rgba(13,110,253,0.2);
        }
        .form-control::placeholder { color: #4a6080; }
        .btn-login {
            background: linear-gradient(135deg, #0d6efd, #0056d3);
            border: none;
            border-radius: 10px;
            padding: 13px;
            font-size: 1rem;
            font-weight: 600;
            color: white;
            width: 100%;
            margin-top: 8px;
            transition: all 0.2s;
        }
        .btn-login:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(13,110,253,0.4); }
        .btn-login:active { transform: translateY(0); }
        .error-msg {
            background: rgba(220,53,69,0.15);
            border: 1px solid rgba(220,53,69,0.3);
            color: #ff8888;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 0.82rem;
            margin-bottom: 16px;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-logo">
            <div class="icon">🚗</div>
            <h4>AZ Tournée</h4>
            <p>Interface Chauffeur</p>
        </div>

        @if($errors->has('login'))
            <div class="error-msg">
                <i class="fas fa-exclamation-circle me-1"></i>
                {{ $errors->first('login') }}
            </div>
        @endif

        <form action="{{ route('chauffeur.login.post') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Votre nom</label>
                <input type="text" name="name" class="form-control"
                       placeholder="Ex: Mohamed" value="{{ old('name') }}"
                       autocomplete="name" required autofocus>
            </div>
            <div class="mb-4">
                <label class="form-label">Mot de passe</label>
                <input type="password" name="password" class="form-control"
                       placeholder="••••••••" autocomplete="current-password" required>
            </div>
            <button type="submit" class="btn-login">
                🚀 Accéder à ma tournée
            </button>
        </form>
    </div>
</body>
</html>