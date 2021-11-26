<?php
namespace App\Controllers\laporan;

use App\Controllers\BaseController;
use App\Models\M_Barang_Keluar;

class Barang_Keluar extends BaseController {
  private $tbarang_keluar;

  function __construct() {
    $this->tbarang_keluar = new M_Barang_Keluar();
  }

  function fetch() {
    try {
      if (\is_empty(get_get("fetch_barang_keluar"))) throw new \Exception("fetch_barang_keluar value not found!");
      $type = get_get("type");
      if ($type === "period") {
        \send_response($this->tbarang_keluar->read(4, $type));
      }
      if ($type === "laporan") {
        $date1 = get_get("date1");
        $date2 = get_get("date2");
        $filter = get_get("filter");
        $id_user = get_get("id_user");
        $r1 = !empty($this->tmenu->read(2, $id_user, "BK-R1"));
        
        if (empty($this->tmenu->read(2, $id_user, "BK-R"))) \send_response(403, ["message" => "Anda tidak memiliki akses untuk melihat laporan barang keluar."]);
        \send_response($this->tbarang_keluar->read(4, $type, $date1, $date2, $filter, $id_user, $r1));
      }
      throw new \Exception("type is not valid!");
    } catch (\Exception $e) {
      \send_500_response(\format_exception($e));
    }
  }
}
