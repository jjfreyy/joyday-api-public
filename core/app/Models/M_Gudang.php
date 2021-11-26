<?php
namespace app\Models;

use CodeIgniter\Model;

class M_Gudang extends Model {
  protected $db;

  function __construct() {
    $this->db = db_connect();
  }

  function put($data_gudang) {
    return $this->db->query("CALL save_gudang(?, ?, ?, ?, ?)", $data_gudang);
  }

  function read() {
    switch (\func_get_arg(0)) {
      case 1: // master/gudang/save
        $type = \func_get_arg(1);
        switch ($type) {
          case "id_kepala_gudang":
            return $this->db->table("tuser")->select("id_user")->where(["id_user" => \func_get_arg(2), "id_level" => 2, "sta" => 1])->get()->getResult();
          case "kode_gudang":
            return $this->db->table("tgudang")->select("id_gudang")->where(["kode_gudang" => \func_get_arg(2), "id_gudang !=" => \func_get_arg(3)])->get()->getResult();
        }
      break;
      case 2: // master/gudang/fetch&type=ajax
        $type = \func_get_arg(1);
        $filter = \func_get_arg(2);
        switch ($type) {
          case "gudang":
            return $this->db->table("tgudang tg")->
            select("
              tg.id_gudang, tg.id_kepala_gudang, tu.kode_user AS kode_kepala_gudang, tu.nama_user AS nama_kepala_gudang, 
              tg.kode_gudang, tg.nama_gudang, tg.keterangan"
            )->
            join("tuser tu", "tg.id_kepala_gudang = tu.id_user", "left")->
            where("tg.sta", 1)->
            groupStart()->
            orWhere("tu.sta", 1)->
            orWhere("tu.sta", null)->
            groupEnd()->
            groupStart()->
            orWhere("tu.id_level", 2)->
            orWhere("tu.id_level", null)->
            groupEnd()->
            groupStart()->
            orLike(["tg.kode_gudang" => $filter, "tg.nama_gudang" => $filter])->
            groupEnd()->
            limit(\get_autocomplete_limit())->
            get()->getResult();
          case "kepala_gudang":
            return $this->db->table("tuser tu")->
            select("tu.id_user AS id_kepala_gudang, tu.kode_user AS kode_kepala_gudang, tu.nama_user AS nama_kepala_gudang")->
            join("tgudang tg", "tg.id_kepala_gudang = tu.id_user", "left")->
            like("CONCAT(tu.kode_user, ' / ', tu.nama_user)", $filter)->
            where(["tg.id_kepala_gudang" => null, "tu.id_level" => 2, "tu.sta" => 1])->
            limit(\get_autocomplete_limit())->
            get()->getResult();
        }
      break;
      case 3: // master/gudang/fetch&type=edit
        return $this->db->table("tgudang tg")->
        select("tg.id_gudang, tg.id_kepala_gudang, tu.kode_user AS kode_kepala_gudang, tu.nama_user AS nama_kepala_gudang, tg.kode_gudang, tg.nama_gudang, tg.keterangan")->
        join("tuser tu", "tg.id_kepala_gudang = tu.id_user", "left")->
        where(["tg.id_gudang" => \func_get_arg(1), "tg.sta" => 1])->
        groupStart()->
        orWhere("tu.id_level", 2)->
        orWhere("tu.id_level", null)->
        groupEnd()->
        groupStart()->
        orWhere("tu.sta", 1)->
        orWhere("tu.sta", null)->
        groupEnd()->
        get()->getResult();
      case 4: // daftar/gudang/fetch
        $filter = $this->db->escapeLikeString(\func_get_arg(1));
        $limit = "";
        if (!is_empty(\func_get_arg(2))) {
            $page = $this->db->escapeString(\func_get_arg(2));
            $display_per_page = $this->db->escapeString(\func_get_arg(3));
            $limit = "LIMIT " .($page * $display_per_page). ", $display_per_page";
        }
        return $this->db->query("
          SELECT tg.id_gudang, tu.nama_user AS nama_kepala_gudang, tg.kode_gudang, tg.nama_gudang, tg.keterangan
          FROM tgudang tg
          LEFT JOIN tuser tu ON tg.id_kepala_gudang = tu.id_user
          WHERE tg.sta = 1 AND
          (
            tu.kode_user LIKE '%$filter%' OR
            tu.nama_user LIKE '%$filter%' OR
            tg.kode_gudang LIKE '%$filter%' OR
            tg.nama_gudang LIKE '%$filter%' OR
            tg.keterangan LIKE '%$filter%'
          )
          ORDER BY tg.id_gudang
          $limit
        ")->getResult();
      case 5: // daftar/gudang/delete
        $type = \func_get_arg(1);
        $id_gudang = \func_get_arg(2);
        switch ($type) {
          case "tasset":
            return $this->db->table("tasset")->
            select("id_gudang")->
            where("id_gudang", $id_gudang)->
            whereIn("sta", [1, 2])->
            get()->getResult();
          case "tbarang_keluar":
            return $this->db->table("tbarang_keluar")->select("id_gudang")->where(["id_gudang" => $id_gudang, "sta" => 1])->get()->getResult();
          case "tbarang_masuk":
            return $this->db->table("tbarang_masuk")->select("id_gudang")->where(["id_gudang" => $id_gudang, "sta" => 1])->get()->getResult();
          case "tmutasi":
            return $this->db->table("tmutasi")->
            select("dari_id_gudang")->
            where("sta", 1)->
            groupStart()->
            orWhere(["dari_id_gudang" => $id_gudang, "ke_id_gudang" => $id_gudang])->
            groupEnd()->
            get()->getResult();
        }
      break;
    }
  }

  function updates() {
    switch (\func_get_arg(0)) {
      case 1: // daftar/gudang/delete
        $this->db->table("tgudang")->where("id_gudang", \func_get_arg(1))->update(["sta" => 0, "alasan" => \func_get_arg(2)]);
        return $this->db->affectedRows();
    }
  }

}
