<?php 
namespace App\Libraries;

use App\Models\M_Utils;
use App\Models\M_Asset;
use App\Models\M_Barang_Masuk;
use App\Libraries\C_Asset;

class C_Barang_Masuk {
  private $tutils;
  private $tasset;
  private $tbarang_masuk;

  private $barang_masuk;

  private $tipe;
  private $id_barang_masuk;
  private $no_masuk;
  private $id_penerima;
  private $no_faktur;

  private $dari_id_pesanan;
  private $ke_id_gudang;
  private $ke_id_agen;
  private $keterangan;

  private $barang_masuk1;

  function __construct() {
    $this->tutils = new M_Utils();
    $this->tasset = new M_Asset();
    $this->tbarang_masuk = new M_Barang_Masuk();

    
    $this->set_tipe(func_get_arg(0));
    $this->set_id_barang_masuk(func_get_arg(1));
    $this->set_no_masuk(func_get_arg(2));
    $this->set_id_penerima(func_get_arg(3));
    $this->set_no_faktur(func_get_arg(4));
    
    $this->set_dari_id_pesanan(func_get_arg(5));
    if ($this->tipe === "2") $this->set_ke_id_gudang(null);
    else {
      $data = $this->tbarang_masuk->read(1, "ke_id_gudang", $this->id_penerima);
      if (empty($data)) $this->set_ke_id_gudang(null);
      else $this->set_ke_id_gudang($data[0]->id_gudang);
    }
    $this->set_ke_id_agen(func_get_arg(6));
    $this->set_keterangan(func_get_arg(7));
    $this->set_barang_masuk1(func_get_arg(8));
    
    $this->set_barang_masuk();
  }

  function is_valid_barang_masuk() {
    $is_valid_tipe = !in_array($this->tipe, ["0", "1", "2"]) ? [false, "Tipe input tidak valid.<br>"] : [true];
    
    $is_valid_id_barang_masuk = !is_empty($this->id_barang_masuk) && empty($this->tbarang_masuk->read(1, "id_barang_masuk", $this->id_barang_masuk))
    ? [false, "Data barang masuk tidak dapat ditemukan.<br>"] : [true];
    
    $is_valid_no_masuk = is_valid_code($this->get_no_masuk(), "No. masuk", true);
    if ($is_valid_no_masuk[0] && !is_empty($this->no_masuk)) {
      $is_valid_no_masuk = !empty($this->tbarang_masuk->read(1, "no_masuk", $this->no_masuk, $this->id_barang_masuk))
      ? [false, "No. masuk telah terdaftar.<br>"]
      : [true]; 
    }

    $data = $this->tbarang_masuk->read(1, "id_penerima", $this->id_penerima);
    if (empty($data)) {
      $is_valid_id_penerima = [false, "Data penerima tidak dapat ditemukan.<br>"];
    } else {
      $id_level = $data[0]->id_level;
      if (in_array($this->tipe, ["0", "1"]) && $id_level !== "2") {
        $is_valid_id_penerima = [false, "Hanya user berstatus kepala gudang yang dapat melakukan input.<br>"];
      } else if ($this->tipe === "2" && $id_level !== "1") {
        $is_valid_id_penerima = [false, "Hanya user berstatus admin yang dapat melakukan input.<br>"];
      } else {
        $is_valid_id_penerima = [true];
      }
    }

    $is_valid_ke_id_gudang = in_array($this->tipe, ["0", "1"]) && is_empty($this->ke_id_gudang) ? [false, "Data gudang tidak dapat ditemukan.<br>"] : [true];

    $is_valid_keterangan = is_valid_str($this->get_keterangan(), "Keterangan", 200, true);

    if (in_array($this->tipe, ["0", "2"])) {
      $is_valid_no_faktur = is_valid_str($this->get_no_faktur(), "No. faktur", 30, true);
      if ($is_valid_no_faktur[0] && !is_empty($this->no_faktur)) {
        $is_valid_no_faktur = !empty($this->tbarang_masuk->read(1, "no_faktur", $this->no_faktur, $this->id_barang_masuk))
        ? [false, "No. faktur telah terdaftar.<br>"]
        : [true];
      }

      if (!is_empty($this->id_barang_masuk) && in_array($this->tipe, ["0", "2"])) {
        if (empty($this->tbarang_masuk->read(1, "id_pesanan", $this->dari_id_pesanan))) $is_valid_dari_id_pesanan = [false, "Data pesanan tidak dapat ditemukan.<br>"];
        else {
          $last_sisa_pesanan = $this->tbarang_masuk->read(1, "last_sisa_pesanan", $this->id_barang_masuk)[0];
          if (is_empty($last_sisa_pesanan->id_pesanan)) {
            $is_valid_dari_id_pesanan = [false, "Data pesanan tidak dapat ditemukan.<br>"];
          } else if ($this->dari_id_pesanan !== $last_sisa_pesanan->id_pesanan && $last_sisa_pesanan->qty_masuk > 0) {
            $is_valid_dari_id_pesanan = [false, "Tidak dapat mengubah data pesanan yang telah berubah assetnya.<br>"];
          } else if ($this->dari_id_pesanan === $last_sisa_pesanan->id_pesanan && count($this->barang_masuk1) > $last_sisa_pesanan->qty_sisa) {
            $is_valid_dari_id_pesanan = [false, "Jumlah qty masuk lebih besar dari qty pesanan.<br>"];
          } else {
            $is_valid_dari_id_pesanan = [true];
          }
        }
      } else $is_valid_dari_id_pesanan = [true];

      $is_valid_ke_id_agen = $this->tipe === "2" && empty($this->tbarang_masuk->read(1, "ke_id_agen", $this->ke_id_agen)) 
      ? [false, "Data agen tidak dapat ditemukan.<br>"]
      : [true];
    } else {
      $is_valid_no_faktur = [true];

      $is_valid_dari_id_pesanan = [true];

      $is_valid_ke_id_agen = [true];
    }

    if (!$is_valid_tipe[0]) $errors["tipe"] = $is_valid_tipe[1];
    if (!$is_valid_id_barang_masuk[0]) $errors["no_masuk"] = $is_valid_id_barang_masuk[1];
    if (!$is_valid_no_masuk[0]) $errors["no_masuk"] = $is_valid_no_masuk[1];
    if (!$is_valid_id_penerima[0]) $errors["id_penerima"] = $is_valid_id_penerima[1];
    if (!$is_valid_ke_id_gudang[0]) $errors["ke_id_gudang"] = $is_valid_ke_id_gudang[1];
    if (!$is_valid_keterangan[0]) $errors["keterangan"] = $is_valid_keterangan[1];
    if (!$is_valid_no_faktur[0]) $errors["no_faktur"] = $is_valid_no_faktur[1];
    if (!$is_valid_dari_id_pesanan[0]) $errors["no_po"] = $is_valid_dari_id_pesanan[1];
    if (!$is_valid_ke_id_agen[0]) $errors["ke_agen"] = $is_valid_ke_id_agen[1];

    if (isset($errors)) {
        return [false, $errors];
    } else {
        return [true];
    }
  }

