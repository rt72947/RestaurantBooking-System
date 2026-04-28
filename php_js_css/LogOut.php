<?php
session_start();

/**
 * 1. Fshi të gjitha të dhënat e session-it
 */
$_SESSION = [];
session_unset();

/**
 * 2. Fshi cookie të session-it (extra security)
 */
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

/**
 * 3. Shkatërro session-in
 */
session_destroy();

/**
 * 4. Mos lejo cache (parandalon back button)
 */
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

/**
 * 5. Redirect te login
 */
header("Location: LogIn.php");
exit;
?>