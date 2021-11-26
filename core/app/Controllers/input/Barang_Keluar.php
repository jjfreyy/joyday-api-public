<?php
namespace App\Controllers\Input;

use App\Controllers\BaseController;
use App\Libraries\C_Barang_Keluar;
use App\Models\M_Barang_Keluar;

class Barang_Keluar extends BaseController {
  private $tbarang_keluar;

  function __construct() {
    $this->tbarang_keluar = new M_Barang_Keluar();
  }

  function fetch() {
    try {
      $fetch_barang_keluar = get_get("fetch_barang_keluar");
      if (is_empty($fetch_barang_keluar)) throw new \Exception("fetch_barang_keluar value not found!");
      $id_user = get_get("id_user");
      switch ($fetch_barang_keluar) {
        case "ajax":
          $type = get_get("type");
          $filter = get_get("filter");
          if ($type === "barang_keluar" && empty($this->tmenu->read(2, $id_user, "BK-E"))) \send_response(403, ["message" => "Anda tidak memiliki akses untuk mengedit data barang keluar."]);
          \send_response($this->tbarang_keluar->read(2, $type, $filter));
        case "edit":
          $id_barang_keluar = get_get("id_barang_keluar");
          if (empty($this->tmenu->read(2, $id_user, "BK-E"))) \send_response(403, ["message" => "Anda tidak memiliki akses untuk mengedit data barang keluar."]);
          if (!$this->tbarang_keluar->read(5, $id_barang_keluar)) \send_response(403, ["message" => "Tidak dapat mengedit data barang keluar yang telah berubah assetnya.<br>"]);
          \send_response($this->tbarang_keluar->read(3, $id_barang_keluar));
        default:
          throw new \Exception("fetch_barang_keluar value not valid!"); 
      }
    } catch (\Exception $e) {
      \send_500_response(format_exception($e));
    }
  } 

  function save() {
    try {
      if (is_empty(get_post("save_barang_keluar"))) throw new \Exception("save_barang_keluar value not found!");
      
      $id_barang_keluar = get_post("id_barang_keluar");
      $id_user = get_post("id_user");
      $id_gudang = get_post("id_gudang");
      $keterangan = get_post("keterangan");
      $barang_keluar1 = get_post("barang_keluar1", false);
      
      if (is_empty($id_barang_keluar) && empty($this->tmenu->read(2, $id_user, "BK-I"))) \send_response(403, ["global" => "Anda tidak memiliki akses untuk menginput data barang keluar."]);
      if (!is_empty($id_barang_keluar) && empty($this->tmenu->read(2, $id_user, "BK-E"))) \send_response(403, ["global" => "Anda tidak memiliki akses untuk mengedit data barang keluar."]);
      if (!is_empty($id_barang_keluar) && !$this->tbarang_keluar->read(5, $id_barang_keluar)) {
        \send_response(403, ["global" => "Tidak dapat mengedit data barang keluar yang telah berubah assetnya.<br>"]);
      }
      if (!is_empty($id_barang_keluar) && empty($this->tbarang_keluar->read(6, $id_barang_keluar, $id_user))) {
        \send_response(403, ["global" => "Anda tidak dapat mengubah data barang keluar dari gudang lain.<br>"]);
      }

      $barang_keluar = new C_barang_keluar($id_barang_keluar, null, $id_user, $id_gudang, $keterangan, $barang_keluar1);
      $is_valid_barang_keluar = $barang_keluar->is_valid_barang_keluar();
      if (!$is_valid_barang_keluar[0]) \send_response(400, $is_valid_barang_keluar[1]);

      $this->tutils->start();
      $id_barang_keluar = $this->tbarang_keluar->put(1, $barang_keluar->get_id_barang_keluar(), $barang_keluar->get_barang_keluar());
      if (!is_empty($barang_keluar->get_id_barang_keluar())) {
        $update = $this->tbarang_keluar->updates(2, $barang_keluar->get_id_barang_keluar());
        if ($update === 0) {
          $this->tutils->rollback("tbarang_keluar");
          \send_response(400, ["barang_keluar1" => "Gagal mengubah data asset.<br>"]);
        }
      }

      $is_valid_barang_keluar1 = $barang_keluar->is_valid_barang_keluar1($id_barang_keluar);
      if (!$is_valid_barang_keluar1[0]) {
        $this->tutils->rollback("tbarang_keluar");
        \send_response(400, ["barang_keluar1" => $is_valid_barang_keluar1[1]]);
      }
      
      if (!$this->tbarang_keluar->deletes($id_barang_keluar))  {
        $this->tutils->rollback("tbarang_keluar");
        \send_response(400, ["global" => "Gagal menambahkan data barang keluar. Silakan coba kembali.<br>"]);
      }
      
      if (!$this->tbarang_keluar->put(2, $is_valid_barang_keluar1[1])) {
        $this->tutils->rollback("tbarang_keluar");
        \send_response(400, ["global" => "Gagal menambahkan data barang keluar. Silakan coba kembali.<br>"]);
      }
      
      $this->tutils->commit();
      \send_response(["id_barang_keluar" => $id_barang_keluar]);
    } catch (\Exception $e) {
      $this->tutils->rollback("tbarang_keluar");
      \send_500_response(format_exception($e));
    }
  }

}
