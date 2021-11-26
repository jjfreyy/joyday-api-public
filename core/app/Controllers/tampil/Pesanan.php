<?php
namespace App\Controllers\tampil;

use App\Controllers\BaseController;
use App\Models\M_Pesanan;

class Pesanan extends BaseController {
  private $tpesanan;

  function __construct() {
    $this->tpesanan = new M_Pesanan();
  }

  function delete() {
    try {
        if (is_empty(get_post("delete_pesanan"))) throw new \Exception("delete_pesanan value not found!");
        $id_user = get_post("id_user");
        $id_pesanan = get_post("id_pesanan");
        $alasan = get_post("alasan");
        if (empty($this->tmenu->read(2, $id_user, "PES-D"))) \send_response(403, ["status" => "error", "message" => "Anda tidak memiliki akses untuk menghapus data pesanan."]);

        if (!empty($this->tpesanan->read(5, $id_pesanan))) \send_response(400, ["status" => "error", "message" => "Anda tidak dapat menghapus pesanan yang sudah diinput."]);
        if ($this->tpesanan->updates(1, $id_pesanan, $alasan) === 0) \send_response(400, ["status" => "error", "message" => "Gagal menghapus pesanan. Silakan coba kembali."]);
        \send_response(200, ["status" => "success", "message" => "Berhasil menghapus data pesanan."]);
    } catch (\Exception $e) {
        \send_500_response(\format_exception($e));
    }
  }

  function fetch() {
    try {
      if (\is_empty(get_get("fetch_pesanan"))) throw new \Exception("fetch_pesanan value not found!");
      $type = get_get("type");
      if ($type === "period") {
        \send_response($this->tpesanan->read(4, $type));
      }
      if ($type === "pesanan") {
        $id_user = get_get("id_user");
        $date1 = get_get("date1");
        $date2 = get_get("date2");
        $filter = get_get("filter");
        $page = get_get("page");
        $display_per_page = get_get("display_per_page");
        if (empty($this->tmenu->read(2, $id_user, "PES-V"))) \send_response(403, ["message" => "Anda tidak memiliki akses untuk melihat daftar pesanan."]);
        if (is_empty($page)) \send_response(count($this->tpesanan->read(4, $type, $date1, $date2, $filter, "")));
        \send_response($this->tpesanan->read(4, $type, $date1, $date2, $filter, $page, $display_per_page));
      }
      throw new \Exception("type is not valid!");
    } catch (\Exception $e) {
      \send_500_response(\format_exception($e));
    }
  }
}
