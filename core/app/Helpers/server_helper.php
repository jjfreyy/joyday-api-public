<?php

function get_autocomplete_limit() {
    return 5;
}

function get_datalist_limit() {
    return 15;
}

function is_authenticate() {
    if (!(
        (isset($_SERVER["PHP_AUTH_USER"]) && $_SERVER["PHP_AUTH_USER"] === env("API_KEY")) && 
        (isset($_SERVER["PHP_AUTH_PW"]) && $_SERVER["PHP_AUTH_PW"] === env("API_SECRET"))
    )) {
        header("WWW-Authenticate: Basic realm='Restricted Area'");
        header("HTTP/1.0 401 Unauthorized");
        send_response(401, ["message" => "Not authorized " .$_SERVER["PHP_AUTH_USER"]]);
        die();
    }
}

function send_500_response($error = "") {
    return send_response(500, ["message" => is_empty($error) || env("CI_ENVIRONMENT") === "production" ? "Terjadi kesalahan internal server." : $error]);
}

function send_response() {
    $status_code = 200;
    $body = "";

    switch (func_num_args()) {
        case 1: $body = func_get_arg(0); break;
        case 2:
            $status_code = func_get_arg(0);
            $body = func_get_arg(1);
        break;
    }

    $response = \Config\Services::response();
    $response->setStatusCode($status_code);
    $response->setBody(json_encode($body));
    $response->send();
    die();
}

function wrap_password($password) {
    return md5(env("API_TOKEN")). "." .md5($password);
}
