<?php

session_start();

require_once './globus_config.php';

unset($_SESSION["eppn"]);
unset($_SESSION["shib-session-id"]);
unset($_SESSION["globus_eppn"]);
unset($_SESSION["globus_shib-session-id"]);

if (!isset($_SESSION['globus_logout_url'])) {
    $endpoint = 'https://auth.globus.org/v2/web/logout';
    $params = [
        'client_id' => $globus_client_id,
        'redirect_uri' => $globus_redirect_uri,
        'redirect_name' => $application_name
    ];

    $_SESSION['globus_logout_url'] = $endpoint . '?' . http_build_query($params);
}

$_SESSION['globus_logout'] = true;

$logout_url = $_SESSION['globus_logout_url'];
header("Location: $logout_url");
