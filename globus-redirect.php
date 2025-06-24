<?php

session_start();

require_once './globus_config.php';

if (!isset($_SESSION['globus_home_url'])) {
    $script = filter_input(INPUT_SERVER, 'SCRIPT_FILENAME', FILTER_SANITIZE_STRING);
    $https = filter_input(INPUT_SERVER, 'HTTPS', FILTER_SANITIZE_STRING);
    $hostname = filter_input(INPUT_SERVER, 'SERVER_NAME', FILTER_SANITIZE_STRING);
    $requestUri = filter_input(INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_STRING);

    $scriptFilename = basename($script);
    $path = str_replace($scriptFilename, '', $requestUri);
    $scheme = (isset($https) && $https === 'on') ? 'https' : 'http';
    $url = "$scheme://$hostname$path";
    $redir_url = rtrim($url, '/');

    $_SESSION['globus_home_url'] = $redir_url;
}

$code = filter_input(INPUT_GET, 'code', FILTER_UNSAFE_RAW);
if (isset($code)) {
    $token_url = 'https://auth.globus.org/v2/oauth2/token';
    $post_fields = [
        'grant_type' => 'authorization_code',
        'code' => $code,
        'redirect_uri' => $globus_redirect_uri,
        'client_id' => $globus_client_id,
        'client_secret' => $globus_client_secret
    ];

    $ch = curl_init($token_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_fields));

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code == 200) {
        $json_response = json_decode($response, true);
        $access_token = $json_response['access_token'];

        $api_url = 'https://auth.globus.org/v2/oauth2/userinfo';
        $headers = [
            'Authorization: Bearer ' . $access_token,
            'Content-Type: application/json',
        ];

        // Make the request to the Globus API to get the user info
        $ch = curl_init($api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $user_info_response = curl_exec($ch);
        curl_close($ch);

        $user_info = json_decode($user_info_response, true);

        $_SESSION["eppn"] = $user_info['preferred_username'];
        $_SESSION["shib-session-id"] = $code;

        // for PM cell headers
        $_SESSION["globus_eppn"] = $user_info['preferred_username'];
        $_SESSION["globus_shib-session-id"] = $code;

        $redir_url = $_SESSION['globus_home_url'] . '/';
        $redir_url = $redir_url . 'globus-acs.php';
        header("Location: $redir_url");
    } else {
        $_SESSION['error_msg'] = json_decode($response, true);
    }
} elseif (isset($_SESSION['globus_logout'])) {
    unset($_SESSION['globus_logout']);

    $redir_url = $_SESSION['globus_home_url'];
    header("Location: $redir_url");
} else {
    if (!isset($_SESSION['globus_login_url'])) {
        $endpoint = 'https://auth.globus.org/v2/oauth2/authorize';
        $params = [
            'client_id' => $globus_client_id,
            'redirect_uri' => $globus_redirect_uri,
            'scope' => 'openid profile email urn:globus:auth:scope:transfer.api.globus.org:all',
            'state' => '_default',
            'response_type' => 'code',
            'access_type' => 'online'
        ];

        $_SESSION['globus_login_url'] = $endpoint . '?' . http_build_query($params);
    }

    $login_url = $_SESSION['globus_login_url'];
    header("Location: $login_url");
}
