<?php
namespace App\Libraries;

use App\Models\M_Asset;

class C_Asset {
  private $tasset;

  private $asset;

  private $id_asset;
  private $id_barang;
  private $qr_code;
  private $serial_number;
  private $tanggal_akuisisi_asset;
  
  private $no_surat_kontrak;
  private $tanggal_berakhir_kontrak;
  private $id_kepemilikan;
  private $keterangan;
  private $id_gudang;

  private $id_pelanggan;
  private $sta;
  private $alasan;

  function __construct() {
    $this->tasset = new M_Asset();

    $this->set_id_asset(\func_get_arg(0));
    $this->set_id_barang(\func_get_arg(1));
    $this->set_qr_code(\func_get_arg(2));
    $this->set_serial_number(\func_get_arg(3));
    $this->set_tanggal_akuisisi_asset(\func_get_arg(4));
    
    $this->set_no_surat_kontrak(\func_get_arg(5));
    $this->set_tanggal_berakhir_kontrak(\func_get_arg(6));
    $this->set_id_kepemilikan(func_get_arg(7));
    $this->set_keterangan(\func_get_arg(8));
    $this->set_id_gudang(\func_get_arg(9));
    
    $this->set_id_pelanggan(\func_get_arg(10));
    $this->set_sta(\func_get_arg(11));
    $this->set_alasan(\func_get_arg(12));

    $this->set_asset();
  }

  function is_valid_asset() {
    $is_valid_id_asset = is_valid_number($this->get_id_asset(), "ID Asset", 2, false, true);

    if (!is_empty($this->id_asset)) $is_valid_id_barang = [true];
    else {
      $is_valid_id_barang = empty($this->tasset->read(1, "id_barang", $this->id_barang)) 
      ? [false, "Data brang tidak dapat ditemukan.<br>"]
      : [true];
    } 

    $is_valid_qr_code = is_valid_str($this->get_qr_code(), "Kode QR", 30);
    if ($is_valid_qr_code[0]) {
      $is_valid_qr_code = !empty($this->tasset->read(1, "qr_code", $this->qr_code, $this->id_asset))
      ? [false, "Kode QR telah terdaftar.<br>"]
      : [true];
    }

    $is_valid_serial_number = is_valid_str($this->get_serial_number(), "No. SN", 30, true);
    if (!is_empty($this->get_serial_number()) && $is_valid_serial_number[0]) {
      $is_valid_serial_number = !empty($this->tasset->read(1, "serial_number", $this->serial_number, $this->id_asset))
      ? [false, "No. SN telah terdaftar.<br>"]
      : [true];
    }

    $is_valid_tanggal_akuisisi_asset = \is_valid_date($this->tanggal_akuisisi_asset, "Tanggal akuisisi asset", true);

    $is_valid_no_surat_kontrak = is_valid_str($this->get_no_surat_kontrak(), "No. surat kontrak", 30, true);
    if ($is_valid_no_surat_kontrak[0] && !is_empty($this->no_surat_kontrak)) {
      $is_valid_no_surat_kontrak = !is_empty($this->no_surat_kontrak) && !empty($this->tasset->read(1, "no_surat_kontrak", $this->no_surat_kontrak, $this->id_asset))
      ? [false, "No. surat kontrak telah terdaftar.<br>"]
      : [true];
    }

    $is_valid_tanggal_berakhir_kontrak = is_valid_date($this->get_tanggal_berakhir_kontrak(), "Tanggal berakhir kontrak", true);

    $is_valid_id_kepemilikan = empty($this->tasset->read(1, "id_kepemilikan", $this->id_kepemilikan))
    ? [false, "Data kepemilikan tidak dapat ditemukan.<br>"]
    : [true];

    $is_valid_keterangan = is_valid_str($this->get_keterangan(), "Keterangan", 200, true);

    if (!is_empty($this->id_asset) || (is_empty($this->id_asset) && !is_empty($this->id_pelanggan))) $is_valid_id_gudang = [true];
    else $is_valid_id_gudang = empty($this->tasset->read(1, "id_gudang", $this->id_gudang))
    ? [false, "Data gudang tidak dapat ditemukan.<br>"]
    : [true];

    if (!is_empty($this->id_asset) || (is_empty($this->id_asset) && !is_empty($this->id_gudang))) $is_valid_id_pelanggan = [true];
    else $is_valid_id_pelanggan = empty($this->tasset->read(1, "id_pelanggan", $this->id_pelanggan))
    ? [false, "Data pelanggan tidak dapat ditemukan.<br>"]
    : [true];

    if (is_empty($this->id_asset) && ( (is_empty($this->id_gudang) && is_empty($this->id_pelanggan)) || (!is_empty($this->id_gudang) && !is_empty($this->id_pelanggan)) )) {
      $is_valid_id_gudang = [false, "Asset hanya bisa dimutasikan ke salah satu antara gudang / pelanggan.<br>"];
    }

    $is_valid_sta = !\in_array($this->sta, ["1", "2"]) ? [false, "Status asset tidak valid.<br>"] : [true];

    $is_valid_alasan = $this->sta !== "2" && is_empty($this->alasan) ? [false, "Jika status asset rusak maka wajib mengisi alasan.<br>"] : [true];

    if (!$is_valid_id_asset[0]) $errors["id_asset"] = $is_valid_id_asset[1];
    if (!$is_valid_id_barang[0]) $errors["barang"] = $is_valid_id_barang[1];
    if (!$is_valid_qr_code[0]) $errors["qr_code"] = $is_valid_qr_code[1];
    if (!$is_valid_serial_number[0]) $errors["serial_number"] = $is_valid_serial_number[1];
    if (!$is_valid_tanggal_akuisisi_asset[0]) $errors["tanggal_akuisisi_asset"] = $is_valid_tanggal_akuisisi_asset[1];
    
    if (!$is_valid_no_surat_kontrak[0]) $errors["no_surat_kontrak"] = $is_valid_no_surat_kontrak[1];
    if (!$is_valid_id_kepemilikan[0]) $errors["kepemilikan"] = $is_valid_id_kepemilikan[1];
    if (!$is_valid_tanggal_berakhir_kontrak[0]) $errors["tanggal_berakhir_kontrak"] = $is_valid_tanggal_berakhir_kontrak[1];
    if (!$is_valid_keterangan[0]) $errors["keterangan"] = $is_valid_keterangan[1];
    if (!$is_valid_id_gudang[0]) $errors["gudang"] = $is_valid_id_gudang[1];
    
    if (!$is_valid_id_pelanggan[0]) $errors["pelanggan"] = $is_valid_id_pelanggan[1];
    if (!$is_valid_sta[0]) $errors["sta"] = $is_valid_sta[1];
    if (!$is_valid_alasan[0]) $errors["alasan"] = $is_valid_alasan[1];

    if (isset($errors)) {
        return [false, $errors];
    } else {
        return [true];
    }
  }

