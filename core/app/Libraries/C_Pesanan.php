<?php 
namespace App\Libraries;

use App\Models\M_Pesanan;

class C_Pesanan {
  private $tpesanan;

  private $pesanan;

  private $id_pesanan;
  private $no_po;
  private $id_distributor;
  private $id_pemesan;
  private $keterangan;
  private $pesanan1;

  function __construct() {
    $this->tpesanan = new M_Pesanan();

    $this->set_id_pesanan(func_get_arg(0));
    $this->set_no_po(func_get_arg(1));
    $this->set_id_distributor(func_get_arg(2));
    $this->set_id_pemesan(func_get_arg(3));
    $this->set_keterangan(func_get_arg(4));
    $this->set_pesanan1(func_get_arg(5));

    $this->set_pesanan();
  }

  function is_valid_pesanan() {
      $is_valid_id_pesanan = is_valid_number($this->get_id_pesanan(), "ID pesanan", 2, false, true);
      $is_valid_no_po = is_valid_code($this->get_no_po(), "No. PO", true);
      if ($is_valid_no_po[0] && !is_empty($this->no_po)) {
        $is_valid_no_po = !empty($this->tpesanan->read(1, "no_po", $this->no_po, $this->id_pesanan)) 
        ? [false, "No. PO telah terdaftar.<br>"]
        : [true];
      }
      $is_valid_id_distributor = empty($this->tpesanan->read(1, "id_distributor", $this->id_distributor))
      ? [false, "Data distributor tidak dapat ditemukan.<br>"]
      : [true];
      $is_valid_id_pemesan = empty($this->tpesanan->read(1, "id_pemesan", $this->id_pemesan))
      ? [false, "Data pemesan tidak dapat ditemukan.<br>"]
      : [true];
      $is_valid_keterangan = is_valid_str($this->get_keterangan(), "Keterangan", 200, true);
      
      if (!$is_valid_id_pesanan[0]) $errors["no_po"] = $is_valid_id_pesanan[1];
      if (!$is_valid_no_po[0]) $errors["no_po"] = $is_valid_no_po[1];
      if (!$is_valid_id_distributor[0]) $errors["distributor"] = $is_valid_id_distributor[1];
      if (!$is_valid_id_pemesan[0]) $errors["pemesan"] = $is_pemesan[1];
      if (!$is_valid_keterangan[0]) $errors["keterangan"] = $is_valid_keterangan[1];

      if (isset($errors)) {
          return [false, $errors];
      } else {
          return [true];
      }
  }

  function is_valid_pesanan1($id_pesanan) {
    try {
      if (is_empty_array($this->pesanan1)) return [false, "Silakan isi data barang pesanan.<br>"];

      $pesanan1 = [];
      for ($i = 0; $i < count($this->pesanan1); $i++) {
        $barang = explode(";", $this->pesanan1[$i]);
        $id_barang = $barang[0];
        $qty = $barang[2];

        if (empty($this->tpesanan->read(1, "id_barang", $id_barang))) return [false, ($i+1). ". Data barang tidak dapat ditemukan.<br>"];
        $is_valid_qty = \is_valid_number($qty, "Qty barang", 2, false);
        if (!$is_valid_qty[0]) return [false, ($i+1). ". " .$is_valid_qty[1]];

        if (array_key_exists($id_barang, $pesanan1)) {
          $pesanan1[$id_barang]["qty"] = $pesanan1[$id_barang]["qty"] + $qty;
        } else {
          $pesanan1[$id_barang] = ["id_pesanan" => $id_pesanan, "no" => $i+1, "id_barang" => $id_barang, "qty" => $qty]; 
        }
      }

      return [true, array_values($pesanan1)];
    } catch (\Exception $e) {
      return [false, \format_exception($e)];
    }
  }

  /** accessors and mutators */
  function get_pesanan() {
      return $this->pesanan;
  }

  function set_pesanan() {
      $this->pesanan[] = $this->get_no_po();
      $this->pesanan[] = $this->get_id_distributor();
      $this->pesanan[] = $this->get_id_pemesan();
      $this->pesanan[] = $this->get_keterangan();
  }

  function get_id_pesanan() {
      return $this->id_pesanan;
  }

  function set_id_pesanan($id_pesanan) {
      $this->id_pesanan = is_empty($id_pesanan) ? null : $id_pesanan;
  }
  
  function get_no_po() {
      return $this->no_po;
  }

  function set_no_po($no_po) {
      $this->no_po = is_empty($no_po) ? null : "PO-" .substr("00000$no_po", -5);
  }

  function get_id_distributor() {
      return $this->id_distributor;
  }

  function set_id_distributor($id_distributor) {
      $this->id_distributor = $id_distributor;
  }

  function get_id_pemesan() {
      return $this->id_pemesan;
  }

  function set_id_pemesan($id_pemesan) {
      $this->id_pemesan = $id_pemesan;
  }

  function get_keterangan() {
    return $this->keterangan;
  }

  function set_keterangan($keterangan) {
      $this->keterangan = is_empty($keterangan) ? null : $keterangan;
  }

  function get_pesanan1() {
    return $this->pesanan1;
  }

  function set_pesanan1($pesanan1) {
    $this->pesanan1 = $pesanan1;
  }
}
