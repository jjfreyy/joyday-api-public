<?php
namespace App\Controllers\Sistem;

use App\Controllers\BaseController;
use App\Libraries\C_User;
use App\Models\M_User;

class Tambah_User extends BaseController {
  private $tuser;

  function __construct() {
    $this->tuser = new M_User();
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
      $id_level = get_post("id_level");

      if (empty($this->tmenu->read(2, $id_user, "USR-I"))) \send_response(403, ["global" => "Anda tidak memiliki akses untuk menambah user."]);
      $user = new C_User(null, $nama_user, $username, $password, $confirm_password, $no_hp, $email, $keterangan, $id_level);
      $is_valid_user = $user->is_valid_user();
      if (!$is_valid_user[0]) \send_response(400, $is_valid_user[1]);
      $this->tutils->start();
      $this->tuser->put($user->get_user());
      $this->tutils->commit();
      \send_response();
    } catch (\Exception $e) {
      $this->tutils->rollback("tuser");
      \send_500_response(\format_exception($e));      
    }
  }
}
