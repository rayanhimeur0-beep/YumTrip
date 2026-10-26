<?php
require_once __DIR__ . '/config.php';

function db_connect(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $directory = dirname(DB_PATH);
    if (!is_dir($directory)) {
        mkdir($directory, 0777, true);
    }

    $pdo = new PDO('sqlite:' . DB_PATH);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            fullname TEXT NOT NULL,
            email TEXT NOT NULL UNIQUE,
            password TEXT NOT NULL,
            phone TEXT,
            city TEXT,
            created_at TEXT DEFAULT CURRENT_TIMESTAMP,
            is_admin INTEGER DEFAULT 0
        )"
    );

    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS circuits (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            slug TEXT NOT NULL UNIQUE,
            description TEXT NOT NULL,
            duration TEXT NOT NULL,
            price REAL NOT NULL,
            difficulty TEXT NOT NULL,
            specialties TEXT NOT NULL,
            start_point TEXT NOT NULL,
            points TEXT NOT NULL,
            image TEXT NOT NULL,
            created_at TEXT DEFAULT CURRENT_TIMESTAMP
        )"
    );

    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS orders (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            circuit_id INTEGER NOT NULL,
            quantity INTEGER DEFAULT 1,
            total REAL NOT NULL,
            status TEXT DEFAULT 'en attente',
            created_at TEXT DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY(user_id) REFERENCES users(id),
            FOREIGN KEY(circuit_id) REFERENCES circuits(id)
        )"
    );

    $count = (int) $pdo->query('SELECT COUNT(*) FROM circuits')->fetchColumn();
    if ($count === 0) {
        $circuits = [
            [
                'name' => 'Circuit Riviera Gourmande',
                'slug' => 'riviere-gourmande',
                'description' => 'Un parcours au cœur de Nice, entre promeneurs, marchés et saveurs locales : une immersion dans les rues historiques et les spécialités niçoises.',
                'duration' => '3 heures',
                'price' => 39,
                'difficulty' => 'Facile',
                'specialties' => 'Socca, pissaladière, pan bagnat, salade niçoise',
                'start_point' => 'Place Masséna',
                'points' => 'Place Masséna, Cours Saleya, Rue Droite, Cathédrale Sainte-Réparate, Promenade des Anglais',
                'image' => 'https://images.unsplash.com/photo-1501594907352-04cda38ebc29?auto=format&fit=crop&w=900&q=80'
            ],
            [
                'name' => 'Le Cœur Historique de Nice',
                'slug' => 'coeur-historique-nice',
                'description' => 'Découvrez les monuments emblématiques et les quartiers aux parfums de la Méditerranée avec des pauses gourmandes à chaque étape.',
                'duration' => '4 heures',
                'price' => 49,
                'difficulty' => 'Moyenne',
                'specialties' => 'Ravioli niçois, ratatouille, tarte tropézienne, glace artisanale',
                'start_point' => 'Cours Saleya',
                'points' => 'Cours Saleya, Château de la Montagne, Place Rossetti, Rue de l’Hospitalité, Vieux Nice',
                'image' => 'https://images.unsplash.com/photo-1491553895911-0055eca6402d?auto=format&fit=crop&w=900&q=80'
            ],
            [
                'name' => 'Nice en Lumière',
                'slug' => 'nice-en-lumiere',
                'description' => 'Un circuit romantique et gourmand pour savourer lorsque le soleil tombe, entre monuments illuminés et spécialités locales de la région.',
                'duration' => '2h30',
                'price' => 34,
                'difficulty' => 'Facile',
                'specialties' => 'Bouchons niçois, légumes du marché, nougat, anisette',
                'start_point' => 'Promenade des Anglais',
                'points' => 'Promenade des Anglais, Parc Phoenix, Place Masséna, Rue de France, Quai des États-Unis',
                'image' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=900&q=80'
            ]
        ];

        $insertSql = 'INSERT INTO circuits (name, slug, description, duration, price, difficulty, specialties, start_point, points, image) VALUES (:name, :slug, :description, :duration, :price, :difficulty, :specialties, :start_point, :points, :image)';
        $stmt = $pdo->prepare($insertSql);

        foreach ($circuits as $circuit) {
            $stmt->execute($circuit);
        }
    }

    $adminExists = $pdo->query("SELECT COUNT(*) FROM users WHERE email = 'admin@yumtreep.fr'")->fetchColumn();
    if ((int) $adminExists === 0) {
        $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $pdo->exec("INSERT INTO users (fullname, email, password, phone, city, is_admin) VALUES ('Administrateur YumTrip', 'admin@yumtreep.fr', '$adminPassword', '0600000000', 'Nice', 1)");
    }

    return $pdo;
}

function get_circuits(PDO $pdo): array
{
    $stmt = $pdo->query('SELECT * FROM circuits ORDER BY id ASC');
    return $stmt->fetchAll();
}

function get_circuit_by_slug(PDO $pdo, string $slug): ?array
{
    $stmt = $pdo->prepare('SELECT * FROM circuits WHERE slug = :slug LIMIT 1');
    $stmt->execute([':slug' => $slug]);
    $result = $stmt->fetch();

    return $result ?: null;
}

function get_orders_for_user(PDO $pdo, int $userId): array
{
    $stmt = $pdo->prepare(
        'SELECT o.*, c.name AS circuit_name, c.price AS circuit_price
         FROM orders o
         JOIN circuits c ON c.id = o.circuit_id
         WHERE o.user_id = :user_id
         ORDER BY o.created_at DESC'
    );
    $stmt->execute([':user_id' => $userId]);
    return $stmt->fetchAll();
}

function get_all_users(PDO $pdo): array
{
    $stmt = $pdo->query('SELECT * FROM users ORDER BY created_at DESC');
    return $stmt->fetchAll();
}

function get_all_orders(PDO $pdo): array
{
    $stmt = $pdo->prepare(
        'SELECT o.*, u.fullname, c.name AS circuit_name
         FROM orders o
         JOIN users u ON u.id = o.user_id
         JOIN circuits c ON c.id = o.circuit_id
         ORDER BY o.created_at DESC'
    );
    $stmt->execute();
    return $stmt->fetchAll();
}
