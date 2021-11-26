<?php
namespace App\Controllers\Input;

use App\Controllers\BaseController;
use App\Libraries\C_Barang_Masuk;
use App\Models\M_Barang_Masuk;

class Barang_Masuk extends BaseController {
  private $tbarang_masuk;

  function __construct() {
    $this->tbarang_masuk = new M_Barang_Masuk();
  }

  function fetch() {
    try {
      $fetch_barang_masuk = get_get("fetch_barang_masuk");
      if (is_empty($fetch_barang_masuk)) throw new \Exception("fetch_barang_masuk value not found!");
      $id_user = get_get("id_user");
      switch ($fetch_barang_masuk) {
        case "ajax":
          $type = get_get("type");
          $filter = get_get("filter");
          if ($type === "barang_masuk" && empty($this->tmenu->read(2, $id_user, "BM-E"))) \send_response(403, ["message" => "Anda tidak memiliki akses untuk mengedit data barang masuk."]);
          \send_response($this->tbarang_masuk->read(2, $type, $filter));
        case "edit":
          $id_barang_masuk = get_get("id_barang_masuk");
          if (empty($this->tmenu->read(2, $id_user, "BM-E"))) \send_response(403, ["message" => "Anda tidak memiliki akses untuk mengedit data barang masuk."]);
          if (!$this->tbarang_masuk->read(5, "update", $id_barang_masuk)) \send_response(403, ["message" => "Tidak dapat mengedit data barang masuk yang telah berubah assetnya.<br>"]);
          \send_response($this->tbarang_masuk->read(3,  $id_barang_masuk));
        default:
        throw new \Exception("fetch_barang_masuk value not valid!"); 
      }
    } catch (\Exception $e) {
      \send_500_response(format_exception($e));
    }
  } 

  function save() {
    try {
      if (is_empty(get_post("save_barang_masuk"))) throw new \Exception("save_barang_masuk value not found!");

      $id_user = get_post("id_user");
      $tipe = get_post("tipe");
      $id_barang_masuk = get_post("id_barang_masuk");
      $no_masuk = get_post("no_masuk");
      $no_faktur = get_post("no_faktur");
      
      $dari_id_pesanan = get_post("dari_id_pesanan");
      $ke_id_agen = get_post("ke_id_agen");
      $keterangan = get_post("keterangan");
      $barang_masuk1 = get_post("barang_masuk1", false);
      
      if (is_empty($id_barang_masuk) && empty($this->tmenu->read(2, $id_user, "BM-I"))) \send_response(403, ["global" => "Anda tidak memiliki akses untuk menginput data barang masuk.<br>"]);
      if (!is_empty($id_barang_masuk) && empty($this->tmenu->read(2, $id_user, "BM-E"))) \send_response(403, ["global" => "Anda tidak memiliki akses untuk mengedit data barang masuk.<br>"]);
      if (!is_empty($id_barang_masuk) && !$this->tbarang_masuk->read(5, "update", $id_barang_masuk)) {
        \send_response(403, ["global" => "Tidak dapat mengedit data barang masuk yang telah berubah assetnya.<br>"]);
      }
      $barang_masuk = new C_Barang_Masuk(
        $tipe, $id_barang_masuk, $no_masuk, $id_user, $no_faktur, 
        $dari_id_pesanan, $ke_id_agen, $keterangan, $barang_masuk1
      );
      
      $is_valid_barang_masuk = $barang_masuk->is_valid_barang_masuk();
      if (!$is_valid_barang_masuk[0]) \send_response(400, $is_valid_barang_masuk[1]);
      $this->tutils->start();
      $id_barang_masuk = $this->tbarang_masuk->put(1, $barang_masuk->get_id_barang_masuk(), $barang_masuk->get_barang_masuk());
      if (is_empty($barang_masuk->get_id_barang_masuk()) || in_array($barang_masuk->get_tipe(), ["0", "2"])) {
        if (!is_empty($barang_masuk->get_id_barang_masuk())) {
          $this->tbarang_masuk->deletes(1, $id_barang_masuk);
          
          if (in_array($barang_masuk->get_tipe(), ["0", "2"])) {
            if (!$this->tbarang_masuk->deletes(2, $barang_masuk->get_id_barang_masuk())) {
              $this->tutils->rollback(["tasset", "tbarang_masuk"]);
              \send_response(400, ["global" => "Gagal memproses data barang masuk. Silakan coba kembali.<br>"]);
            }
          }
        }

        $is_valid_barang_masuk1 = $barang_masuk->is_valid_barang_masuk1($id_barang_masuk);
        if (!$is_valid_barang_masuk1[0]) {
          $this->tutils->rollback(["tasset", "tbarang_masuk"]);
          \send_response(400, ["barang_masuk1" => $is_valid_barang_masuk1[1]]);
        }

        if (!$this->tbarang_masuk->put(2, $is_valid_barang_masuk1[1])) {
          $this->tutils->rollback(["tasset", "tbarang_masuk"]);
          \send_response(400, ["global" => "Gagal memproses data barang masuk. Silakan coba kembali.<br>"]);
        }
      }
      
      $this->tutils->commit();
      \send_response();
    } catch (\Exception $e) {
      $this->tutils->rollback(["tasset", "tbarang_masuk"]);
      \send_500_response(format_exception($e));
    }
  }

}
