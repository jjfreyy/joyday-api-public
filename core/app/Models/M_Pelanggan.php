<?php
namespace app\Models;

use CodeIgniter\Model;

class M_Pelanggan extends Model {
  protected $db;
  
  function __construct() {
    $this->db = db_connect();
  }

  function put($data_pelanggan) {
    return $this->db->query("CALL save_pelanggan(
      ?, ?, ?, ?, ?, 
      ?, ?, ?, ?, ?,
      ?, ?, ?, ?, ?,
      ?, ?, ?, ?, ?,
      ?, ?, ?, ?
    )", $data_pelanggan);
  }

  function read() {
    switch (func_get_arg(0)) {
      case 1: // master/pelanggan/save
        $type = \func_get_arg(1);
        switch ($type) {
          case "id_agen":
            return $this->db->table("tpelanggan")->select("id_pelanggan")->where(["id_pelanggan" => \func_get_arg(2), "id_level" => 2, "sta" => 1])->get()->getResult();
          case "kode_pelanggan":
            return $this->db->table("tpelanggan")->
            select("id_pelanggan")->
            where(["kode_pelanggan" => \func_get_arg(2), "id_pelanggan !=" => \func_get_arg(3)])->
            get()->getResult();
          case "propinsi":
            return $this->db->table("tpropinsi")->select("id_propinsi")->where("id_propinsi", \func_get_arg(2))->get()->getResult();
          case "kabupaten":
            return $this->db->table("tkabupaten")->select("id_kabupaten")->where(["id_kabupaten" => \func_get_arg(2), "id_propinsi" => \func_get_arg(3)])->get()->getResult();
          case "kecamatan":
            return $this->db->table("tkecamatan")->select("id_kecamatan")->where(["id_kecamatan" => \func_get_arg(2), "id_kabupaten" => \func_get_arg(3)])->get()->getResult();
          case "kelurahan":
            return $this->db->table("tkelurahan")->select("id_kelurahan")->where(["id_kelurahan" => \func_get_arg(2), "id_kecamatan" => \func_get_arg(3)])->get()->getResult();
        }
      break;
      case 2: // master/pelanggan/fetch&type=ajax
        $type = func_get_arg(1);
        $filter = func_get_arg(2);
        switch ($type) {
          case "pelanggan":
            return $this->db->table("tpelanggan tp")->
            select("
              tp.id_level, tp.id_agen, t1.kode_agen, t1.nama_agen, tp.id_pelanggan,
              tp.kode_pelanggan, tp.nama_pelanggan, tp.no_identitas, tp.no_hp1, tp.no_hp2, 
              tp.email, tp.id_propinsi, tpr.nama_propinsi, tp.id_kabupaten, tkb.nama_kabupaten, 
              tp.id_kecamatan, tkc.nama_kecamatan, tp.id_kelurahan, tkl.nama_kelurahan, tp.alamat, 
              tp.kode_pos, tp.keterangan, tp.daya_listrik, tp.latitude, tp.longitude, 
              tp.nama_kerabat, tp.no_identitas_kerabat, tp.no_hp_kerabat, tp.alamat_kerabat, tp.hubungan
            ")->
            join("(
              SELECT id_pelanggan AS id_agen, kode_pelanggan AS kode_agen, nama_pelanggan AS nama_agen
              FROM tpelanggan
              WHERE id_level = 2 AND sta = 1
            ) t1", "tp.id_agen = t1.id_agen", "left")->
            join("tpropinsi tpr", "tp.id_propinsi = tpr.id_propinsi")->
            join("tkabupaten tkb", "tp.id_kabupaten = tkb.id_kabupaten")->
            join("tkecamatan tkc", "tp.id_kecamatan = tkc.id_kecamatan")->
            join("tkelurahan tkl", "tp.id_kelurahan = tkl.id_kelurahan")->
            groupStart()->
            orLike(["tp.kode_pelanggan" => $filter, "tp.nama_pelanggan" => $filter])->
            groupEnd()->
            where("sta", 1)->
            limit(\get_autocomplete_limit())->
            get()->getResult();
          case "agen":
            return $this->db->table("tpelanggan")->
            select("id_pelanggan AS id_agen, kode_pelanggan AS kode_agen, nama_pelanggan AS nama_agen")->
            groupStart()->
            orLike(["kode_pelanggan" => $filter, "nama_pelanggan" => $filter, "CONCAT(kode_pelanggan, ' / ', nama_pelanggan)" => $filter])->
            groupEnd()->
            where(["id_level" => 2, "sta" => 1])->
            limit(\get_autocomplete_limit())->
            get()->getResult();
          case "propinsi":
            return $this->db->table("tpropinsi")->select("id_propinsi, nama_propinsi")->like("nama_propinsi", $filter)->limit(\get_autocomplete_limit())->get()->getResult();
          case "kabupaten":
            $filter = explode("#", $filter);
            return $this->db->table("tkabupaten")->
            select("id_kabupaten, nama_kabupaten")->
            where("id_propinsi", $filter[0])->
            like("nama_kabupaten", $filter[1])->
            limit(\get_autocomplete_limit())->
            get()->getResult();
          case "kecamatan":
            $filter = explode("#", $filter);
            return $this->db->table("tkecamatan")->
            select("id_kecamatan, nama_kecamatan")->
            where("id_kabupaten", $filter[0])->
            like("nama_kecamatan", $filter[1])->
            limit(\get_autocomplete_limit())->
            get()->getResult();
          case "kelurahan":
            $filter = explode("#", $filter);
            return $this->db->table("tkelurahan")->
            select("id_kelurahan, nama_kelurahan")->
            where("id_kecamatan", $filter[0])->
            like("nama_kelurahan", $filter[1])->
            limit(\get_autocomplete_limit())->
            get()->getResult();
        }
      break;
      case 3: // master/pelanggan/fetch?type=edit
        return $this->db->table("tpelanggan tp")->
        select("
          tp.id_level, tp.id_agen, t1.kode_agen, t1.nama_agen, tp.id_pelanggan,
          tp.kode_pelanggan, tp.nama_pelanggan, tp.no_identitas, tp.no_hp1, tp.no_hp2, 
          tp.email, tp.id_propinsi, tpr.nama_propinsi, tp.id_kabupaten, tkb.nama_kabupaten, 
          tp.id_kecamatan, tkc.nama_kecamatan, tp.id_kelurahan, tkl.nama_kelurahan, tp.alamat, 
          tp.kode_pos, tp.keterangan, tp.daya_listrik, tp.latitude, tp.longitude, 
          tp.nama_kerabat, tp.no_identitas_kerabat, tp.no_hp_kerabat, tp.alamat_kerabat, tp.hubungan
        ")->
        join("(
          SELECT id_pelanggan AS id_agen, kode_pelanggan AS kode_agen, nama_pelanggan AS nama_agen
          FROM tpelanggan
          WHERE id_level = 2 AND sta = 1
        ) t1", "tp.id_agen = t1.id_agen", "left")->
        join("tpropinsi tpr", "tp.id_propinsi = tpr.id_propinsi", "left")->
        join("tkabupaten tkb", "tp.id_kabupaten = tkb.id_kabupaten", "left")->
        join("tkecamatan tkc", "tp.id_kecamatan = tkc.id_kecamatan", "left")->
        join("tkelurahan tkl", "tp.id_kelurahan = tkl.id_kelurahan", "left")->
        where(["tp.id_pelanggan" => \func_get_arg(1), "sta" => 1])->
        get()->getResult();
      case 4: // daftar/pelanggan/fetch
        $filter = $this->db->escapeLikeString(\func_get_arg(1));
        $limit = "";
        if (!is_empty(\func_get_arg(2))) {
            $page = $this->db->escapeString(\func_get_arg(2));
            $display_per_page = $this->db->escapeString(\func_get_arg(3));
            $limit = "LIMIT " .($page * $display_per_page). ", $display_per_page";
        }
        return $this->db->query("
          SELECT 
            tpel.id_pelanggan, tpel1.nama_level, t1.nama_agen, tpel.kode_pelanggan, tpel.nama_pelanggan,
            tpel.no_identitas, tpel.no_hp1, tpel.no_hp2, tpel.email, tpr.nama_propinsi, 
            tkb.nama_kabupaten, tkc.nama_kecamatan, tkl.nama_kelurahan, tpel.alamat, tpel.kode_pos,
            tpel.keterangan, tpel.daya_listrik, tpel.latitude, tpel.longitude, tpel.nama_kerabat, 
            tpel.no_identitas_kerabat, tpel.no_hp_kerabat, tpel.alamat_kerabat, tpel.hubungan
          FROM tpelanggan tpel
          JOIN tpelanggan1 tpel1 ON tpel.id_level = tpel1.id_level
          LEFT JOIN (
            SELECT id_pelanggan AS id_agen, nama_pelanggan AS nama_agen
            FROM tpelanggan
            WHERE sta = 1 AND id_level = 2
          ) t1 ON tpel.id_agen = t1.id_agen
          LEFT JOIN tpropinsi tpr ON tpel.id_propinsi = tpr.id_propinsi
          LEFT JOIN tkabupaten tkb ON tpel.id_kabupaten = tkb.id_kabupaten
          LEFT JOIN tkecamatan tkc ON tpel.id_kecamatan = tkc.id_kecamatan
          LEFT JOIN tkelurahan tkl ON tpel.id_kelurahan = tkl.id_kelurahan
          WHERE tpel.sta = 1 AND tpel.id_level != 3 AND
          (
            tpel.kode_pelanggan LIKE '%$filter%' OR
            tpel.nama_pelanggan LIKE '%$filter%' OR
            tpel.no_identitas = '$filter' OR
            tpel.no_hp1 = '$filter' OR
            tpel.no_hp2 = '$filter' OR
            tpel.email = '$filter' OR
            tpr.nama_propinsi LIKE '%$filter%' OR
            tkb.nama_kabupaten LIKE '%$filter%' OR
            tkc.nama_kecamatan LIKE '%$filter%' OR
            tkl.nama_kelurahan LIKE '%$filter%' OR
            tpel.alamat LIKE '%$filter%' OR
            tpel.kode_pos = '$filter' OR
            tpel.keterangan LIKE '%$filter%' OR
            tpel.daya_listrik = '$filter' OR
            CONCAT(tpel.latitude, ', ', tpel.longitude) = '$filter' OR
            tpel.no_identitas_kerabat = '$filter' OR
            tpel.no_hp_kerabat = '$filter' OR
            tpel.alamat_kerabat LIKE '%$filter%' OR
            tpel.hubungan = '$filter' OR
            tpel1.nama_level = '$filter'
          )
          ORDER BY tpel.id_pelanggan
          $limit
        ")->getResult();
      case 5: // daftar/pelanggan/delete
        $type = func_get_arg(1);
        $id_pelanggan = func_get_arg(2);
        switch ($type) {
          case "tasset":
            return $this->db->table("tasset")->
            select("id_pelanggan")->
            where("id_pelanggan", $id_pelanggan)->
            whereIn("sta", [1, 2])->
            get()->getResult();
          case "tbarang_keluar":
            return $this->db->table("tbarang_keluar tbk")->
            select("tbk.id_barang_keluar")->
            join("tbarang_keluar1 tbk1", "tbk.id_barang_keluar = tbk1.id_barang_keluar")->
            where(["tbk.sta" => 1, "tbk1.id_pelanggan" => $id_pelanggan])->
            get()->getResult();
          case "tbarang_masuk":
            return $this->db->table("tbarang_masuk")->select("id_pelanggan")->where(["id_pelanggan" => $id_pelanggan, "sta" => 1])->get()->getResult();
          case "tmutasi":
            return $this->db->table("tmutasi")->
            select("id_mutasi")->
            where("sta", 1)->
            groupStart()->
            orWhere(["dari_id_pelanggan" => $id_pelanggan, "ke_id_pelanggan" => $id_pelanggan])->
            groupEnd()->
            get()->getResult();
        }
      break;
    }
  }

  function updates() {
    switch (\func_get_arg(0)) {
      case 1: // dafter/pelanggan/delete
        $this->db->table("tpelanggan")->where("id_pelanggan", \func_get_arg(1))->update(["sta" => 0, "alasan" => \func_get_arg(2)]);
        return $this->db->affectedRows();
    }
  }

}
