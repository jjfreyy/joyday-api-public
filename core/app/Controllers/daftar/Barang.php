<?php
namespace App\Controllers\daftar;

use App\Controllers\BaseController;
use App\Models\M_Barang;

class Barang extends BaseController {
  private $tbarang;

  function __construct() {
    $this->tbarang = new M_Barang();
  }

  function delete() {
    try {
      if (is_empty(get_post("delete_barang"))) throw new \Exception("delete_barang value not found!");
      $id_user = get_post("id_user");
      $id_barang = get_post("id_barang");
      $alasan = get_post("alasan");
      if (empty($this->tmenu->read(2, $id_user, "B-D"))) \send_response(403, ["status" => "error", "message" => "Anda tidak memiliki akses untuk menghapus data barang."]);
      if (!empty($this->tbarang->read(5, "tasset", $id_barang))) \send_response(403, ["status" => "error", "message" => "Gagal menghapus barang.<br>Data barang telah terdaftar dalam asset."]);
      // if (!empty($this->tbarang->read(5, "tpesanan", $id_barang))) \send_response(403, ["status" => "error", "message" => "Gagal menghapus barang.<br>Data barang telah terdaftar dalam pesanan."]);
      if ($this->tbarang->updates(1, $id_barang, $alasan) === 0) \send_response(403, ["status" => "error", "message" => "Gagal menghapus barang. Silakan coba kembali."]);
      \send_response(200, ["status" => "success", "message" => "Berhasil menghapus data barang."]);
    } catch (\Exception $e) {
        \send_500_response(\format_exception($e));
    }
  }

  function fetch() {
    try {
      if (\is_empty(get_get("fetch_barang"))) throw new \Exception("fetch_barang value not found!");
      $id_user = get_get("id_user");
      $filter = get_get("filter");
      $page = get_get("page");
      $display_per_page = get_get("display_per_page");
      if (empty($this->tmenu->read(2, $id_user, "B-V"))) \send_response(403, ["message" => "Anda tidak memiliki akses untuk melihat daftar barang."]);
      if (is_empty($page)) send_response(count($this->tbarang->read(4, $filter, "")));
      \send_response($this->tbarang->read(4, $filter, $page, $display_per_page));
    } catch (\Exception $e) {
      \send_500_response(\format_exception($e));
    }
  }
}
