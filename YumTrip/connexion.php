<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/includes/functions.php';

$db = db_connect();
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        login_user($db, $email, $password);
        redirect(app_url('profil.php'));
    } catch (InvalidArgumentException $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="css/connexion.css?v=<?= filemtime(__DIR__ . '/css/connexion.css') ?>">
</head>
<body>
    <header class="topbar">
        <div class="container nav">
            <a href="index.php" class="brand" aria-label="YumTrip - Food Tours, accueil">
                <img class="brand-logo" src="assets/images/logo.png" alt="YumTrip - Food Tours">
            </a>
            <a href="inscription.php">Créer un compte</a>
        </div>
    </header>

    <main class="auth-wrap">
        <section class="visual">
            <div class="visual-box">
                <h1>Bienvenue à Nice</h1>
                <p>Connectez-vous pour réserver vos circuits, suivre vos achats et profiter des meilleures expériences gastronomiques de la ville.</p>
            </div>
        </section>

        <section class="form-panel">
            <form method="post" action="connexion.php">
                <h2>Connexion</h2>
                <?php if ($error): ?>
                    <div class="alert"><?= e($error) ?></div>
                <?php endif; ?>

                <div class="field">
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" placeholder="jean@email.com" required>
                </div>

                <div class="field">
                    <label for="password">Mot de passe</label>
                    <input id="password" name="password" type="password" placeholder="Votre mot de passe" required>
                </div>

                <button type="submit">Se connecter</button>
                <div class="small"><a href="inscription.php">Créer un compte</a></div>
            </form>
        </section>
    </main>
</body>
</html>
