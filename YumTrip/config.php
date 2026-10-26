<?php
session_start();
date_default_timezone_set('Europe/Paris');

const APP_NAME = 'YumTrip';
const DB_PATH = __DIR__ . '/data/yumtreep.sqlite';
const BASE_URL = '/';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

function app_url(string $path = ''): string
{
    return BASE_URL . ltrim($path, '/');
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function is_logged_in(): bool
{
    return !empty($_SESSION['user_id']);
}

function current_user(): ?array
{
    if (!is_logged_in()) {
        return null;
    }

    return [
        'id' => (int) $_SESSION['user_id'],
        'fullname' => $_SESSION['user_fullname'] ?? '',
        'email' => $_SESSION['user_email'] ?? '',
        'is_admin' => (int) ($_SESSION['user_is_admin'] ?? 0),
    ];
}
