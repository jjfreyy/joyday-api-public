<?php
namespace App\Controllers;

use App\Models\M_User;
use App\Models\M_Menu;

class Authentication extends BaseController {
    private $tuser;

    function __construct() {
        $this->tuser = new M_User();
    }

    function check_privileges() {
        try {
            if (is_empty($this->request->getPost("check_privileges"))) throw new \Exception("check_privileges value not found!");
            $id_user = get_post("id_user");
            $kode_akses = get_post("kode_akses");
            if (empty($this->tmenu->read(2, $id_user, $kode_akses))) send_response(403, ["message" => "Maaf, anda tidak memiliki hakakses."]);
            \send_response();
        } catch (\Exception $e) {
            \send_500_response(\format_exception($e));
        }
    }

    function check_session() {
        try {
            if (is_empty($this->request->getPost("check_session"))) throw new \Exception("check_session value not found!");
            $id_user = get_post("id_user");
            $username = get_post("username");
            $data = $this->tuser->read(2, $id_user);
            if (empty($data)) \send_response(403, ["message" => "User $username tidak dapat ditemukan."]);
            \send_response();
        } catch (\Exception $e) {
            \send_500_response(\format_exception($e));
        }
    }

    function login() {
        try {
            if (is_empty($this->request->getPost("login"))) throw new \Exception("login value not found!");
            $username = get_post("username");
            $password = get_post("password");
            
            $data = $this->tuser->read(1, $username)[0];
            if (empty($data)) \send_response(403, ["message" => "Username / Password salah."]);
            if (!\password_verify(\wrap_password($password), $data->password)) \send_response(403, ["message" => "Username / Password salah."]);
            
            $data = [
                "id_user" => $data->id_user,
                "nama_user" => $data->nama_user,
                "username" => $data->username,
                "no_hp" => $data->no_hp,
                "email" => $data->email,
                "id_level" => $data->id_level,
                "nama_level" => $data->nama_level,
            ];
            \send_response($data);
        } catch (\Exception $e) {
            \send_500_response(\format_exception($e));
        }
    }

}