<?php
namespace App\Controllers\laporan;

use App\Controllers\BaseController;
use App\Models\M_Mutasi;

class Mutasi extends BaseController {
  private $tmutasi;

  function __construct() {
    $this->tmutasi = new M_Mutasi();
  }

  function fetch() {
    try {
      if (\is_empty(get_get("fetch_mutasi"))) throw new \Exception("fetch_mutasi value not found!");
      $type = get_get("type");
      if ($type === "period") {
        \send_response($this->tmutasi->read(4, $type));
      }
      if ($type === "laporan") {
        $date1 = get_get("date1");
        $date2 = get_get("date2");
        $dari_pelanggan = get_get("dari_pelanggan");
        $filter = get_get("filter");
        $id_user = get_get("id_user");
        
        if (empty($this->tmenu->read(2, $id_user, "MUT-R"))) \send_response(403, ["message" => "Anda tidak memiliki akses untuk melihat laporan mutasi."]);
        \send_response($this->tmutasi->read(4, $type, $date1, $date2, $dari_pelanggan, $filter));
      }
      throw new \Exception("type is not valid!");
    } catch (\Exception $e) {
      \send_500_response(\format_exception($e));
    }
  }
}
