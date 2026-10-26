<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database.php';

$db = db_connect();
$circuits = get_circuits($db);
$user = current_user();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YumTrip | Découvrez Nice à travers ses saveurs</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
    <header class="topbar">
        <div class="container nav">
            <a href="index.php" class="brand">Yum<span>Trip</span></a>
            <nav class="menu">
                <a href="index.php">Accueil</a>
                <a href="#circuits">Circuits</a>
                <a href="#experience">Expérience</a>
                <a href="panier.php">Panier</a>
                <?php if ($user): ?>
                    <a href="profil.php">Mon compte</a>
                    <a href="deconnexion.php" class="btn">Déconnexion</a>
                <?php else: ?>
                    <a href="connexion.php">Connexion</a>
                    <a href="inscription.php" class="btn">S'inscrire</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main>
        <section class="hero">
            <div class="container hero-content">
                <span class="eyebrow">Carte gourmande & culturelle</span>
                <h1>Parcourez Nice en savourant ses meilleurs trésors.</h1>
                <p>Des circuits guidés entre monuments, points de rendez-vous et spécialités culinaires locales. Une expérience immersive, facile à vivre et à partager.</p>
                <p><strong>Vaness je t'aime</strong></p>
                <div class="cta-row">
                    <a href="#circuits" class="btn btn-primary">Découvrir les circuits</a>
                    <a href="inscription.php" class="btn btn-secondary">Créer mon compte</a>
                </div>
            </div>
        </section>

        <section class="section" id="circuits">
            <div class="container">
                <div class="section-head">
                    <div>
                        <div class="eyebrow">Nos cartes</div>
                        <h2>Des parcours gourmands dans la ville</h2>
                    </div>
                </div>

                <div class="grid">
                    <?php foreach ($circuits as $circuit): ?>
                        <article class="card">
                            <img src="<?= e($circuit['image']) ?>" alt="<?= e($circuit['name']) ?>">
                            <div class="card-body">
                                <span class="badge"><?= e($circuit['difficulty']) ?></span>
                                <h3><?= e($circuit['name']) ?></h3>
                                <div class="meta">
                                    <span><?= e($circuit['duration']) ?></span>
                                    <span><?= e($circuit['start_point']) ?></span>
                                </div>
                                <p><?= e($circuit['description']) ?></p>
                                <div class="card-footer">
                                    <div class="price"><?= number_format((float) $circuit['price'], 2, ',', ' ') ?> €</div>
                                    <a class="btn btn-primary" href="circuit.php?slug=<?= urlencode($circuit['slug']) ?>">Voir le circuit</a>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="section" id="experience">
            <div class="container">
                <div class="section-head">
                    <div>
                        <div class="eyebrow">Pourquoi nous choisir</div>
                        <h2>Une immersion culturelle et gastronomique</h2>
                    </div>
                </div>

                <div class="feature-grid">
                    <div class="feature">
                        <div class="icon">📍</div>
                        <h4>Points de rendez-vous</h4>
                        <p>Des arrêts précis dans la ville où nos clients peuvent se retrouver, arrêter, et savourer chaque étape.</p>
                    </div>
                    <div class="feature">
                        <div class="icon">🏛️</div>
                        <h4>Monuments emblématiques</h4>
                        <p>Des parcours construits autour des lieux les plus représentatifs de Nice et de son patrimoine.</p>
                    </div>
                    <div class="feature">
                        <div class="icon">🍽️</div>
                        <h4>Spécialités locales</h4>
                        <p>Découverte des saveurs niçoises : socca, pissaladière, pan bagnat, produits du marché et douceurs.</p>
                    </div>
                    <div class="feature">
                        <div class="icon">🎟️</div>
                        <h4>Carte numérique</h4>
                        <p>Une expérience fluide et simple à réserver, avec suivi de vos circuits et achats enregistrés.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="section">
            <div class="container stats">
                <div class="stats-grid">
                    <div class="stat">
                        <strong>3</strong>
                        <span>Circuits à la carte</span>
                    </div>
                    <div class="stat">
                        <strong>18+</strong>
                        <span>Points de rendez-vous</span>
                    </div>
                    <div class="stat">
                        <strong>1200+</strong>
                        <span>Clients satisfaits</span>
                    </div>
                    <div class="stat">
                        <strong>4.9/5</strong>
                        <span>Note moyenne</span>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="container footer-inner">
            <div>
                <strong>YumTrip</strong> – Découvrir Nice par le goût
            </div>
            <div>Nice, France</div>
        </div>
    </footer>
</body>
</html>
