<?php
namespace App\Controllers\laporan;

use App\Controllers\BaseController;
use App\Models\M_Asset;

class Asset extends BaseController {
  private $tasset;

  function __construct() {
    $this->tasset = new M_Asset();
  }

  function fetch() {
    try {
      if (\is_empty(get_get("fetch_asset"))) throw new \Exception("fetch_asset value not found!");
      $type = get_get("type");
      if ($type === "period") {
        \send_response($this->tasset->read(6, $type));
      }
      if ($type === "laporan") {
        $filter_by = get_get("filter_by");
        $id = get_get("id");
        $date1 = get_get("date1");
        $date2 = get_get("date2");
        $kondisi = get_get("kondisi");
        $filter = get_get("filter");
        $id_user = get_get("id_user");
        $r1 = !empty($this->tmenu->read(2, $id_user, "ASS-R1"));
        if (empty($this->tmenu->read(2, $id_user, "ASS-R"))) \send_response(403, ["message" => "Anda tidak memiliki akses untuk melihat laporan asset."]);
        \send_response($this->tasset->read(6, $type, $filter_by, $id, $date1, $date2, $kondisi, $filter, $id_user, $r1));
      }
      throw new \Exception("type is not valid!");
    } catch (\Exception $e) {
      \send_500_response(\format_exception($e));
    }
  }
}
