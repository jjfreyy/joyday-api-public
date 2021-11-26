<?php 
namespace App\Libraries;

use App\Models\M_Asset;
use App\Models\M_Barang_Keluar;

class C_Barang_Keluar {
  private $tasset;
  private $tbarang_keluar;

  private $barang_keluar;

  private $id_barang_keluar;
  private $no_keluar;
  private $id_pengurus;
  private $id_gudang;
  private $keterangan;
  private $barang_keluar1;

  function __construct() {
    $this->tasset = new M_Asset();
    $this->tbarang_keluar = new M_Barang_Keluar();

    $this->set_id_barang_keluar(func_get_arg(0));
    $this->set_no_keluar(func_get_arg(1));
    $this->set_id_pengurus(func_get_arg(2));
    $this->set_id_gudang(func_get_arg(3));
    $this->set_keterangan(func_get_arg(4));
    $this->set_barang_keluar1(func_get_arg(5));

    $this->set_barang_keluar();
  }

  function is_valid_barang_keluar() {
    $is_valid_id_barang_keluar = is_valid_number($this->get_id_barang_keluar(), "ID barang keluar", 2, false, true);
    $is_valid_no_keluar = is_valid_code($this->get_no_keluar(), "No. keluar", true);
    if ($is_valid_no_keluar[0]) {
      $is_valid_no_keluar = !empty($this->tbarang_keluar->read(1, "no_keluar", $this->no_keluar, $this->id_barang_keluar)) 
      ? [false, "No. keluar telah terdaftar.<br>"]
      : [true];
    }
    $is_valid_id_pengurus = empty($this->tbarang_keluar->read(1, "id_pengurus", $this->id_pengurus)) 
    ? [false, "Data pengurus tidak dapat ditemukan.<br>"]
    : [true];
    $is_valid_id_gudang = is_empty($this->id_barang_keluar) && empty($this->tbarang_keluar->read(1, "id_gudang", $this->id_gudang, $this->id_pengurus))
    ? [false, "Data gudang tidak dapat ditemukan.<br>"]
    : [true];
    $is_valid_keterangan = is_valid_str($this->get_keterangan(), "Keterangan", 200, true);
    
    if (!$is_valid_id_barang_keluar[0]) $errors["no_keluar"] = $is_valid_id_barang_keluar[1];
    if (!$is_valid_no_keluar[0]) $errors["no_keluar"] = $is_valid_no_keluar[1];
    if (!$is_valid_id_pengurus[0]) $errors["pengurus"] = $is_valid_id_pengurus[1];
    if (!$is_valid_id_gudang[0]) $errors["gudang"] = $is_valid_id_gudang[1];
    if (!$is_valid_keterangan[0]) $errors["keterangan"] = $is_valid_keterangan[1];

    if (isset($errors)) {
        return [false, $errors];
    } else {
        return [true];
    }
  }

  function is_valid_barang_keluar1($id_barang_keluar) {
    try {
      if (is_empty_array($this->barang_keluar1)) return [false, "Silakan isi data barang keluar.<br>"];
      
      for ($i = 0; $i < count($this->barang_keluar1); $i++) {
        $no = $i + 1;
        $asset = explode(";", $this->barang_keluar1[$i]);
        $id_asset = sanitize($asset[0]);
        // $id_pelanggan = sanitize($asset[3]);
        $id_pelanggan = 1;

        $data_asset = $this->tbarang_keluar->read(1, "id_asset", $id_asset, $id_barang_keluar);
        if (empty($data_asset)) return [false, "$no. Data asset tidak dapat ditemukan.<br>"];
        // if (empty($this->tbarang_keluar->read(1, "id_pelanggan", $id_pelanggan))) return [false, "$no. Data pelanggan tidak dapat ditemukan.<br>"];

        $update = $this->tasset->updates(2, $id_asset, [
          "id_gudang" => null,
          "id_pelanggan" => $id_pelanggan,
          "id_input" => $id_barang_keluar,
          "dari_input" => 1,
        ]);
        if ($update === 0) return [false, "$no. Gagal mengubah data asset.<br>"];
        $barang_keluar1 = [
          "id_barang_keluar" => $id_barang_keluar,
          "no" => $no,
          "id_asset" => $id_asset,
          "ke_id_pelanggan" => $id_pelanggan,
          "id_input_terakhir" => $data_asset[0]->id_input,
        ];
        $barang_keluar1_arr[] = $barang_keluar1;
      }

      return [true, $barang_keluar1_arr];
    } catch (\Exception $e) {
      return [false, \format_exception($e)];
    }
  }

  /** accessors and mutators */
  function get_barang_keluar() {
    return $this->barang_keluar;
  }

  function set_barang_keluar() {
    $this->barang_keluar = [
      $this->get_no_keluar(),
      $this->get_id_pengurus(),
      $this->get_id_gudang(),
      $this->get_keterangan(),
    ];
  }

  function get_id_barang_keluar() {
      return $this->id_barang_keluar;
  }

  function set_id_barang_keluar($id_barang_keluar) {
      $this->id_barang_keluar = is_empty($id_barang_keluar) ? null : $id_barang_keluar;
  }
  
  function get_no_keluar() {
      return $this->no_keluar;
  }

  function set_no_keluar($no_keluar) {
      $this->no_keluar = is_empty($no_keluar) ? null : "BK-" .substr("00000$no_keluar", -5);
  }

  function get_id_pengurus() {
      return $this->id_pengurus;
  }

  function set_id_pengurus($id_pengurus) {
      $this->id_pengurus = $id_pengurus;
  }

  function get_id_gudang() {
      return $this->id_gudang;
  }

  function set_id_gudang($id_gudang) {
      $this->id_gudang = $id_gudang;
  }

  function get_keterangan() {
    return $this->keterangan;
  }

  function set_keterangan($keterangan) {
      $this->keterangan = is_empty($keterangan) ? null : $keterangan;
  }

  function get_barang_keluar1() {
    return $this->barang_keluar1;
  }

  function set_barang_keluar1($barang_keluar1) {
    $this->barang_keluar1 = $barang_keluar1;
  }
}
