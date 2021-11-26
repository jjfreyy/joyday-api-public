<?php
namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Libraries\C_Distributor;
use App\Models\M_Distributor;

class Distributor extends BaseController {
  private $tdistributor;

  function __construct() {
    $this->tdistributor = new M_Distributor();
  }

  function fetch() {
    try {
      $fetch_distributor = get_get("fetch_distributor");
      if (is_empty($fetch_distributor)) throw new \Exception("fetch_distributor value not found!");
      $id_user = get_get("id_user");
      switch ($fetch_distributor) {
        case "ajax":
          $filter = get_get("filter");
          if (empty($this->tmenu->read(2, $id_user, "DIS-E"))) \send_response(403, ["message" => "Anda tidak memiliki akses untuk mengedit data distributor."]);
          \send_response($this->tdistributor->read(2, $filter));
        case "edit":
          $id_distributor = get_get("id_distributor");
          if (empty($this->tmenu->read(2, $id_user, "DIS-E"))) \send_response(403, ["message" => "Anda tidak memiliki akses untuk mengedit data distributor."]);
          \send_response($this->tdistributor->read(3, $id_distributor));
        default:
        throw new \Exception("fetch_distributor value not valid!");
      }
    } catch (\Exception $e) {
      \send_500_response(format_exception($e));
    }
  } 

  function save() {
    try {
      if (is_empty(get_post("save_distributor"))) throw new \Exception("save_distributor value not found!");

      $id_user = get_post("id_user");
      $id_distributor = get_post("id_distributor");
      if (is_empty($id_distributor) && empty($this->tmenu->read(2, $id_user, "DIS-I"))) \send_response(403, ["global" => "Anda tidak memiliki akses untuk menginput data distributor."]);
      if (!is_empty($id_distributor) && empty($this->tmenu->read(2, $id_user, "DIS-E"))) \send_response(403, ["global" => "Anda tidak memiliki akses untuk mengedit data distributor."]);
      
      $kode_distributor = get_post("kode_distributor");
      $nama_distributor = get_post("nama_distributor");
      $alamat = get_post("alamat");
      $no_hp = get_post("no_hp");
      $email = get_post("email");
      $keterangan = get_post("keterangan");
      
      $distributor = new C_Distributor($id_distributor, null, $nama_distributor, $alamat, $no_hp, $email, $keterangan);
      $is_valid_distributor = $distributor->is_valid_distributor();
      if (!$is_valid_distributor[0]) \send_response(400, $is_valid_distributor[1]);      

      $this->tutils->start();
      $this->tdistributor->put($distributor->get_distributor());
      $this->tutils->commit();
      \send_response();
    } catch (\Exception $e) {
      $this->tutils->rollback("tdistributor");
      \send_500_response(format_exception($e));
    }
  }
}
