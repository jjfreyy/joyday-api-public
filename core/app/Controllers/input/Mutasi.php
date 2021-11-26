<?php
namespace App\Controllers\Input;

use App\Controllers\BaseController;
use App\Libraries\C_Mutasi;
use App\Models\M_Mutasi;

class Mutasi extends BaseController {
  private $tmutasi;

  function __construct() {
    $this->tmutasi = new M_Mutasi();
  }

  function fetch() {
    try {
      $fetch_mutasi = get_get("fetch_mutasi");
      if (is_empty($fetch_mutasi)) throw new \Exception("fetch_mutasi value not found!");
      $id_user = get_get("id_user");
      switch ($fetch_mutasi) {
        case "ajax":
          $type = get_get("type");
          $filter = get_get("filter");
          if ($type === "mutasi" && empty($this->tmenu->read(2, $id_user, "MUT-E"))) \send_response(403, ["message" => "Anda tidak memiliki akses untuk mengedit data barang keluar."]);
          \send_response($this->tmutasi->read(2, $type, $filter));
        case "edit":
          $id_mutasi = get_get("id_mutasi");
          if (empty($this->tmenu->read(2, $id_user, "MUT-E"))) \send_response(403, ["message" => "Anda tidak memiliki akses untuk mengedit data mutasi."]);
          if (!$this->tmutasi->read(5, $id_mutasi)) \send_response(403, ["message" => "Tidak dapat mengedit data mutasi yang telah berubah assetnya.<br>"]);
          \send_response($this->tmutasi->read(3, $id_mutasi));
        default:
        throw new \Exception("fetch_mutasi value not valid!"); 
      }
    } catch (\Exception $e) {
      \send_500_response(format_exception($e));
    }
  } 

  function save() {
    try {
      if (is_empty(get_post("save_mutasi"))) throw new \Exception("save_mutasi value not found!");
      
      $id_mutasi = get_post("id_mutasi");
      $id_user = get_post("id_user");
      $dari_id_pelanggan = get_post("dari_id_pelanggan");
      $keterangan = get_post("keterangan");
      $mutasi1 = get_post("mutasi1", false);
      
      if (is_empty($id_mutasi) && empty($this->tmenu->read(2, $id_user, "MUT-I"))) \send_response(403, ["global" => "Anda tidak memiliki akses untuk menginput data mutasi."]);
      if (!is_empty($id_mutasi) && empty($this->tmenu->read(2, $id_user, "MUT-E"))) \send_response(403, ["global" => "Anda tidak memiliki akses untuk mengedit data mutasi."]);
      if (!is_empty($id_mutasi) && !$this->tmutasi->read(5, $id_mutasi)) {
        \send_response(403, ["global" => "Tidak dapat mengedit data mutasi yang telah berubah assetnya.<br>"]);
      }
      \send_response(403, ["test" => "SSS"]);
      $mutasi = new C_mutasi($id_mutasi, null, $id_user, $dari_id_pelanggan, $keterangan, $mutasi1);
      $is_valid_mutasi = $mutasi->is_valid_mutasi();
      if (!$is_valid_mutasi[0]) \send_response(400, $is_valid_mutasi[1]);
      
      $this->tutils->start();
      $id_mutasi = $this->tmutasi->put(1, $mutasi->get_id_mutasi(), $mutasi->get_mutasi());
      if (!is_empty($mutasi->get_id_mutasi())) {
        $update = $this->tmutasi->updates(2, $id_mutasi);
        if ($update === 0) \send_response(400, ["mutasi1" => "Gagal mengubah data asset.<br>"]);
      }

      $is_valid_mutasi1 = $mutasi->is_valid_mutasi1($id_mutasi);
      if (!$is_valid_mutasi1[0]) {
        $this->tutils->rollback("tmutasi");
        \send_response(400, ["mutasi1" => $is_valid_mutasi1[1]]);
      }

      if (!$this->tmutasi->deletes($id_mutasi))  {
        $this->tutils->rollback("tmutasi");
        \send_response(400, ["global" => "Gagal menambahkan data mutasi. Silakan coba kembali.<br>"]);
      }
      
      if (!$this->tmutasi->put(2, $is_valid_mutasi1[1])) {
        $this->tutils->rollback("tmutasi");
        \send_response(400, ["global" => "Gagal menambahkan data mutasi. Silakan coba kembali.<br>"]);
      }
      
      $this->tutils->commit();
      \send_response();
    } catch (\Exception $e) {
      $this->tutils->rollback("tmutasi");
      \send_500_response(format_exception($e));
    }
  }

}