  function get_asset($type = "array") {
    if ($type === "assoc") {
      return [
        "id_asset" => $this->id_asset,
        "id_barang" => $this->id_barang,
        "qr_code" => $this->qr_code,
        "serial_number" => $this->serial_number,
        "tanggal_akuisisi_asset" => $this->tanggal_akuisisi_asset,
        
        "no_surat_kontrak" => $this->no_surat_kontrak,
        "tanggal_berakhir_kontrak" => $this->tanggal_berakhir_kontrak,
        "id_kepemilikan" => $this->id_kepemilikan,
        "keterangan" => $this->keterangan,
        "id_gudang" => $this->id_gudang,
        
        "id_pelanggan" => $this->id_pelanggan,
        "sta" => $this->sta,
        "alasan" => $this->alasan,
      ];
    }
    return $this->asset;
  }

  function set_asset() {
    $this->asset = [
      $this->get_id_barang(),
      $this->get_qr_code(),
      $this->get_serial_number(),
      $this->get_tanggal_akuisisi_asset(),
      $this->get_no_surat_kontrak(),
      
      $this->get_tanggal_berakhir_kontrak(),
      $this->get_id_kepemilikan(),
      $this->get_keterangan(),
      $this->get_id_gudang(),
      $this->get_id_pelanggan(),

      $this->get_sta(),
      $this->get_alasan(),
    ];
  }

  function get_id_asset() {
    return $this->id_asset;
  }

  function set_id_asset($id_asset) {
    $this->id_asset = is_empty($id_asset) ? null : $id_asset;
  }
  
  function get_id_barang() {
    return $this->id_barang;
  }

  function set_id_barang($id_barang) {
    $this->id_barang = $id_barang;
  }

  function get_qr_code() {
    return $this->qr_code;
  }

  function set_qr_code($qr_code) {
    $this->qr_code = $qr_code;
  }

  function get_serial_number() {
    return $this->serial_number;
  }

  function set_serial_number($serial_number) {
    $this->serial_number = is_empty($serial_number) ? null : $serial_number;
  }

  function get_tanggal_akuisisi_asset() {
    return $this->tanggal_akuisisi_asset;
  }

  function set_tanggal_akuisisi_asset($tanggal_akuisisi_asset) {
    $this->tanggal_akuisisi_asset = \is_empty($tanggal_akuisisi_asset) ? null : $tanggal_akuisisi_asset;
  }

  function get_no_surat_kontrak() {
    return $this->no_surat_kontrak;
  }

  function set_no_surat_kontrak($no_surat_kontrak) {
    $this->no_surat_kontrak = is_empty($no_surat_kontrak) ? null : $no_surat_kontrak;
  }

  function get_tanggal_berakhir_kontrak() {
    return $this->tanggal_berakhir_kontrak;
  }

  function set_tanggal_berakhir_kontrak($tanggal_berakhir_kontrak) {
    $this->tanggal_berakhir_kontrak = is_empty($tanggal_berakhir_kontrak) ? null : $tanggal_berakhir_kontrak;
  }

  function get_id_kepemilikan() {
    return $this->id_kepemilikan;
  }

  function set_id_kepemilikan($id_kepemilikan) {
    $this->id_kepemilikan = $id_kepemilikan;
  }

  function get_keterangan() {
    return $this->keterangan;
  }

  function set_keterangan($keterangan) {
    $this->keterangan = is_empty($keterangan) ? null : $keterangan;
  }

  function get_id_gudang() {
    return $this->id_gudang;
  }

  function set_id_gudang($id_gudang) {
    $this->id_gudang = is_empty($id_gudang) ? null : $id_gudang;
  }

  function get_id_pelanggan() {
    return $this->id_pelanggan;
  }

  function set_id_pelanggan($id_pelanggan) {
    $this->id_pelanggan = is_empty($id_pelanggan) ? null : $id_pelanggan;
  }
  
  function get_sta() {
    return $this->sta;
  }

  function set_sta($sta) {
    $this->sta = $sta;
  }
  
  function get_alasan() {
    return $this->alasan;
  }

  function set_alasan($alasan) {
    $this->alasan = is_empty($alasan) ? null : $alasan;
  }
}
