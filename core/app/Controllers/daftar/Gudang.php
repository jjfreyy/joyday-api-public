<?php
namespace App\Controllers\daftar;

use App\Controllers\BaseController;
use App\Models\M_Gudang;

class Gudang extends BaseController {
  private $tgudang;

  function __construct() {
    $this->tgudang = new M_Gudang();
  }

  function delete() {
    try {
        if (is_empty(get_post("delete_gudang"))) throw new \Exception("delete_gudang value not found!");
        $id_user = get_post("id_user");
        $id_gudang = get_post("id_gudang");
        $alasan = get_post("alasan");
        if (empty($this->tmenu->read(2, $id_user, "GUD-D"))) \send_response(403, ["status" => "error", "message" => "Anda tidak memiliki akses untuk menghapus data gudang."]);
        if (!empty($this->tgudang->read(5, "tasset", $id_gudang))) \send_response(403, ["status" => "error", "message" => "Gagal menghapus gudang.<br>Data gudang telah terdaftar dalam asset."]);
        // if (!empty($this->tgudang->read(5, "tbarang_masuk", $id_gudang))) \send_response(403, ["status" => "error", "message" => "Gagal menghapus gudang.<br>Data gudang telah terdaftar dalam barang masuk."]);
        // if (!empty($this->tgudang->read(5, "tbarang_keluar", $id_gudang))) \send_response(403, ["status" => "error", "message" => "Gagal menghapus gudang.<br>Data gudang telah terdaftar dalam barang keluar."]);
        // if (!empty($this->tgudang->read(5, "tmutasi", $id_gudang))) \send_response(403, ["status" => "error", "message" => "Gagal menghapus gudang.<br>Data gudang telah terdaftar dalam mutasi."]);
        if ($this->tgudang->updates(1, $id_gudang, $alasan) === 0) \send_response(403, ["status" => "error", "message" => "Gagal menghapus gudang. Silakan coba kembali."]);
        \send_response(200, ["status" => "success", "message" => "Berhasil menghapus data gudang."]);
    } catch (\Exception $e) {
        \send_500_response(\format_exception($e));
    }
  }

  function fetch() {
    try {
      if (\is_empty(get_get("fetch_gudang"))) throw new \Exception("fetch_gudang value not found!");
      $id_user = get_get("id_user");
      $filter = get_get("filter");
      $page = get_get("page");
      $display_per_page = get_get("display_per_page");
      if (empty($this->tmenu->read(2, $id_user, "GUD-V"))) \send_response(403, ["message" => "Anda tidak memiliki akses untuk melihat daftar gudang."]);
      if (is_empty($page)) \send_response(count($this->tgudang->read(4, $filter, "")));
      \send_response($this->tgudang->read(4, $filter, $page, $display_per_page));
    } catch (\Exception $e) {
      \send_500_response(\format_exception($e));
    }
  }
}
