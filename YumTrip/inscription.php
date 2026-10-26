<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/includes/functions.php';

$db = db_connect();
$error = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        register_user($db, $_POST);
        $success = 'Votre compte a été créé avec succès. Vous pouvez maintenant vous connecter.';
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
    <title>Inscription | YumTrip</title>
    <link rel="stylesheet" href="css/inscription.css">
</head>
<body>
    <header class="topbar">
        <div class="container nav">
            <a href="index.php" class="brand">Yum<span>Trip</span></a>
            <a href="connexion.php">Déjà inscrit ?</a>
        </div>
    </header>

    <main class="auth-wrap">
        <section class="visual">
            <div class="visual-box">
                <h1>Créez votre compte YumTrip</h1>
                <p>Réservez votre parcours gourmand dans Nice et accédez à vos circuits, votre historique et vos points de rendez-vous favoris.</p>
            </div>
        </section>

        <section class="form-panel">
            <form method="post" action="inscription.php">
                <h2>Inscription</h2>

                <?php if ($error): ?>
                    <div class="alert error"><?= e($error) ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert success"><?= e($success) ?></div>
                <?php endif; ?>

                <div class="field">
                    <label for="fullname">Nom complet</label>
                    <input id="fullname" name="fullname" type="text" placeholder="Jean Dupont" required>
                </div>

                <div class="field">
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" placeholder="jean@email.com" required>
                </div>

                <div class="field">
                    <label for="password">Mot de passe</label>
                    <input id="password" name="password" type="password" placeholder="Minimum 6 caractères" required>
                </div>

                <div class="field">
                    <label for="phone">Téléphone</label>
                    <input id="phone" name="phone" type="tel" placeholder="06 12 34 56 78">
                </div>

                <div class="field">
                    <label for="city">Ville</label>
                    <input id="city" name="city" type="text" placeholder="Nice">
                </div>

                <button type="submit">Créer mon compte</button>
                <div class="small"><a href="connexion.php">J’ai déjà un compte</a></div>
            </form>
        </section>
    </main>
</body>
</html>
