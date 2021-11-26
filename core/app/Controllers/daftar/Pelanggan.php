<?php
namespace App\Controllers\daftar;

use App\Controllers\BaseController;
use App\Models\M_Pelanggan;

class Pelanggan extends BaseController {
  private $tpelanggan;

  function __construct() {
    $this->tpelanggan = new M_Pelanggan();
  }

  function delete() {
    try {
        if (is_empty(get_post("delete_pelanggan"))) throw new \Exception("delete_pelanggan value not found!");
        $id_user = get_post("id_user");
        $id_pelanggan = get_post("id_pelanggan");
        $alasan = get_post("alasan");
        if (empty($this->tmenu->read(2, $id_user, "PEL-D"))) \send_response(403, ["status" => "error", "message" => "Anda tidak memiliki akses untuk menghapus data pelanggan."]);
        if (!empty($this->tpelanggan->read(5, "tasset", $id_pelanggan))) \send_response(403, ["status" => "error", "message" => "Gagal menghapus pelanggan.<br>Data pelanggan telah terdaftar dalam asset."]);
        // if (!empty($this->tpelanggan->read(5, "tbarang_keluar", $id_pelanggan))) \send_response(403, ["status" => "error", "message" => "Gagal menghapus pelanggan.<br>Data pelanggan telah terdaftar dalam barang keluar."]);
        // if (!empty($this->tpelanggan->read(5, "tbarang_masuk", $id_pelanggan))) \send_response(403, ["status" => "error", "message" => "Gagal menghapus pelanggan.<br>Data pelanggan telah terdaftar dalam barang masuk."]);
        // if (!empty($this->tpelanggan->read(5, "tmutasi", $id_pelanggan))) \send_response(403, ["status" => "error", "message" => "Gagal menghapus pelanggan.<br>Data pelanggan telah terdaftar dalam barang mutasi."]);
        if ($this->tpelanggan->updates(1, $id_pelanggan, $alasan) === 0) \send_response(403, ["status" => "error", "message" => "Gagal menghapus pelanggan. Silakan coba kembali."]);
        \send_response(200, ["status" => "success", "message" => "Berhasil menghapus data pelanggan."]);
    } catch (\Exception $e) {
        \send_500_response(\format_exception($e));
    }
  }

  function fetch() {
    try {
      if (\is_empty(get_get("fetch_pelanggan"))) throw new \Exception("fetch_pelanggan value not found!");
      $id_user = get_get("id_user");
      $filter = get_get("filter");
      $page = get_get("page");
      $display_per_page = get_get("display_per_page");
      if (empty($this->tmenu->read(2, $id_user, "PEL-V"))) \send_response(403, ["message" => "Anda tidak memiliki akses untuk melihat daftar pelanggan."]);
      if (is_empty($page)) \send_response(count($this->tpelanggan->read(4, $filter, "")));
      \send_response($this->tpelanggan->read(4, $filter, $page, $display_per_page));
    } catch (\Exception $e) {
      \send_500_response(\format_exception($e));
    }
  }
}