  function is_valid_barang_masuk1($id_barang_masuk) {
    if (is_empty_array($this->barang_masuk1)) return [false, "Silakan isi data barang masuk.<br>"];

    try {
      if (in_array($this->tipe, ["0", "2"])) {
        $id_kepemilikan = 1;
        $last_no = $this->tbarang_masuk->read(1, "last_no", $id_barang_masuk)[0]->last_no;
        for ($i = 0; $i < count($this->barang_masuk1); $i++) {
          $no = $last_no + $i + 1;
          $data = explode(";", $this->barang_masuk1[$i]);
          $id_barang = sanitize($data[0]);
          $qr_code = sanitize($data[1]);

          $asset = new C_Asset(
            null, $id_barang, $qr_code, null, null, 
            null, null, $id_kepemilikan, null, $this->tipe === "0" ? $this->ke_id_gudang : null, 
            $this->tipe === "2" ? $this->ke_id_agen : null, "2", null
          );
          $is_valid_asset = $asset->is_valid_asset();
          if (!$is_valid_asset[0]) return [false, "$no. " .array_values($is_valid_asset[1])[0]];
          $insert = $this->tbarang_masuk->put(3, [
            "id_barang" => $id_barang, 
            "qr_code" => $qr_code,
            "id_kepemilikan" => $id_kepemilikan,
            "id_gudang" => $this->tipe === "0" ? $this->ke_id_gudang : null,
            "id_pelanggan" => $this->tipe === "2" ? $this->ke_id_agen : null,
            "id_input" => $id_barang_masuk,
            "dari_input" => 0,
          ]);
          if (!$insert) return [false, "$no. Gagal menambahkan data asset.<br>"];
          $barang_masuk1[] = ["id_barang_masuk" => $id_barang_masuk, "no" => $no, "id_asset" => $this->tutils->get_insert_id(), "id_barang" => $id_barang];
        }

        return [true, $barang_masuk1];
      } else {
        for ($i = 0; $i < count($this->barang_masuk1); $i++) {
          $no = $i + 1;
          $data = explode(";", $this->barang_masuk1[$i]);
          $id_asset = sanitize($data[0]);
          $id_barang = sanitize($data[2]);
          $id_pelanggan = sanitize($data[4]);
          $sta = sanitize($data[6]);
          
          $data_asset = $this->tbarang_masuk->read(1, "id_asset", $id_asset);
          if (empty($data_asset)) return [false, "$no. Data asset tidak dapat ditemukan.<br>"];
          $data_barang = $this->tbarang_masuk->read(1, "id_barang", $id_barang);
          if (empty($data_barang)) return [false, "$no. Data barang tidak dapat ditemukan.<br>"];
          $data_pelanggan = $this->tbarang_masuk->read(1, "dari_id_pelanggan", $id_asset, $id_pelanggan);
          if (empty($data_pelanggan)) return [false, "$no. Data pelanggan tidak dapat ditemukan.<br>"];

          $update = $this->tasset->updates(2, $id_asset, [
            "id_gudang" => $this->ke_id_gudang,
            "id_pelanggan" => null,
            "id_input" => $id_barang_masuk,
            "dari_input" => 0,
            "sta" => $sta,
          ]);
          if (!$update) return [false, "$no. Gagal mengubah data asset.<br>"];
          $barang_masuk1[] = [
            "id_barang_masuk" => $id_barang_masuk, 
            "no" => $no, 
            "id_asset" => $id_asset, 
            "id_barang" => $id_barang,
            "dari_id_pelanggan" => $id_pelanggan,
            "id_input_terakhir" => $data_asset[0]->id_input,
            "dari_input" => $data_asset[0]->dari_input, 
          ];
        }

        return [true, $barang_masuk1];
      }
    } catch (\Exception $e) {
      return [false, \format_exception($e)];
    }
  }

