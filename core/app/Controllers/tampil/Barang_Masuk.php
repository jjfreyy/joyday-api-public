<?php
namespace App\Controllers\tampil;

use App\Controllers\BaseController;
use App\Models\M_Barang_Masuk;

class Barang_Masuk extends BaseController {
  private $tbarang_masuk;

  function __construct() {
    $this->tbarang_masuk = new M_Barang_Masuk();
  }

  function delete() {
    try {
      if (is_empty(get_post("delete_barang_masuk"))) throw new \Exception("delete_barang_masuk value not found!");
      $id_user = get_post("id_user");
      $id_barang_masuk = get_post("id_barang_masuk");
      $alasan = get_post("alasan");
      if (empty($this->tmenu->read(2, $id_user, "BM-D"))) \send_response(403, ["status" => "error", "message" => "Anda tidak memiliki akses untuk menghapus data barang masuk."]);

      $data = $this->tbarang_masuk->read(6, $id_barang_masuk);
      if (empty($data)) \send_response(403, ["status" => "error", "message" => "Data barang masuk tidak dapat ditemukan.<br>"]);
      $tipe = $data[0]->tipe;
      if (!in_array($tipe, ["0", "2"])) {
        \send_response(403, ["status" => "error", "message" => "Tidak dapat menghapus data barang masuk dari pelanggan.<br>"]);
      }
      if (!is_empty($id_barang_masuk) && !$this->tbarang_masuk->read(5, "delete", $id_barang_masuk)) {
        \send_response(403, ["status" => "error", "message" => "Tidak dapat menghapus data barang masuk yang telah berubah assetnya.<br>"]);
      }

      $this->tutils->start();
      if (!$this->tbarang_masuk->updates(1, $id_barang_masuk, $alasan)) {
        $this->tutils->rollback();
        \send_response(403, ["status" => "error", "message" => "Gagal menghapus data barang masuk. Silakan coba kembali.<br>"]);
      } 
      $this->tutils->commit();

      \send_response(200, ["status" => "success", "message" => "Berhasil menghapus data barang masuk."]);
    } catch (\Exception $e) {
      \send_500_response(\format_exception($e));
    }
  }

  function fetch() {
    try {
      if (\is_empty(get_get("fetch_barang_masuk"))) throw new \Exception("fetch_barang_masuk value not found!");
      $type = get_get("type");
      if ($type === "period") {
        \send_response($this->tbarang_masuk->read(4, $type));
      }
      if ($type === "barang_masuk") {
        $id_user = get_get("id_user");
        $date1 = get_get("date1");
        $date2 = get_get("date2");
        $filter = get_get("filter");
        $tipe = get_get("tipe");
        $page = get_get("page");
        $display_per_page = get_get("display_per_page");
        if (empty($this->tmenu->read(2, $id_user, "BM-V"))) \send_response(403, ["message" => "Anda tidak memiliki akses untuk melihat daftar barang masuk."]);
        if (is_empty($page)) \send_response(count($this->tbarang_masuk->read(4, $type, $date1, $date2, $filter, $tipe, $id_user, !empty($this->tmenu->read(2, $id_user, "BM-V1")), "")));
        \send_response($this->tbarang_masuk->read(4, $type, $date1, $date2, $filter, $tipe, $id_user, !empty($this->tmenu->read(2, $id_user, "BM-V1")), $page, $display_per_page));
      }
      throw new \Exception("type is not valid!");
    } catch (\Exception $e) {
      \send_500_response(\format_exception($e));
    }
  }
}
