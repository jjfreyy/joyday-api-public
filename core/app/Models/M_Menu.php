<?php
namespace app\Models;

use CodeIgniter\Model;

class M_Menu extends Model {
  protected $db;

  function __construct() {
    $this->db = db_connect();
  }

  function read() {
    switch (\func_get_arg(0)) {
      case 1: // menu/get_menu
        $length = $this->db->escape(\func_get_arg(1));
        $id_user = $this->db->escape(\func_get_arg(2));
        $kode = $this->db->escapeLikeString(\func_get_arg(3));
        return $this->db->query("
          SELECT tm.kode, tm.nama, tm.link
          FROM tmenu tm
          LEFT JOIN tuser2 tu2 ON tm.kode_akses = tu2.kode_akses
          WHERE 
            LENGTH(tm.kode) = $length AND 
            tm.sta = 1 AND
            IF(tm.link = '#', TRUE, tu2.id_user = $id_user) AND
            IF(tm.link = '#', TRUE, tu2.sta = 1) AND
            tm.kode LIKE '$kode%'
          ORDER by tm.kode
        ")->getResult();
      case 2: // authentication/check_privileges
        $id_user = \func_get_arg(1);
        $kode_akses = func_get_arg(2);
        return $this->db->table("tuser2")->
        select("id_user")->
        where(["id_user" => $id_user, "kode_akses" => $kode_akses, "sta" => 1])->
        get()->
        getResult();
    }
  }

}
