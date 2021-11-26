<?php
namespace App\Controllers\laporan;

use App\Controllers\BaseController;
use App\Models\M_Asset;

class Penggantian_Freezer extends BaseController {
  private $tasset;

  function __construct() {
    $this->tasset = new M_Asset();
  }

  function fetch() {
    try {
      if (\is_empty(get_get("fetch_asset"))) throw new \Exception("fetch_asset value not found!");
      $type = get_get("type");
      if ($type === "period") {
        \send_response($this->tasset->read("laporan/penggantian_freezer?fetch", $type));
      }
      if ($type === "laporan") {
        $id_user = get_get("id_user");
        $date1 = get_get("date1");
        $date2 = get_get("date2");
        $filter = get_get("filter");
        if (empty($this->tmenu->read(2, $id_user, "ASS-R2"))) \send_response(403, ["message" => "Anda tidak memiliki akses untuk melihat laporan histori asset."]);
        \send_response($this->tasset->read("laporan/penggantian_freezer?fetch", $type, $date1, $date2, $filter));
      }
      throw new \Exception("type is not valid!");
    } catch (\Exception $e) {
      \send_500_response(\format_exception($e));
    }
  }
}