  /** accessors and mutators */
  function get_barang_masuk() {
      return $this->barang_masuk;
  }

  function set_barang_masuk() {
      $this->barang_masuk = [
        $this->tipe, 
        $this->no_masuk, 
        $this->id_penerima, 
        $this->no_faktur, 
        $this->dari_id_pesanan, 
        $this->ke_id_gudang, 
        $this->ke_id_agen, 
        $this->keterangan,
      ]; 
      
  }

  function get_tipe() {
    return $this->tipe;
  }

  function set_tipe($tipe) {
    $this->tipe = $tipe;
  } 

  function get_id_barang_masuk() {
      return $this->id_barang_masuk;
  }

  function set_id_barang_masuk($id_barang_masuk) {
      $this->id_barang_masuk = is_empty($id_barang_masuk) ? null : $id_barang_masuk;
  }

  
  function get_no_masuk() {
      return $this->no_masuk;
  }

  function set_no_masuk($no_masuk) {
    $no_masuk_prefix;
    if (is_empty($no_masuk)) {
      $no_masuk_prefix = null;
    } else if ($this->tipe === "0") {
      $no_masuk_prefix = "BMD";
    } else if ($this->tipe === "1") {
      $no_masuk_prefix = "BMP";
    } else if ($this->tipe === "2") {
      $no_masuk_prefix = "BMA";
    }

    if (!isset($no_masuk_prefix)) $this->no_masuk = null;
    else $this->no_masuk = "$no_masuk_prefix-" .substr("00000$no_masuk", -5);
  }

  function get_id_penerima() {
      return $this->id_penerima;
  }

  function set_id_penerima($id_penerima) {
      $this->id_penerima = $id_penerima;
  }

  function get_no_faktur() {
      return $this->no_faktur;
  }

  function set_no_faktur($no_faktur) {
      $this->no_faktur = !in_array($this->tipe, ["0", "2"]) || is_empty($no_faktur) ? null : $no_faktur;
  }

  function get_dari_id_pesanan() {
      return $this->dari_id_pesanan;
  }

  function set_dari_id_pesanan($dari_id_pesanan) {
      $this->dari_id_pesanan = !in_array($this->tipe, ["0", "2"]) ? null : $dari_id_pesanan;
  }

  function get_ke_id_gudang() {
    return $this->ke_id_gudang;
  }

  function set_ke_id_gudang($ke_id_gudang) {
    $this->ke_id_gudang = $this->tipe === "2" ? null : $ke_id_gudang;
  }  

  function get_ke_id_agen() {
      return $this->ke_id_agen;
  }

  function set_ke_id_agen($ke_id_agen) {
      $this->ke_id_agen = $this->tipe !== "2" ? null : $ke_id_agen;
  }

  function get_keterangan() {
    return $this->keterangan;
  }

  function set_keterangan($keterangan) {
      $this->keterangan = is_empty($keterangan) ? null : $keterangan;
  }

  function get_sisa_pesanan() {
    return $this->sisa_pesanan;
  }

  function get_barang_masuk1() {
    return $this->barang_masuk1;
  }

  function set_barang_masuk1($barang_masuk1) {
    $this->barang_masuk1 = $barang_masuk1;
  }
}
