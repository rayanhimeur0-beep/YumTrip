<?php
require_once __DIR__ . '/../database.php';

function require_login(): void
{
    if (!is_logged_in()) {
        redirect(app_url('connexion.php'));
    }
}

function require_admin(): void
{
    require_login();
    $user = current_user();

    if (!$user || (int) $user['is_admin'] !== 1) {
        redirect(app_url('index.php'));
    }
}

function register_user(PDO $pdo, array $data): array
{
    $fullname = trim((string) $data['fullname']);
    $email = strtolower(trim((string) $data['email']));
    $password = (string) $data['password'];
    $phone = trim((string) ($data['phone'] ?? ''));
    $city = trim((string) ($data['city'] ?? ''));

    if ($fullname === '' || $email === '' || $password === '') {
        throw new InvalidArgumentException('Veuillez renseigner tous les champs obligatoires.');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new InvalidArgumentException('L’adresse e-mail est invalide.');
    }

    if (strlen($password) < 6) {
        throw new InvalidArgumentException('Le mot de passe doit contenir au moins 6 caractères.');
    }

    $exists = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
    $exists->execute([':email' => $email]);
    if ($exists->fetch()) {
        throw new InvalidArgumentException('Un compte existe déjà pour cet e-mail.');
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO users (fullname, email, password, phone, city) VALUES (:fullname, :email, :password, :phone, :city)');
    $stmt->execute([
        ':fullname' => $fullname,
        ':email' => $email,
        ':password' => $hash,
        ':phone' => $phone,
        ':city' => $city,
    ]);

    $userId = (int) $pdo->lastInsertId();
    return ['id' => $userId, 'fullname' => $fullname, 'email' => $email];
}

function login_user(PDO $pdo, string $email, string $password): array
{
    $email = strtolower(trim($email));
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        throw new InvalidArgumentException('Identifiants incorrects.');
    }

    $_SESSION['user_id'] = (int) $user['id'];
    $_SESSION['user_fullname'] = $user['fullname'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_is_admin'] = (int) $user['is_admin'];

    return $user;
}

function create_order(PDO $pdo, int $userId, int $circuitId, int $quantity = 1): array
{
    $circuit = $pdo->prepare('SELECT * FROM circuits WHERE id = :id LIMIT 1');
    $circuit->execute([':id' => $circuitId]);
    $item = $circuit->fetch();

    if (!$item) {
        throw new InvalidArgumentException('Circuit introuvable.');
    }

    $total = (float) $item['price'] * $quantity;
    $stmt = $pdo->prepare('INSERT INTO orders (user_id, circuit_id, quantity, total, status) VALUES (:user_id, :circuit_id, :quantity, :total, :status)');
    $stmt->execute([
        ':user_id' => $userId,
        ':circuit_id' => $circuitId,
        ':quantity' => $quantity,
        ':total' => $total,
        ':status' => 'en attente',
    ]);

    return ['order_id' => (int) $pdo->lastInsertId(), 'total' => $total];
}

function add_to_cart(int $circuitId): void
{
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if (!isset($_SESSION['cart'][$circuitId])) {
        $_SESSION['cart'][$circuitId] = 0;
    }

    $_SESSION['cart'][$circuitId]++;
}

function remove_from_cart(int $circuitId): void
{
    if (isset($_SESSION['cart'][$circuitId])) {
        unset($_SESSION['cart'][$circuitId]);
    }
}

function clear_cart(): void
{
    $_SESSION['cart'] = [];
}

function get_cart_items(PDO $pdo): array
{
    if (empty($_SESSION['cart'])) {
        return [];
    }

    $ids = array_keys($_SESSION['cart']);
    if ($ids === []) {
        return [];
    }

    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare('SELECT * FROM circuits WHERE id IN (' . $placeholders . ')');
    $stmt->execute($ids);
    $circuits = $stmt->fetchAll();

    $items = [];
    foreach ($circuits as $circuit) {
        $id = (int) $circuit['id'];
        $quantity = isset($_SESSION['cart'][$id]) ? (int) $_SESSION['cart'][$id] : 0;
        if ($quantity <= 0) {
            continue;
        }

        $items[] = [
            'id' => $id,
            'name' => $circuit['name'],
            'price' => (float) $circuit['price'],
            'quantity' => $quantity,
            'total' => (float) $circuit['price'] * $quantity,
            'image' => $circuit['image'],
        ];
    }

    return $items;
}

function get_cart_total(PDO $pdo): float
{
    $total = 0.0;
    foreach (get_cart_items($pdo) as $item) {
        $total += (float) $item['total'];
    }

    return $total;
}

function checkout_cart(PDO $pdo, int $userId): array
{
    $items = get_cart_items($pdo);
    if ($items === []) {
        throw new InvalidArgumentException('Votre panier est vide.');
    }

    $orders = [];
    foreach ($items as $item) {
        $orders[] = create_order($pdo, $userId, (int) $item['id'], (int) $item['quantity']);
    }

    clear_cart();
    return $orders;
}

