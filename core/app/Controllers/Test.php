<?php
namespace App\Controllers;
use App\Models\M_Menu;
use App\Models\M_Utils;
use App\Libraries\C_Test;

class Test extends BaseController {
    function index() {
        try {
            $tutils = new M_Utils();
            // \send_response(env("API_SECRET"));
            // \send_response($_SERVER["PHP_AUTH_PW"]);
            // send_response((isset($_SERVER["PHP_AUTH_USER"]) && $_SERVER["PHP_AUTH_USER"] === env("API_KEY")) && 
            // (isset($_SERVER["PHP_AUTH_PW"]) && $_SERVER["PHP_AUTH_PW"] === env("API_SECRET")));
        } catch (\Exception $e) {
            // \debug_exception($e);
            \send_500_response(\format_exception($e));
        }
    }

    function test(&$h, $i = 0) {
        if ($i === 2) return $h;
        $h .= " hello";
        return $this->test($h, ++$i);
    }
}