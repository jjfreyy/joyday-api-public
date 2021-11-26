<?php
namespace App\Controllers\tampil;

use App\Controllers\BaseController;
use App\Models\M_Mutasi;

class Mutasi extends BaseController {
  private $tmutasi;

  function __construct() {
    $this->tmutasi = new M_Mutasi();
  }

  function delete() {
    try {
      if (is_empty(get_post("delete_mutasi"))) throw new \Exception("delete_mutasi value not found!");
      $id_user = get_post("id_user");
      $id_mutasi = get_post("id_mutasi");
      $alasan = get_post("alasan");
      if (empty($this->tmenu->read(2, $id_user, "MUT-D"))) \send_response(403, ["status" => "error", "message" => "Anda tidak memiliki akses untuk menghapus data mutasi."]);
      if (!is_empty($id_mutasi) && !$this->tmutasi->read(5, $id_mutasi)) {
        \send_response(403, ["status" => "error", "message" => "Tidak dapat menghapus data mutasi yang telah berubah assetnya.<br>"]);
      }

      $this->tutils->start();
      if ($this->tmutasi->updates(1, $id_mutasi, $alasan) === 0) {
        $this->tutils->rollback();
        \send_response(403, ["status" => "error", "message" => "Gagal menghapus data mutasi. Silakan coba kembali."]);
      }
      if ($this->tmutasi->updates(2, $id_mutasi) === 0) {
        $this->tutils->rollback();
        \send_response(403, ["status" => "error", "message" => "Gagal menghapus data mutasi. Silakan coba kembali."]);
      }
      $this->tutils->commit();

      \send_response(200, ["status" => "success", "message" => "Berhasil menghapus data mutasi."]);
    } catch (\Exception $e) {
        \send_500_response(\format_exception($e));
    }
  }

  function fetch() {
    try {
      if (\is_empty(get_get("fetch_mutasi"))) throw new \Exception("fetch_mutasi value not found!");
      $type = get_get("type");
      if ($type === "period") {
        \send_response($this->tmutasi->read(4, $type));
      }
      if ($type === "mutasi") {
        $id_user = get_get("id_user");
        $date1 = get_get("date1");
        $date2 = get_get("date2");
        $filter = get_get("filter");
        $page = get_get("page");
        $display_per_page = get_get("display_per_page");
        if (empty($this->tmenu->read(2, $id_user, "BK-V"))) \send_response(403, ["message" => "Anda tidak memiliki akses untuk melihat daftar barang masuk."]);
        if (is_empty($page)) \send_response(count($this->tmutasi->read(4, $type, $date1, $date2, $filter, "")));
        \send_response($this->tmutasi->read(4, $type, $date1, $date2, $filter, $page, $display_per_page));
      }
      throw new \Exception("type is not valid!");
    } catch (\Exception $e) {
      \send_500_response(\format_exception($e));
    }
  }
}
