<?php
namespace app\Models;

use CodeIgniter\Model;

class M_Pesanan extends Model {
  protected $db;

  function __construct() {
    $this->db = db_connect();
  }

  function deletes($id_pesanan) {
    return $this->db->table("tpesanan1")->where("id_pesanan", $id_pesanan)->delete();
  }

  function put() {
    switch (\func_get_arg(0)) {
      case 1:
        $id_pesanan = \func_get_arg(1);
        $data_pesanan = \func_get_arg(2);
        if (!is_empty($id_pesanan)) $this->db->query("SET @id_pesanan = $id_pesanan");
        $this->db->query("CALL save_pesanan(@id_pesanan, ?, ?, ?, ?)", $data_pesanan);
        return $this->db->query("SELECT @id_pesanan AS id_pesanan")->getResult()[0]->id_pesanan;
      case 2:
        $data_pesanan1 = \func_get_arg(1);
        $result = $this->db->table("tpesanan1")->insertBatch($data_pesanan1);
        return count($data_pesanan1) === $result;
    }
  }

  function read() {
    switch (\func_get_arg(0)) {
      case 1: // input/pesanan/save
        $type = \func_get_arg(1);
        switch ($type) {
          case "no_po":
            return $this->db->table("tpesanan")->select("id_pesanan")->where(["no_po" => \func_get_arg(2), "id_pesanan !=" => \func_get_arg(3)])->get()->getResult();
          case "id_distributor":
            return $this->db->table("tdistributor")->
            select("id_distributor")->where(["id_distributor" => \func_get_arg(2), "sta" => 1])->
            get()->getResult();
          case "id_pemesan":
            return $this->db->table("tuser")->
            select("id_user")->
            where(["sta" => 1, "id_user" => func_get_arg(2)])->
            get()->getResult();
          case "id_barang":
            return $this->db->table("tbarang")->
            select("id_barang")->
            where(["id_barang" => \func_get_arg(2), "sta" => 1])->
            get()->getResult();
        }
      break;
      case 2: // input/pesanan/fetch&type=ajax
        $type = \func_get_arg(1);
        $filter = \func_get_arg(2);
        switch ($type) {
          case "pesanan":
            $filter = $this->db->escapeString($filter);
            return $this->db->query("
              SELECT
                t1.id_pesanan, t1.no_po, 
                t1.id_distributor, t1.distributor, 
                t1.keterangan, 
                GROUP_CONCAT(CONCAT_WS(';', t1.id_barang, t1.barang, t1.qty) SEPARATOR '#') AS pesanan1
              FROM (
                SELECT
                tp.id_pesanan, tp.no_po, 
                tp.id_distributor, CONCAT(td.kode_distributor, ' / ', td.nama_distributor) AS distributor,
                tp.keterangan,
                t1.id_barang, t1.barang, t1.qty
                FROM tpesanan tp
                JOIN tdistributor td ON tp.id_distributor = td.id_distributor
                JOIN (
                  SELECT
                  tp1.id_pesanan, tp1.id_barang, CONCAT(tb1.nama_brand, ' / ', tb2.nama_tipe) AS barang, tp1.qty
                  FROM tpesanan1 tp1
                  JOIN tbarang tb ON tp1.id_barang = tb.id_barang
                  JOIN tbarang1 tb1 ON tb.id_brand = tb1.id_brand
                  JOIN tbarang2 tb2 ON tb.id_tipe = tb2.id_tipe
                ) t1 ON tp.id_pesanan = t1.id_pesanan
                WHERE tp.sta = 1 AND tp.no_po LIKE '%$filter%' AND tp.id_pesanan NOT IN (SELECT dari_id_pesanan FROM tbarang_masuk WHERE dari_id_pesanan IS NOT NULL)
              ) t1
              GROUP BY t1.id_pesanan
              LIMIT " .\get_autocomplete_limit(). "
            ")->getResult();
          case "distributor":
            return $this->db->table("tdistributor")->
            select("id_distributor, kode_distributor, nama_distributor")->
            where("sta", 1)->
            groupStart()->
            orLike(["kode_distributor" => $filter, "nama_distributor" => $filter, "CONCAT(kode_distributor, ' / ', nama_distributor)" => $filter])->
            groupEnd()->
            limit(\get_autocomplete_limit())->
            get()->getResult();
          case "barang":
            return $this->db->table("tbarang tb")->
            select("tb.id_barang, CONCAT(tb1.nama_brand, ' / ', tb2.nama_tipe) AS barang_detail")->
            join("tbarang1 tb1", "tb.id_brand = tb1.id_brand")->
            join("tbarang2 tb2", "tb.id_tipe = tb2.id_tipe")->
            where("tb.sta", 1)->
            groupStart()->
            orLike(["CONCAT(tb1.nama_brand, ' / ', tb2.nama_tipe)" => $filter])->
            groupEnd()->
            limit(\get_autocomplete_limit())->
            get()->getResult();
        }
      break;
      case 3: // input/pesanan/fetch&type=edit
        $id_pesanan = $this->db->escapeString(\func_get_arg(1));
        return $this->db->query("
          SELECT 
            tp.id_pesanan, tp.no_po, 
            tp.id_distributor, CONCAT(td.kode_distributor, ' / ', td.nama_distributor) AS distributor, 
            tp.keterangan,
            NULL AS id_barang, NULL AS barang, NULL AS qty
          FROM tpesanan tp
          JOIN tdistributor td ON tp.id_distributor = td.id_distributor
          WHERE tp.id_pesanan = '$id_pesanan' AND tp.sta = 1 AND tp.id_pesanan NOT IN (SELECT dari_id_pesanan FROM tbarang_masuk WHERE dari_id_pesanan IS NOT NULL AND sta != 0)

          UNION ALL
          SELECT  
            NULL AS id_pesanan, NULL AS no_po, 
            NULL AS id_distributor, NULL AS distributor,
            NULL AS keterangan,
            tp1.id_barang, CONCAT(tb1.nama_brand, ' / ', tb2.nama_tipe) AS barang, tp1.qty
          FROM tpesanan1 tp1
          JOIN tpesanan tp ON tp1.id_pesanan = tp.id_pesanan
          JOIN tbarang tb ON tp1.id_barang = tb.id_barang
          JOIN tbarang1 tb1 ON tb.id_brand = tb1.id_brand
          JOIN tbarang2 tb2 ON tb.id_tipe = tb2.id_tipe  
          WHERE tp1.id_pesanan = '$id_pesanan' AND tp.sta = 1
        ")->getResult();
      case 4: // tampil/pesanan/fetch
        $type = \func_get_arg(1);
        switch ($type) {
          case "period":
            return $this->db->query("
              SELECT YEAR(tp.tanggal_buat) AS tahun, tb.kode AS bulan, tb.nama AS nama_bulan
              FROM tpesanan tp
              JOIN tbulan tb ON MONTH(tp.tanggal_buat) = tb.kode
              WHERE tp.sta != 0
              GROUP BY YEAR(tp.tanggal_buat), MONTH(tp.tanggal_buat)
              ORDER BY tp.tanggal_buat DESC
            ")->getResult();
          case "pesanan":
            $date1 = $this->db->escapeString(\func_get_arg(2));
            $date2 = $this->db->escapeString(func_get_arg(3));
            $filter = $this->db->escapeLikeString(\func_get_arg(4));
            $limit = "";
            if (!is_empty(\func_get_arg(5))) {
              $page = $this->db->escapeString(\func_get_arg(5));
              $display_per_page = $this->db->escapeString(\func_get_arg(6));
              $limit = "LIMIT " .($page * $display_per_page). ", $display_per_page";
            }
            return $this->db->query("
              SELECT 
                t1.id_pesanan,
                t1.no_po,
                t1.distributor,
                t1.pemesan,
                t1.keterangan,
                t1.tanggal_pesan,
                SUM(t1.qty_pesan) AS qty_pesan,
                SUM(t1.qty_masuk) AS qty_masuk,
                GROUP_CONCAT(CONCAT_WS(';', t1.nama_brand, t1.nama_tipe, t1.qty_pesan, t1.qty_masuk) ORDER BY t1.nama_brand SEPARATOR '#') AS pesanan1
              FROM (
                SELECT
                  tp.id_pesanan, 
                  tp.no_po,
                  td.nama_distributor AS distributor,
                  tu.nama_user AS pemesan,
                  tp.keterangan,
                  tp.tanggal_buat AS tanggal_pesan,
                  tb1.nama_brand,
                  tb2.nama_tipe,
                  tp1.qty AS qty_pesan,
                  IFNULL(t1.qty_masuk, 0) AS qty_masuk
                
                FROM tpesanan tp
                JOIN tpesanan1 tp1 ON tp.id_pesanan = tp1.id_pesanan
                JOIN tdistributor td ON tp.id_distributor = td.id_distributor
                JOIN tuser tu ON tp.id_pemesan = tu.id_user
                JOIN tbarang tb ON tp1.id_barang = tb.id_barang
                JOIN tbarang1 tb1 ON tb.id_brand = tb1.id_brand
                JOIN tbarang2 tb2 ON tb.id_tipe = tb2.id_tipe
                LEFT JOIN (
                  SELECT 
                    tbm.id_barang_masuk,
                    tbm.dari_id_pesanan,
                    tbm1.id_barang,
                    COUNT(tbm1.id_barang) AS qty_masuk
                  FROM tbarang_masuk tbm
                  JOIN tbarang_masuk1 tbm1 ON tbm.id_barang_masuk = tbm1.id_barang_masuk
                  WHERE tipe IN (0, 2) AND tbm.sta != 0
                  GROUP BY tbm.id_barang_masuk, tbm1.id_barang
                ) t1 ON tp.id_pesanan = t1.dari_id_pesanan AND tp1.id_barang = t1.id_barang
                
                WHERE tp.sta = 1 AND (DATE(tp.tanggal_buat) BETWEEN '$date1' AND '$date2') AND (
                  tp.no_po LIKE '%$filter%' OR 
                  td.kode_distributor LIKE '%$filter%' OR
                  td.nama_distributor LIKE '%$filter%' OR
                  tu.kode_user LIKE '%$filter%' OR
                  tu.nama_user LIKE '%$filter%' OR
                  tu.username LIKE '%$filter%' OR 
                  tp.keterangan LIKE '%$filter%' OR
                  tp.id_pesanan IN (
                    SELECT tpf.id_pesanan
                    FROM tpesanan tpf
                    JOIN tpesanan1 tp1f ON tpf.id_pesanan = tp1f.id_pesanan
                    JOIN tbarang tbf ON tp1f.id_barang = tbf.id_barang
                    LEFT JOIN tbarang1 tb1f ON tbf.id_brand = tb1f.id_brand
                    JOIN tbarang2 tb2f ON tbf.id_tipe = tb2f.id_tipe
                    WHERE tpf.sta = 1 AND (
                      tp1f.qty = '$filter' OR
                      tbf.kode_barang LIKE '%$filter%' OR 
                      tbf.nama_barang LIKE '%$filter%' OR
                      tb1f.nama_brand LIKE '%$filter%' OR
                      tb2f.nama_tipe LIKE '%$filter%'
                    )
                  )
                )
                
                ORDER BY tp.id_pesanan, tp1.id_barang
              ) t1
              GROUP BY t1.id_pesanan
              $limit
            ")->getResult();
        }
      break;
      case 5: // tampil/pesanan/delete
        $id_pesanan = \func_get_arg(1);
        return $this->db->table("tbarang_masuk")->
        select("id_barang_masuk")->
        whereIn("tipe", [0, 2])->
        where(["dari_id_pesanan" => $id_pesanan, "sta !=" => 0])->
        get()->getResult();
    }
  }

  function updates() {
    switch (\func_get_arg(0)) {
      case 1: // daftar/pesanan/delete
        $this->db->table("tpesanan")->where("id_pesanan", \func_get_arg(1))->update(["sta" => 0, "alasan" => \func_get_arg(2)]);
        return $this->db->affectedRows();
    }
  }

}
