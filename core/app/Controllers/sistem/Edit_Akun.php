<?php
namespace App\Controllers\Sistem;

use App\Controllers\BaseController;
use App\Libraries\C_User;
use App\Models\M_User;

class Edit_Akun extends BaseController {
  private $tuser;

  function __construct() {
    $this->tuser = new M_User();
  }

  function fetch() {
    try {
      if (is_empty(get_get("fetch_user"))) throw new \Exception("fetch_user value not found!");
      
      $id_user = get_get("id_user");
      if (empty($this->tmenu->read(2, $id_user, "USR-E"))) \send_response("403", ["global" => "Anda tidak memiliki akses untuk mengedit akun."]);
      \send_response(200, $this->tuser->read(4, $id_user));
    } catch (\Exception $e) {
      \send_response(\format_exception($e));
    }
  }

  function save() {
    try {
      if (is_empty(get_post("save_user"))) throw new \Exception("save_user value not found!");

      $id_user = get_post("id_user");
      $nama_user = get_post("nama_user");
      $username = get_post("username");
      $password = get_post("password");
      $confirm_password = get_post("confirm_password");
      $no_hp = get_post("no_hp");
      $email = get_post("email");
      $keterangan = get_post("keterangan");
      if (empty($this->tmenu->read(2, $id_user, "USR-E"))) \send_response(403, ["global" => "Anda tidak memiliki akses untuk mengedit akun."]);
      $user = new C_User($id_user, $nama_user, $username, $password, $confirm_password, $no_hp, $email, $keterangan, null);
      $is_valid_user = $user->is_valid_user();
      if (!$is_valid_user[0]) \send_response(400, $is_valid_user[1]);
      $this->tutils->start();
      $this->tuser->put($user->get_user());
      $this->tutils->commit();

      $data = $this->tuser->read(1, $username)[0];
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
      $this->tutils->rollback("tuser");
      \send_500_response(\format_exception($e));      
    }
  }
}
