<?php
namespace app\Models;

use CodeIgniter\Model;

class M_Hak_Akses extends Model {
  protected $db;

  function __construct() {
    $this->db = db_connect();
  }

  function read() {
    switch (\func_get_arg(0)) {
      case 1: // sistem/hak_akses/fetch
        $id_user = $this->db->escapeString(\func_get_arg(1), \func_get_arg(2));
        return $this->db->query("
          SELECT tu2.id_user, tu2.kode_akses, tha.nama_akses, tu2.sta
          FROM tuser2 tu2
          JOIN thakakses tha ON tu2.kode_akses = tha.kode_akses
          WHERE tu2.id_user = '$id_user' AND tu2.sta = 1 AND tu2.kode_akses NOT REGEXP 'VD|USR-E'
        ")->getResult();
    }
  }

}
