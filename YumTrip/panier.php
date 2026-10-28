<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/includes/functions.php';

$db = db_connect();
$cartItems = get_cart_items($db);
$cartTotal = get_cart_total($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $circuitId = (int) ($_POST['circuit_id'] ?? 0);
        if ($circuitId > 0) {
            add_to_cart($circuitId);
        }
        redirect(app_url('panier.php'));
    }

    if ($_POST['action'] === 'remove') {
        $circuitId = (int) ($_POST['circuit_id'] ?? 0);
        remove_from_cart($circuitId);
        redirect(app_url('panier.php'));
    }

    if ($_POST['action'] === 'checkout') {
        require_login();
        try {
            checkout_cart($db, (int) $_SESSION['user_id']);
            redirect(app_url('profil.php?success=reservation'));
        } catch (InvalidArgumentException $e) {
            $error = $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panier</title>
    <link rel="stylesheet" href="css/panier.css?v=<?= filemtime(__DIR__ . '/css/panier.css') ?>">
</head>
<body>
    <header class="topbar">
        <div class="container nav">
            <a href="index.php" class="brand" aria-label="YumTrip - Food Tours, accueil">
                <img class="brand-logo" src="assets/images/logo.png" alt="YumTrip - Food Tours">
            </a>
            <a href="profil.php">Mon compte</a>
        </div>
    </header>

    <main class="container page">
        <h1>Mon panier</h1>

        <?php if (!empty($error)): ?>
            <div class="alert"><?= e($error) ?></div>
        <?php endif; ?>

        <div class="grid">
            <section class="card">
                <?php if (empty($cartItems)): ?>
                    <p class="muted">Votre panier est vide pour le moment.</p>
                    <a href="index.php" class="btn primary">Découvrir les circuits</a>
                <?php else: ?>
                    <?php foreach ($cartItems as $item): ?>
                        <div class="item">
                            <div class="item-main">
                                <img src="<?= e($item['image']) ?>" class="thumb" alt="<?= e($item['name']) ?>">
                                <div>
                                    <strong><?= e($item['name']) ?></strong><br>
                                    <span class="muted"><?= (int) $item['quantity'] ?> carte(s) × <?= number_format((float) $item['price'], 2, ',', ' ') ?> €</span>
                                </div>
                            </div>
                            <div class="inline-form">
                                <strong><?= number_format((float) $item['total'], 2, ',', ' ') ?> €</strong>
                                <form method="post" action="panier.php">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="circuit_id" value="<?= (int) $item['id'] ?>">
                                    <button type="submit" class="primary">Retirer</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </section>

            <aside class="summary">
                <h3>Résumé</h3>
                <div>Total estimé : <strong><?= number_format((float) $cartTotal, 2, ',', ' ') ?> €</strong></div>
                <div class="total"><?= number_format((float) $cartTotal, 2, ',', ' ') ?> €</div>

                <?php if (!empty($cartItems)): ?>
                    <form method="post" action="panier.php">
                        <input type="hidden" name="action" value="checkout">
                        <button type="submit" class="secondary full-width">Valider la commande</button>
                    </form>
                <?php endif; ?>
            </aside>
        </div>
    </main>
</body>
</html>
