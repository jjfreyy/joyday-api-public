<?php
namespace App\Libraries;

use App\Models\M_User;

class C_User {
  private $tuser;

  private $id_user;
  private $nama_user;
  private $username;
  private $password;
  private $confirm_password;
  private $no_hp;
  private $email;
  private $keterangan;
  private $id_level;

  private $user;

  function __construct() {
    $this->tuser = new M_User();

    $this->set_id_user(func_get_arg(0));
    $this->set_nama_user(func_get_arg(1));
    $this->set_username(func_get_arg(2));
    $this->set_password(func_get_arg(3));
    $this->set_confirm_password(func_get_arg(4));
    $this->set_no_hp(func_get_arg(5));
    $this->set_email(func_get_arg(6));
    $this->set_keterangan(func_get_arg(7));
    $this->set_id_level(func_get_arg(8));

    $this->set_user();
  }

  function is_valid_user() {
    $is_valid_id_user = is_valid_number($this->id_user, "ID user", 2, false, true);
    $is_valid_nama_user = \is_valid_name($this->nama_user, "Nama user", 100);
    $is_valid_username = \is_valid_username($this->username, "Username");
    if ($is_valid_username[0]) {
      $is_valid_username = !empty($this->tuser->read(3, "username", $this->username, $this->id_user)) 
      ? [false, "Username telah terdaftar.<br>"] : [true];
    }
    $is_valid_password = $this->password === $this->confirm_password ? [true] : [false, "Password dan konfirmasi password tidak sama.<br>"];
    if ($is_valid_password[0]) {
      if (is_empty($this->password)) {
        $is_valid_password = is_empty($this->id_user) ? [false, "Silakan masukkan password.<br>"] : [true];
      } else if (strlen($this->password) < 3 && strlen($this->password) > 50) {
        $is_valid_password = [false, "Panjang password minimal 3 karakter. maksimal 50 karakter.<br>"];
      }
    }
    $is_valid_no_hp = \is_valid_phone($this->no_hp, "No. hp", true);
    if ($is_valid_no_hp[0] && !is_empty($this->no_hp)) {
      $is_valid_no_hp = !empty($this->tuser->read(3, "no_hp", $this->no_hp, $this->id_user)) 
      ? [false, "No. HP telah terdaftar.<br>"] : [true];
    }
    $is_valid_email = \is_valid_email($this->email, "Email", true);
    if ($is_valid_email[0] && !is_empty($this->email)) {
      $is_valid_email = !empty($this->tuser->read(3, "email", $this->email, $this->id_user)) ? [false, "Email telah terdaftar.<br>"] : [true];
    }
    $is_valid_keterangan = is_valid_str($this->keterangan, "Keterangan", 200, true);
    if (is_empty($this->id_user)) {
      $is_valid_id_level = empty($this->tuser->read(3, "id_level", $this->id_level)) 
      ? [false, "Level user tidak dapat ditemukan.<br>"] : [true];
    } else {
      $is_valid_id_level = [true];
    }

    if (!$is_valid_id_user[0]) $errors["kode_user"] = $is_valid_id_user[1];
    if (!$is_valid_nama_user[0]) $errors["nama_user"] = $is_valid_nama_user[1];
    if (!$is_valid_username[0]) $errors["username"] = $is_valid_username[1];
    if (!$is_valid_password[0]) $errors["password"] = $is_valid_password[1];
    if (!$is_valid_no_hp[0]) $errors["no_hp"] = $is_valid_no_hp[1];
    if (!$is_valid_email[0]) $errors["email"] = $is_valid_email[1];
    if (!$is_valid_keterangan[0]) $errors["keterangan"] = $is_valid_keterangan[1];
    if (!$is_valid_id_level[0]) $errors["id_level"] = $is_valid_id_level[1];

    if (isset($errors)) {
      return [false, $errors];
    } else {
      return [true];
    }
  }

  /** accessors and mutators */
  function get_user() {
    return $this->user;
  }

  function set_user() {
    $this->user = [
      $this->id_user, 
      $this->nama_user, 
      $this->username, 
      is_empty($this->password) ? null : password_hash(md5(env("API_TOKEN")). "." .md5($this->password), PASSWORD_BCRYPT), 
      null,
      $this->no_hp, 
      $this->email, 
      $this->keterangan, 
      $this->id_level,
    ];
  }

  function get_id_user() {
    return $this->id_user;
  }

  function set_id_user($id_user) {
    $this->id_user = $id_user;
  }

  function get_nama_user() {
    return $this->nama_user;
  }

  function set_nama_user($nama_user) {
    $this->nama_user = $nama_user;
  }

  function get_username() {
    return $this->username;
  }

  function set_username($username) {
    $this->username = $username;
  }

  function get_password() {
    return $this->password;
  }

  function set_password($password) {
    $this->password = is_empty($password) ? null : $password;
  }

  function get_confirm_password() {
    return $this->confirm_password;
  }

  function set_confirm_password($confirm_password) {
    $this->confirm_password = is_empty($confirm_password) ? null : $confirm_password;
  }

  function get_no_hp() {
    return $this->no_hp;
  }

  function set_no_hp($no_hp) {
    $this->no_hp = is_empty($no_hp) ? null : $no_hp;
  }

  function get_email() {
    return $this->email;
  }

  function set_email($email) {
    $this->email = is_empty($email) ? null : $email;
  }

  function get_keterangan() {
    return $this->keterangan;
  }

  function set_keterangan($keterangan) {
    $this->keterangan = is_empty($keterangan) ? null : $keterangan;
  }

  function get_id_level() {
    return $this->id_level;
  }

  function set_id_level($id_level) {
    $this->id_level = is_empty($this->id_user) ? $id_level : null;
  }
}
