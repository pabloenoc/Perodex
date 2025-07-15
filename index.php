<?php

session_start();

/**
 * config.php - Discord App credentials
 */

define('DISCORD_CLIENT_ID', '<client id>');
define('DISCORD_CLIENT_SECRET', '<client secret>');
define('DISCORD_REDIRECT_URI', 'http://localhost:8000/index.php'); 
define('DISCORD_OAUTH_SCOPE', 'identify guilds');

/**
 * logout.php - End session
 */

if (isset($_GET['logout'])) {
    $_SESSION = array();
    session_destroy();
    header('Location: index.php');
    exit;
}

/**
 * index.php - Main file
 */

if (isset($_SESSION['guilds'])) {
    echo "<h1>Welcome, " . htmlspecialchars($_SESSION['user']['username']) . "!</h1>";
    echo "<h2>Your Servers:</h2><ul>";
    foreach ($_SESSION['guilds'] as $guild) {
        echo "<li>" . htmlspecialchars($guild['name']) . "</li>";
    }
    echo "</ul><a href='?logout=true'>Logout</a>";
    exit;
}

$params = [
    'client_id' => DISCORD_CLIENT_ID,
    'redirect_uri' => DISCORD_REDIRECT_URI,
    'response_type' => 'code',
    'scope' => DISCORD_OAUTH_SCOPE
];

$authorizeUrl = 'https://discord.com/api/oauth2/authorize?' . http_build_query($params);

/**
 * callback.php - Handle Discord response 
 */

function perodex_fetch_token($code) {
    $data = [
        'grant_type' => 'authorization_code',
        'code' => $code,
        'redirect_uri' => DISCORD_REDIRECT_URI,
        'client_id' => DISCORD_CLIENT_ID,
        'client_secret' => DISCORD_CLIENT_SECRET
    ];

    $ch = curl_init('https://discord.com/api/oauth2/token');
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
        CURLOPT_POSTFIELDS => http_build_query($data)
    ]);

    $response = curl_exec($ch);

     if ($response === false) {
        $error = curl_error($ch);
        file_put_contents('debug.log', 'cURL error: ' . $error);
        curl_close($ch);
        return [];
    }

    curl_close($ch);
    file_put_contents('debug.log', $response);
    return json_decode($response, true);
}

function perodex_api_request($url, $token) {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ["Authorization: Bearer $token"]
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

if (isset($_GET['code'])) {
    $token_response = perodex_fetch_token($_GET['code']);

    if (!isset($token_response['access_token'])) {
        die('Failed to obtain access token');
    }

    $access_token = $token_response['access_token'];

    // Fetch user info

    $user = perodex_api_request('https://discord.com/api/users/@me', $access_token);
    $guilds = perodex_api_request('https://discord.com/api/users/@me/guilds', $access_token);

    $_SESSION['user'] = $user;
    $_SESSION['guilds'] = $guilds;

    header('Location: index.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perodex</title>
    <link rel="stylesheet" href="/assets/css/main.css">
</head>
<body>
    <nav class="navbar">
        <h1 class="navbar__title"><a href="/">perodex</a></h1>
    </nav>
    <main class="container">
        <section class="hero">
            <p class="hero__description">A digital assets exchange platform</p>
            <p class="hero__tagline">All your trades. One dashboard.</p>
            <a href="<?= $authorizeUrl ?>" class="hero__login">
                <img src="https://external-content.duckduckgo.com/iu/?u=https%3A%2F%2Frepository-images.githubusercontent.com%2F486722660%2F7ef9a610-b65a-48b7-ba4a-f4da081b90ad&f=1&nofb=1&ipt=d89c4719829bbb0d6ed3eaa4e08c305b4e3df76da58a47aaccf7e522f182c994" alt="Discord Logo">
                <span>Login with Discord</span>
            </a>
        </section>
    </main>
    <footer class="footer">
        <ul class="m-0 p-0 footer__links">
            <li class="m-0 p-0"><a href="/">About</a></li>
            <li class="m-0 p-0"><a href="/">Privacy Policy</a></li>
            <li class="m-0 p-0"><a href="/">Terms of Service</a></li>
            <li class="m-0 p-0"><a href="/">Contact</a></li>
            <li class="m-0 p-0"><a href="/">Changelog</a></li>
        </ul>
        <p class="footer__copyright"><small>&copy; <?= date('Y') ?> Perodex. All rights reserved.</small></p>
    </footer>
</body>
</html>


