<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/includes/functions.php';

$db = db_connect();
$slug = $_GET['slug'] ?? null;
$circuit = $slug ? get_circuit_by_slug($db, $slug) : null;
$user = current_user();

if (!$circuit) {
    redirect(app_url('index.php'));
}

$message = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reserve'])) {
    if (!is_logged_in()) {
        redirect(app_url('connexion.php'));
    }

    try {
        $order = create_order($db, (int) $_SESSION['user_id'], (int) $circuit['id'], 1);
        $message = 'Votre réservation a bien été enregistrée. Référence : #' . $order['order_id'] . ' – Total : ' . number_format((float) $order['total'], 2, ',', ' ') . ' €';
    } catch (Exception $e) {
        $message = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($circuit['name']) ?> | YumTrip</title>
    <link rel="stylesheet" href="css/circuit.css">
</head>
<body>
    <header class="topbar">
        <div class="container nav">
            <a href="index.php" class="brand">Yum<span>Trip</span></a>
            <a href="profil.php">Mon compte</a>
        </div>
    </header>

    <main class="container hero">
        <div class="hero-card">
            <img src="<?= e($circuit['image']) ?>" alt="<?= e($circuit['name']) ?>">
            <div class="hero-content">
                <span class="badge"><?= e($circuit['difficulty']) ?></span>
                <h1><?= e($circuit['name']) ?></h1>
                <div class="meta">
                    <span>⏱️ <?= e($circuit['duration']) ?></span>
                    <span>📍 <?= e($circuit['start_point']) ?></span>
                </div>
                <p><?= e($circuit['description']) ?></p>

                <div class="price-box">
                    <div>Prix de la carte</div>
                    <div class="big-price"><?= number_format((float) $circuit['price'], 2, ',', ' ') ?> €</div>
                </div>

                <form method="post">
                    <input type="hidden" name="reserve" value="1">
                    <button type="submit">Réserver ce circuit</button>
                </form>

                <form method="post" action="panier.php" class="secondary-btn">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="circuit_id" value="<?= (int) $circuit['id'] ?>">
                    <button type="submit">Ajouter au panier</button>
                </form>

                <?php if ($message): ?>
                    <div class="success"><?= e($message) ?></div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <section class="container section">
        <div class="grid">
            <div class="panel">
                <h3>Spécialités culinaires</h3>
                <ul>
                    <?php foreach (explode(',', $circuit['specialties']) as $specialty): ?>
                        <li><?= e(trim($specialty)) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="panel">
                <h3>Points de rendez-vous</h3>
                <ul>
                    <?php foreach (explode(',', $circuit['points']) as $point): ?>
                        <li><?= e(trim($point)) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </section>
</body>
</html>
