<?php
namespace App\Controllers\Input;

use App\Controllers\BaseController;
use App\Libraries\C_Pesanan;
use App\Models\M_Pesanan;

class Pesanan extends BaseController {
  private $tpesanan;

  function __construct() {
    $this->tpesanan = new M_Pesanan();
  }

  function fetch() {
    try {
      $fetch_pesanan = get_get("fetch_pesanan");
      if (is_empty($fetch_pesanan)) throw new \Exception("fetch_pesanan value not found!");
      $id_user = get_get("id_user");
      switch ($fetch_pesanan) {
        case "ajax":
          $type = get_get("type");
          $filter = get_get("filter");
          if ($type === "pesanan" && empty($this->tmenu->read(2, $id_user, "PES-E"))) \send_response(403, ["message" => "Anda tidak memiliki akses untuk mengedit data pesanan."]);
          \send_response($this->tpesanan->read(2, $type, $filter));
        case "edit":
          $id_pesanan = get_get("id_pesanan");
          if (empty($this->tmenu->read(2, $id_user, "PES-E"))) \send_response(403, ["message" => "Anda tidak memiliki akses untuk mengedit data pesanan."]);
          \send_response($this->tpesanan->read(3, $id_pesanan));
        default:
        throw new \Exception("fetch_pesanan value not valid!"); 
      }
    } catch (\Exception $e) {
      \send_500_response(format_exception($e));
    }
  } 

  function save() {
    try {
      if (is_empty(get_post("save_pesanan"))) throw new \Exception("save_pesanan value not found!");

      $id_user = get_post("id_user");
      $id_pesanan = get_post("id_pesanan");
      if (is_empty($id_pesanan) && empty($this->tmenu->read(2, $id_user, "PES-I"))) \send_response(403, ["global" => "Anda tidak memiliki akses untuk menginput data pesanan."]);
      if (!is_empty($id_pesanan) && empty($this->tmenu->read(2, $id_user, "PES-E"))) \send_response(403, ["global" => "Anda tidak memiliki akses untuk mengedit data pesanan."]);
      
      $no_po = get_post("no_po");
      $id_distributor = get_post("id_distributor");
      $keterangan = get_post("keterangan");
      $keterangan = get_post("keterangan");
      $pesanan1 = get_post("pesanan1", false);
      
      $pesanan = new C_pesanan($id_pesanan, $no_po, $id_distributor, $id_user, $keterangan, $pesanan1);
      $is_valid_pesanan = $pesanan->is_valid_pesanan();
      if (!$is_valid_pesanan[0]) \send_response(400, $is_valid_pesanan[1]);

      $this->tutils->start();
      $id_pesanan = $this->tpesanan->put(1, $pesanan->get_id_pesanan(), $pesanan->get_pesanan());
      $is_valid_pesanan1 = $pesanan->is_valid_pesanan1($id_pesanan);
      if (!$is_valid_pesanan1[0]) {
        $this->tutils->rollback("tpesanan");
        \send_response(400, ["pesanan1" => $is_valid_pesanan1[1]]);
      }

      if (!$this->tpesanan->deletes($id_pesanan))  {
        $this->tutils->rollback("tpesanan");
        \send_response(400, ["global" => "Gagal menambahkan data pesanan. Silakan coba kembali.<br>"]);
      }
      
      if (!$this->tpesanan->put(2, $is_valid_pesanan1[1])) {
        $this->tutils->rollback("tpesanan");
        \send_response(400, ["global" => "Gagal menambahkan data pesanan. Silakan coba kembali.<br>"]);
      }

      $this->tutils->commit();
      \send_response();
    } catch (\Exception $e) {
      $this->tutils->rollback("tpesanan");
      \send_500_response(format_exception($e));
    }
  }

}
