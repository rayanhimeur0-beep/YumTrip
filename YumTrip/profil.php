<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/includes/functions.php';

require_login();
$db = db_connect();
$user = current_user();
$orders = get_orders_for_user($db, (int) $user['id']);
$successMessage = isset($_GET['success']) && $_GET['success'] === 'reservation' ? 'Votre réservation a bien été enregistrée.' : null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon profil</title>
    <link rel="stylesheet" href="css/profil.css">
</head>
<body>
    <header class="topbar">
        <div class="container nav">
            <a href="index.php" class="brand" aria-label="YumTrip - Food Tours, accueil">
                <img class="brand-logo" src="assets/images/logo.png" alt="YumTrip - Food Tours">
            </a>
            <div>
                <a href="deconnexion.php" class="btn">Déconnexion</a>
            </div>
        </div>
    </header>

    <main class="page container">
        <div class="grid">
            <aside class="card">
                <h1><?= e($user['fullname']) ?></h1>
                <div class="muted">Compte client</div>
                <div class="info">
                    <div><strong>Email :</strong> <?= e($user['email']) ?></div>
                    <div><strong>Statut :</strong> <?= $user['is_admin'] ? 'Administrateur' : 'Client' ?></div>
                    <?php if ((int) $user['is_admin'] === 1): ?>
                        <div><strong>Admin :</strong> <a href="admin.php">Accéder au tableau de bord</a></div>
                    <?php endif; ?>
                </div>
            </aside>

            <section class="card">
                <?php if ($successMessage): ?>
                    <div class="success-message">
                        <?= e($successMessage) ?>
                    </div>
                <?php endif; ?>
                <h2>Mes réservations</h2>
                <?php if (empty($orders)): ?>
                    <p class="muted">Vous n'avez aucune commande.</p>
                <?php else: ?>
                    <table>
                        <thead>
                        <tr>
                            <th>Carte</th>
                            <th>Prix</th>
                            <th>Quantité</th>
                            <th>Total</th>
                            <th>Statut</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><?= e($order['circuit_name']) ?></td>
                                <td><?= number_format((float) $order['circuit_price'], 2, ',', ' ') ?> €</td>
                                <td><?= (int) $order['quantity'] ?></td>
                                <td><?= number_format((float) $order['total'], 2, ',', ' ') ?> €</td>
                                <td><span class="status"><?= e($order['status']) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </section>
        </div>
    </main>
</body>
</html>
