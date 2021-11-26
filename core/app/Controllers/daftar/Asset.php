<?php
namespace App\Controllers\daftar;

use App\Controllers\BaseController;
use App\Models\M_Asset;

class Asset extends BaseController {
  private $tasset;

  function __construct() {
    $this->tasset = new M_Asset();
  }

  function delete() {
    try {
        if (is_empty(get_post("delete_asset"))) throw new \Exception("delete_asset value not found!");
        $id_user = get_post("id_user");
        $id_asset = get_post("id_asset");
        $alasan = get_post("alasan");
        // if (empty($this->tmenu->read(2, $id_user, "ASS-D"))) \send_response(403, ["status" => "error", "message" => "Anda tidak memiliki akses untuk menghapus data asset."]);
        // if (!empty($this->tasset->read(5, "tbarang_keluar", $id_asset))) \send_response(403, ["status" => "error", "message" => "Gagal menghapus asset.<br>Data asset telah terdaftar dalam barang keluar."]);
        // if (!empty($this->tasset->read(5, "tbarang_masuk", $id_asset))) \send_response(403, ["status" => "error", "message" => "Gagal menghapus asset.<br>Data asset telah terdaftar dalam barang masuk."]);
        // if (!empty($this->tasset->read(5, "tmutasi", $id_asset))) \send_response(403, ["status" => "error", "message" => "Gagal menghapus asset.<br>Data asset telah terdaftar dalam mutasi."]);
        if (!empty($this->tasset->read(5, "id_pelanggan", $id_asset))) \send_response(403, ["status" => "error", "message" => "Tidak dapat menghapus asset yang masih dimiliki pelanggan.<br>."]);
        if ($this->tasset->updates(1, $id_asset, $alasan) === 0) \send_response(403, ["status" => "error", "message" => "Gagal menghapus asset. Silakan coba kembali."]);
        \send_response(200, ["status" => "success", "message" => "Berhasil menghapus data asset."]);
    } catch (\Exception $e) {
        \send_500_response(\format_exception($e));
    }
  }

  function fetch() {
    try {
      if (\is_empty(get_get("fetch_asset"))) throw new \Exception("fetch_asset value not found!");
      $type = get_get("type");
      switch ($type) {
        case "period":
          \send_response(empty($this->tasset->read(4, $type)));
        case "asset":
          $id_user = get_get("id_user");
          $date1 = get_get("date1");
          $date2 = get_get("date2");
          $sta = get_get("sta");
          $filter = get_get("filter");
          $page = get_get("page");
          $display_per_page = get_get("display_per_page");
          if (empty($this->tmenu->read(2, $id_user, "ASS-V"))) \send_response(403, ["message" => "Anda tidak memiliki akses untuk melihat daftar asset."]);
          if (is_empty($page)) send_response(count($this->tasset->read(4, $type, $date1, $date2, $sta, $filter, $id_user, !empty($this->tmenu->read(2, $id_user, "ASS-V1")), "")));
          \send_response($this->tasset->read(4, $type, $date1, $date2, $sta, $filter, $id_user, !empty($this->tmenu->read(2, $id_user, "ASS-V1")), $page, $display_per_page));
        case "export_excel":
          $id_user = get_get("id_user");
          if (empty($this->tutils->read("check_data", "tuser", "id_user", $id_user))) send_response(403, ["message" => "Data user tidak dapat ditemukan.<br>"]);
          \send_response($this->tasset->read("daftar/asset?export_excel", $id_user, !empty($this->tmenu->read(2, $id_user, "ASS-V1"))));
      }
      throw new \Exception("type is not valid!");
    } catch (\Exception $e) {
      \send_500_response(\format_exception($e));
    }
  }
}
