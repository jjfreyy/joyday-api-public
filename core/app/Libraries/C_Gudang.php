<?php 
namespace App\Libraries;

use App\Models\M_Gudang;

class C_Gudang {
  private $tgudang;

  private $gudang;

  private $id_gudang;
  private $id_kepala_gudang;
  private $kode_gudang;
  private $nama_gudang;
  private $keterangan;

  function __construct() {
    $this->tgudang = new M_Gudang();

    $this->set_id_gudang(func_get_arg(0));
    $this->set_id_kepala_gudang(func_get_arg(1));
    $this->set_kode_gudang(func_get_arg(2));
    $this->set_nama_gudang(func_get_arg(3));
    $this->set_keterangan(func_get_arg(4));

    $this->set_gudang();
  }

  function is_valid_gudang() {
      $is_valid_id_gudang = is_valid_number($this->get_id_gudang(), "ID gudang", 2, false, true);
      $is_valid_id_kepala_gudang = !is_empty($this->id_kepala_gudang) && empty($this->tgudang->read(1, "id_kepala_gudang", $this->id_kepala_gudang))
      ? [false, "Data kepala gudang tidak dapat ditemukan.<br>"]
      : [true];
      $is_valid_kode_gudang = is_valid_code($this->get_kode_gudang(), "Kode gudang", true);
      if ($is_valid_kode_gudang[0] && !is_empty($this->kode_gudang)) {
          $is_valid_kode_gudang = !empty($this->tgudang->read(1, "kode_gudang", $this->kode_gudang, $this->id_gudang))
          ? [false, "Kode gudang belum terdaftar.<br>"]
          : [true];
      }
      $is_valid_nama_gudang = is_valid_name($this->get_nama_gudang(), "Nama gudang", 100);
      $is_valid_keterangan = is_valid_str($this->get_keterangan(), "Keterangan", 200, true);
      
      if (!$is_valid_id_gudang[0]) $errors["kode_gudang"] = $is_valid_id_gudang[1];
      if (!$is_valid_id_kepala_gudang[0]) $errors["kepala_gudang"] = $is_valid_id_kepala_gudang[1];
      if (!$is_valid_kode_gudang[0]) $errors["kode_gudang"] = $is_valid_kode_gudang[1];
      if (!$is_valid_nama_gudang[0]) $errors["nama_gudang"] = $is_valid_nama_gudang[1];
      if (!$is_valid_keterangan[0]) $errors["keterangan"] = $is_valid_keterangan[1];

      if (isset($errors)) {
          return [false, $errors];
      } else {
          return [true];
      }
  }

  /** accessors and mutators */
  function get_gudang() {
      return $this->gudang;
  }

  function set_gudang() {
      $this->gudang[] = $this->get_id_gudang();
      $this->gudang[] = $this->get_id_kepala_gudang();
      $this->gudang[] = $this->get_kode_gudang();
      $this->gudang[] = $this->get_nama_gudang();
      $this->gudang[] = $this->get_keterangan();
  }

  function get_id_gudang() {
      return $this->id_gudang;
  }

  function set_id_gudang($id_gudang) {
      $this->id_gudang = is_empty($id_gudang) ? null : $id_gudang;
  }
  
  function get_id_kepala_gudang() {
      return $this->id_kepala_gudang;
  }

  function set_id_kepala_gudang($id_kepala_gudang) {
      $this->id_kepala_gudang = is_empty($id_kepala_gudang) ? null : $id_kepala_gudang;
  }

  function get_kode_gudang() {
      return $this->kode_gudang;
  }

  function set_kode_gudang($kode_gudang) {
      $this->kode_gudang = is_empty($kode_gudang) ? null : "GUD-" .substr("000$kode_gudang", -3);
  }

  function get_nama_gudang() {
      return $this->nama_gudang;
  }

  function set_nama_gudang($nama_gudang) {
      $this->nama_gudang = $nama_gudang;
  }

  function get_keterangan() {
    return $this->keterangan;
  }

  function set_keterangan($keterangan) {
      $this->keterangan = is_empty($keterangan) ? null : $keterangan;
  }
}
