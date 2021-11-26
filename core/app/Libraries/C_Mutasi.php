<?php 
namespace App\Libraries;

use App\Models\M_Asset;
use App\Models\M_Mutasi;

class C_Mutasi {
  private $tasset;
  private $tmutasi;

  private $mutasi;

  private $id_mutasi;
  private $no_mutasi;
  private $id_user;
  private $dari_id_pelanggan;
  private $keterangan;
  private $mutasi1;

  function __construct() {
    $this->tasset = new M_Asset();
    $this->tmutasi = new M_Mutasi();

    $this->set_id_mutasi(func_get_arg(0));
    $this->set_no_mutasi(func_get_arg(1));
    $this->set_id_user(func_get_arg(2));
    $this->set_dari_id_pelanggan(func_get_arg(3));
    $this->set_keterangan(func_get_arg(4));
    $this->set_mutasi1(func_get_arg(5));

    $this->set_mutasi();
  }

  function is_valid_mutasi() {
    $is_valid_id_mutasi = is_valid_number($this->get_id_mutasi(), "ID mutasi", 2, false, true);

    $is_valid_no_mutasi = is_valid_code($this->get_no_mutasi(), "No. mutasi", true);
    if ($is_valid_no_mutasi[0] && !is_empty($this->no_mutasi)) {
      $is_valid_no_mutasi = !empty($this->tmutasi->read(1, "no_mutasi", $this->no_mutasi, $this->id_mutasi))
      ? [false, "No. mutasi telah terdaftar.<br>"] : [true]; 
    }

    $is_valid_id_user = empty($this->tmutasi->read(1, "id_user", $this->id_user))
    ? [false, "Data user tidak dapat ditemukan.<br>"] : [true];

    $is_valid_dari_id_pelanggan = empty($this->tmutasi->read(1, "id_pelanggan", $this->dari_id_pelanggan))
    ? [false, "Data dari pelanggan tidak dapat ditemukan.<br>"] : [true];

    $is_valid_keterangan = is_valid_str($this->get_keterangan(), "Keterangan", 200, true);

    if (!$is_valid_id_mutasi[0]) $errors["no_mutasi"] = $is_valid_id_barang_masuk[1];
    if (!$is_valid_no_mutasi[0]) $errors["no_mutasi"] = $is_valid_no_mutasi[1];
    if (!$is_valid_id_user[0]) $errors["id_user"] = $is_valid_id_user[1];
    if (!$is_valid_dari_id_pelanggan[0]) $errors["dari_id_pelanggan"] = $is_valid_dari_id_pelanggan[1];
    if (!$is_valid_keterangan[0]) $errors["keterangan"] = $is_valid_keterangan[1];

    if (isset($errors)) {
        return [false, $errors];
    } else {
        return [true];
    }
  }

  function is_valid_mutasi1($id_mutasi) {
    try {
      if (\is_empty_array($this->mutasi1)) return [false, "Silakan isi data asset.<br>"];

      for ($i = 0; $i < count($this->mutasi1); $i++) {
        $no = $i + 1;
        $asset = explode(";", $this->mutasi1[$i]);
        $id_asset = sanitize($asset[0]);
        $no_surat_kontrak = if_empty_then(sanitize($asset[2]), null);
        $id_pelanggan = sanitize($asset[3]);

        $data_asset = $this->tmutasi->read(1, "id_asset", $id_asset, $this->dari_id_pelanggan);
        if (empty($data_asset)) return [false, "$no. Data asset tidak dapat ditemukan.<br>"];
        if (empty($this->tmutasi->read(1, "id_pelanggan", $id_pelanggan))) return [false, "$no. Data pelanggan tidak dapat ditemukan.<br>"];
        if (!empty($this->tasset->read(1, "no_surat_kontrak", $no_surat_kontrak, $id_asset))) return [false, "$no. No. surat kontrak telah terdaftar.<br>"];

        $update = $this->tasset->updates(2, $id_asset, [
          "id_pelanggan" => $id_pelanggan,
          "id_input" => $id_mutasi,
          "dari_input" => 2,
          "no_surat_kontrak" => $no_surat_kontrak,
        ]);
        if ($update === 0) return [false, "$no. Gagal mengubah data asset.<br>"];
        $mutasi1[] = [
          "id_mutasi" => $id_mutasi, 
          "no" => $no, 
          "id_asset" => $id_asset, 
          "ke_id_pelanggan" => $id_pelanggan, 
          "id_input_terakhir" => $data_asset[0]->id_input,
          "dari_input" => $data_asset[0]->dari_input,
        ];
      }

      return [true, $mutasi1];
    } catch (\Exception $e) {
      return [false, \format_exception($e)];
    }
  }

  /** accessors and mutators */
  function get_mutasi() {
    return $this->mutasi;
  }

  function set_mutasi() {
    $this->mutasi = [
      $this->get_no_mutasi(),
      $this->get_id_user(),
      $this->get_dari_id_pelanggan(),
      $this->get_keterangan(),
    ];
  }

  function get_id_mutasi() {
      return $this->id_mutasi;
  }

  function set_id_mutasi($id_mutasi) {
      $this->id_mutasi = is_empty($id_mutasi) ? null : $id_mutasi;
  }

  function get_no_mutasi() {
      return $this->no_mutasi;
  }

  function set_no_mutasi($no_mutasi) {
      $this->no_mutasi = is_empty($no_mutasi) ? null : "MUT-" .substr("00000$no_mutasi", -5);
  }

  function get_id_user() {
      return $this->id_user;
  }

  function set_id_user($id_user) {
      $this->id_user = $id_user;
  }
  
  function get_dari_id_pelanggan() {
      return $this->dari_id_pelanggan;
  }

  function set_dari_id_pelanggan($dari_id_pelanggan) {
      $this->dari_id_pelanggan = $dari_id_pelanggan;
  }

  function get_keterangan() {
    return $this->keterangan;
  }

  function set_keterangan($keterangan) {
      $this->keterangan = is_empty($keterangan) ? null : $keterangan;
  }

  function get_mutasi1() {
    return $this->mutasi1;
  }

  function set_mutasi1($mutasi1) {
    $this->mutasi1 = $mutasi1;
  }
}
