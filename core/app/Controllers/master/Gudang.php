<?php
namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Libraries\C_Gudang;
use App\Models\M_Gudang;

class Gudang extends BaseController {
  private $tgudang;

  function __construct() {
    $this->tgudang = new M_Gudang();
  }

  function fetch() {
    try {
      $fetch_gudang = get_get("fetch_gudang");
      if (is_empty($fetch_gudang)) throw new \Exception("fetch_gudang value not found!");
      $id_user = get_get("id_user");
      switch ($fetch_gudang) {
        case "ajax":
          $type = get_get("type");
          $filter = get_get("filter");
          if ($type === "gudang" && empty($this->tmenu->read(2, $id_user, "GUD-E"))) \send_response(403, ["message" => "Anda tidak memiliki akses untuk mengedit data gudang."]);
          \send_response($this->tgudang->read(2, $type, $filter));
        case "edit":
          $id_gudang = get_get("id_gudang");
          if (empty($this->tmenu->read(2, $id_user, "GUD-E"))) \send_response(403, ["message" => "Anda tidak memiliki akses untuk mengedit data gudang."]);
          \send_response($this->tgudang->read(3, $id_gudang));
        default:
        throw new \Exception("fetch_gudang value not valid!"); 
      }
    } catch (\Exception $e) {
      \send_500_response(format_exception($e));
    }
  } 

  function save() {
    try {
      if (is_empty(get_post("save_gudang"))) throw new \Exception("save_gudang value not found!");

      $id_user = get_post("id_user");
      $id_gudang = get_post("id_gudang");
      if (is_empty($id_gudang) && empty($this->tmenu->read(2, $id_user, "GUD-I"))) \send_response(403, ["global" => "Anda tidak memiliki akses untuk menginput data gudang."]);
      if (!is_empty($id_gudang) && empty($this->tmenu->read(2, $id_user, "GUD-E"))) \send_response(403, ["global" => "Anda tidak memiliki akses untuk mengedit data gudang."]);
      
      $id_kepala_gudang = get_post("id_kepala_gudang");
      $kode_gudang = get_post("kode_gudang");
      $nama_gudang = get_post("nama_gudang");
      $keterangan = get_post("keterangan");
      
      $gudang = new C_Gudang($id_gudang, $id_kepala_gudang, $kode_gudang, $nama_gudang, $keterangan);
      $is_valid_gudang = $gudang->is_valid_gudang();
      if (!$is_valid_gudang[0]) \send_response(400, $is_valid_gudang[1]);
      
      $this->tutils->start();
      $this->tgudang->put($gudang->get_gudang());
      $this->tutils->commit();
      \send_response();
    } catch (\Exception $e) {
      $this->tutils->rollback("tgudang");
      \send_500_response(format_exception($e));
    }
  }

}
