<?php
namespace App\Controllers\daftar;

use App\Controllers\BaseController;
use App\Models\M_Distributor;

class Distributor extends BaseController {
  private $tdistributor;

  function __construct() {
    $this->tdistributor = new M_Distributor();
  }

  function delete() {
    try {
        if (is_empty(get_post("delete_distributor"))) throw new \Exception("delete_distributor value not found!");
        $id_user = get_post("id_user");
        $id_distributor = get_post("id_distributor");
        $alasan = get_post("alasan");
        if (empty($this->tmenu->read(2, $id_user, "DIS-D"))) \send_response(403, ["status" => "error", "message" => "Anda tidak memiliki akses untuk menghapus data distributor."]);
        // if (!empty($this->tdistributor->read(5, $id_distributor))) \send_response(403, ["status" => "error", "message" => "Gagal menghapus distributor.<br>Data distributor telah terdaftar dalam pesanan."]);
        if ($this->tdistributor->updates(1, $id_distributor, $alasan) === 0) \send_response(403, ["status" => "error", "message" => "Gagal menghapus distributor. Silakan coba kembali."]);
        \send_response(200, ["status" => "success", "message" => "Berhasil menghapus data distributor."]);
    } catch (\Exception $e) {
        \send_500_response(\format_exception($e));
    }
  }

  function fetch() {
    try {
      if (\is_empty(get_get("fetch_distributor"))) throw new \Exception("fetch_distributor value not found!");
      $id_user = get_get("id_user");
      $filter = get_get("filter");
      $page = get_get("page");
      $display_per_page = get_get("display_per_page");
      if (empty($this->tmenu->read(2, $id_user, "DIS-V"))) \send_response(403, ["message" => "Anda tidak memiliki akses untuk melihat daftar distributor."]);
      if (is_empty($page)) \send_response(count($this->tdistributor->read(4, $filter, "")));
      \send_response($this->tdistributor->read(4, $filter, $page, $display_per_page));
    } catch (\Exception $e) {
      \send_500_response(\format_exception($e));
    }
  }
}
