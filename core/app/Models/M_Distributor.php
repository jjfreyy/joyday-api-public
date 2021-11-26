<?php
namespace app\Models;

use CodeIgniter\Model;

class M_Distributor extends Model {
  protected $db;
  
  function __construct() {
    $this->db = db_connect();
  }

  function put($data_distributor) {
    return $this->db->query("CALL save_distributor(?, ?, ?, ?, ?, ?, ?)", $data_distributor);
  }

  function read() {
    switch (func_get_arg(0)) {
      case 1: // master/distributor/save
        return $this->db->table("tdistributor")->select("id_distributor")->where(["kode_distributor" => \func_get_arg(1), "id_distributor !=" => \func_get_arg(2)])->get()->getResult();
      case 2: // master/distributor/fetch&type=ajax
        return $this->db->table("tdistributor")->
        select("id_distributor, kode_distributor, nama_distributor, alamat, no_hp, email, keterangan")->
        groupStart()->
        orLike(["kode_distributor" => \func_get_arg(1), "nama_distributor" => \func_get_arg(1)])->
        groupEnd()->
        where("sta", 1)->
        limit(\get_autocomplete_limit())->
        get()->getResult();
      case 3: // master/distributor/fetch&type=edit
        return $this->db->table("tdistributor")->
        select("id_distributor, kode_distributor, nama_distributor, alamat, no_hp, email, keterangan")->
        where(["id_distributor" => \func_get_arg(1), "sta" => 1])->
        get()->getResult();
      case 4: // daftar/distributor/fetch
        $filter = $this->db->escapeLikeString(\func_get_arg(1));
        $limit = "";
        if (!is_empty(\func_get_arg(2))) {
            $page = $this->db->escapeString(\func_get_arg(2));
            $display_per_page = $this->db->escapeString(\func_get_arg(3));
            $limit = "LIMIT " .($page * $display_per_page). ", $display_per_page";
        }
        return $this->db->query("
          SELECT id_distributor, kode_distributor, nama_distributor, alamat, no_hp, email, keterangan
          FROM tdistributor
          WHERE sta = 1 AND
          (
            kode_distributor LIKE '%$filter%' OR
            nama_distributor LIKE '%$filter%' OR
            alamat LIKE '%$filter%' OR
            no_hp = '$filter' OR
            email = '$filter' OR
            keterangan LIKE '%$filter%'
          )
          ORDER BY id_distributor
          $limit
        ")->getResult();
        case 5: // daftar/distributor/delete
          return $this->db->table("tpesanan")->select("id_distributor")->where(["id_distributor" => \func_get_arg(1), "sta" => 1])->get()->getResult();
    }
  }

  function updates() {
    switch (\func_get_arg(0)) {
      case 1: // daftar/distributor/delete
        $this->db->table("tdistributor")->where("id_distributor", \func_get_arg(1))->update(["sta" => 0, "alasan" => \func_get_arg(2)]);
        return $this->db->affectedRows();
    }
  }

}
