<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/includes/functions.php';

require_admin();
$db = db_connect();
$users = get_all_users($db);
$orders = get_all_orders($db);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <header class="topbar">
        <div class="container nav">
            <a href="index.php" class="brand" aria-label="YumTrip - Food Tours, accueil">
                <img class="brand-logo" src="assets/images/logo.png" alt="YumTrip - Food Tours">
            </a>
            <a href="profil.php">Retour profil</a>
        </div>
    </header>

    <main class="container page">
        <div class="panel">
            <h2>Utilisateurs enregistrés</h2>
            <table>
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Ville</th>
                        <th>Téléphone</th>
                        <th>Compte</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= e($user['fullname']) ?></td>
                            <td><?= e($user['email']) ?></td>
                            <td><?= e($user['city'] ?: '—') ?></td>
                            <td><?= e($user['phone'] ?: '—') ?></td>
                            <td><span class="pill"><?= (int) $user['is_admin'] === 1 ? 'Admin' : 'Client' ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="panel">
            <h2>Commandes reçues</h2>
            <table>
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Carte</th>
                        <th>Total</th>
                        <th>Quantité</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?= e($order['fullname']) ?></td>
                            <td><?= e($order['circuit_name']) ?></td>
                            <td><?= number_format((float) $order['total'], 2, ',', ' ') ?> €</td>
                            <td><?= (int) $order['quantity'] ?></td>
                            <td><span class="pill"><?= e($order['status']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
