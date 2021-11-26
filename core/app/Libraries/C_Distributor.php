<?php
namespace App\Libraries;

use App\Models\M_Distributor;

class C_Distributor {
  private $tdistributor;

  private $distributor;

  private $id_distributor;
  private $kode_distributor;
  private $nama_distributor;
  private $alamat;
  private $no_hp;
  private $email;
  private $keterangan;

  function __construct() {
    $this->tdistributor = new M_Distributor();

    $this->set_id_distributor(func_get_arg(0));
    $this->set_kode_distributor(func_get_arg(1));
    $this->set_nama_distributor(func_get_arg(2));
    $this->set_alamat(func_get_arg(3));
    $this->set_no_hp(func_get_arg(4));
    $this->set_email(func_get_arg(5));
    $this->set_keterangan(func_get_arg(6));

    $this->set_distributor();
  }

  function is_valid_distributor() {
      $is_valid_id_distributor = is_valid_number($this->get_id_distributor(), "ID distributor", 2, false, true);
      $is_valid_kode_distributor = is_valid_code($this->get_kode_distributor(), "Kode distributor", true);
      if ($is_valid_kode_distributor[0] && !is_empty($this->kode_distributor)) {
          $is_valid_kode_distributor = !empty($this->tdistributor->read(1, $this->kode_distributor, $this->id_distributor))
          ? [false, "Kode distributor telah terdaftar.<br>"]
          : [true]; 
      }
      $is_valid_nama_distributor = is_valid_name($this->get_nama_distributor(), "Nama distributor", 100);
      $is_valid_alamat = is_valid_str($this->get_alamat(), "Alamat", 200, false);
      $is_valid_no_hp = is_valid_phone($this->get_no_hp(), "No. hp", false);
      $is_valid_email = is_valid_email($this->get_email(), "Email distributor", true);
      $is_valid_keterangan = is_valid_str($this->get_keterangan(), "Keterangan", 200, true);
      
      if (!$is_valid_id_distributor[0]) $errors["kode_distributor"] = $is_valid_id_distributor[1];
      if (!$is_valid_kode_distributor[0]) $errors["kode_distributor"] = $is_valid_kode_distributor[1];
      if (!$is_valid_nama_distributor[0]) $errors["nama_distributor"] = $is_valid_nama_distributor[1];
      if (!$is_valid_alamat[0]) $errors["alamat"] = $is_valid_alamat[1];
      if (!$is_valid_no_hp[0]) $errors["no_hp"] = $is_valid_no_hp[1];
      if (!$is_valid_email[0]) $errors["email"] = $is_valid_email[1];
      if (!$is_valid_keterangan[0]) $errors["keterangan"] = $is_valid_keterangan[1];

      if (isset($errors)) {
          return [false, $errors];
      } else {
          return [true];
      }
  }

  /** accessors and mutators */
  function get_distributor() {
      return $this->distributor;
  }

  function set_distributor() {
      $this->distributor[] = $this->get_id_distributor();
      $this->distributor[] = $this->get_kode_distributor();
      $this->distributor[] = $this->get_nama_distributor();
      $this->distributor[] = $this->get_alamat();
      $this->distributor[] = $this->get_no_hp();
      $this->distributor[] = $this->get_email();
      $this->distributor[] = $this->get_keterangan();
  }

  function get_id_distributor() {
      return $this->id_distributor;
  }

  function set_id_distributor($id_distributor) {
      $this->id_distributor = is_empty($id_distributor) ? null : $id_distributor;
  }

  function get_kode_distributor() {
      return $this->kode_distributor;
  }

  function set_kode_distributor($kode_distributor) {
      $this->kode_distributor = is_empty($kode_distributor) ? null : "DIS-" .substr("000$kode_distributor", -3);
  }

  function get_nama_distributor() {
      return $this->nama_distributor;
  }

  function set_nama_distributor($nama_distributor) {
      $this->nama_distributor = $nama_distributor;
  }

  function get_alamat() {
    return $this->alamat;
  }

  function set_alamat($alamat) {
      $this->alamat = $alamat;
  }

  function get_no_hp() {
      return $this->no_hp;
  }

  function set_no_hp($no_hp) {
      $this->no_hp = $no_hp;
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
}
