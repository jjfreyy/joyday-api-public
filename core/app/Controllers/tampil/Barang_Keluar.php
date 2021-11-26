<?php
namespace App\Controllers\tampil;

use App\Controllers\BaseController;
use App\Models\M_Barang_Keluar;

class Barang_Keluar extends BaseController {
  private $tbarang_keluar;

  function __construct() {
    $this->tbarang_keluar = new M_Barang_Keluar();
  }

  function delete() {
    try {
      if (is_empty(get_post("delete_barang_keluar"))) throw new \Exception("delete_barang_keluar value not found!");
      $id_user = get_post("id_user");
      $id_barang_keluar = get_post("id_barang_keluar");
      $alasan = get_post("alasan");
      if (empty($this->tmenu->read(2, $id_user, "BK-D"))) \send_response(403, ["status" => "error", "message" => "Anda tidak memiliki akses untuk menghapus data barang keluar."]);
      if (!is_empty($id_barang_keluar) && !$this->tbarang_keluar->read(5, $id_barang_keluar)) {
        \send_response(403, ["status" => "error", "message" => "Tidak dapat menghapus data barang masuk yang telah berubah assetnya.<br>"]);
      }

      $this->tutils->start();
      if ($this->tbarang_keluar->updates(1, $id_barang_keluar, $alasan) === 0) {
        $this->tutils->rollback();
        \send_response(403, ["status" => "error", "message" => "Gagal menghapus data barang keluar. Silakan coba kembali."]);
      }
      if ($this->tbarang_keluar->updates(2, $id_barang_keluar) === 0) {
        $this->tutils->rollback();
        \send_response(403, ["status" => "error", "message" => "Gagal menghapus data barang keluar. Silakan coba kembali."]);
      }
      $this->tutils->commit();

      \send_response(200, ["status" => "success", "message" => "Berhasil menghapus data barang keluar."]);
    } catch (\Exception $e) {
        \send_500_response(\format_exception($e));
    }
  }

  function fetch() {
    try {
      if (\is_empty(get_get("fetch_barang_keluar"))) throw new \Exception("fetch_barang_keluar value not found!");
      $type = get_get("type");
      switch ($type) {
        case "period":
          \send_response($this->tbarang_keluar->read(4, $type));
        case "barang_keluar":
          $id_user = get_get("id_user");
          $date1 = get_get("date1");
          $date2 = get_get("date2");
          $filter = get_get("filter");
          $page = get_get("page");
          $display_per_page = get_get("display_per_page");
          if (empty($this->tmenu->read(2, $id_user, "BK-V"))) \send_response(403, ["message" => "Anda tidak memiliki akses untuk melihat daftar barang masuk."]);
          if (is_empty($page)) \send_response(count($this->tbarang_keluar->read(4, $type, $date1, $date2, $filter, $id_user, !empty($this->tmenu->read(2, $id_user, "BK-V1")), "")));
          \send_response($this->tbarang_keluar->read(4, $type, $date1, $date2, $filter, $id_user, !empty($this->tmenu->read(2, $id_user, "BK-V1")), $page, $display_per_page));
        case "cetak_surat_jalan":
          $id_user = get_get("id_user");
          
          $id_barang_keluar = get_get("id_barang_keluar");
          
          if (empty($this->tmenu->read(2, $id_user, "BK-SJ"))) \send_response(403, ["message" => "Anda tidak memiliki akses untuk mencetak surat jalan."]);
          $data = $this->tbarang_keluar->read(7, $id_barang_keluar);
          if (empty($data)) \send_response(400, ["message" => "Data barang keluar tidak dapat ditemukan"]);
          \send_response($data);
      }

      throw new \Exception("type is not valid!");
    } catch (\Exception $e) {
      \send_500_response(\format_exception($e));
    }
  }
}
