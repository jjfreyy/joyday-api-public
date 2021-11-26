<?php
namespace App\Controllers\Sistem;

use App\Controllers\BaseController;
use App\Models\M_Hak_Akses;

class Hak_Akses extends BaseController {
  private $thakakses;

  function __construct() {
    $this->thakakses = new M_Hak_Akses();
  }

  function fetch() {
    try {
      if (is_empty(get_get("fetch_hak_akses"))) throw new \Exception("fetch_hak_akses value not found");

      $id_user = get_get("id_user");
      if (empty($this->tmenu->read(2, $id_user, "AKS-I"))) \send_response("403", ["global" => "Anda tidak memiliki hak akses untuk mensetting hak akses."]);
      \send_response(200, $this->thakakses->read(1, $id_user));
    } catch (\Exception $e) {
      \send_response(\format_exception($e));
    }
  }

}
