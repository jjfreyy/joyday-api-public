<?php
namespace app\Models;

use CodeIgniter\Model;

class M_Mutasi extends Model {
  protected $db;

  function __construct() {
    $this->db = db_connect();
  }

  function deletes($id_mutasi) {
    return $this->db->table("tmutasi1")->where("id_mutasi", $id_mutasi)->delete();
  }

  function put() {
    switch (func_get_arg(0)) {
      case 1: 
        $id_mutasi = func_get_arg(1);
        $data_mutasi = func_get_arg(2);
        if (!is_empty($id_mutasi)) $this->db->query("SET @id_mutasi = $id_mutasi");
        $this->db->query("CALL save_mutasi(@id_mutasi, ?, ?, ?, ?)", $data_mutasi);
        return $this->db->query("SELECT @id_mutasi AS id_mutasi")->getResult()[0]->id_mutasi;
      case 2:
        $data_mutasi1 = func_get_arg(1);
        $result = $this->db->table("tmutasi1")->insertBatch($data_mutasi1);
        return count($data_mutasi1) === $result;
    }
  }

  function read() {
    switch (func_get_arg(0)) {
      case 1: // libraries/input/c_mutasi/is_valid_mutasi
        $type = func_get_arg(1);
        switch ($type) {
          case "no_mutasi":
            return $this->db->table("tmutasi")->
            select("id_mutasi")->
            where(["no_mutasi" => \func_get_arg(2), "id_mutasi !=" => \func_get_arg(3)])->
            get()->getResult();
          case "id_user":
            return $this->db->table("tuser")->
            select("id_user")->
            where(["id_user" => \func_get_arg(2), "sta" => 1])->
            get()->getResult();
          case "id_pelanggan":
            return $this->db->table("tpelanggan")->
            select("id_pelanggan")->
            where(["id_pelanggan" => \func_get_arg(2), "sta" => 1])->
            get()->getResult();
          case "id_asset":
            return $this->db->table("tasset")->
            select("id_asset, id_input, dari_input")->
            where(["id_asset" => \func_get_arg(2), "id_pelanggan" => \func_get_arg(3), "sta !=" => 0])->
            get()->getResult();
        }
      break;
      case 2: // input/mutasi_asset/fetch&type=ajax
        $type = func_get_arg(1);
        $filter = func_get_arg(2);
        switch ($type) {
          case "dari_pelanggan":
            $filter = explode(";", $this->db->escapeLikeString($filter));
            return $this->db->query("
            SELECT 
              t1.id_pelanggan, t1.pelanggan, t1.alamat,
              GROUP_CONCAT(t1.asset ORDER BY t1.id_asset SEPARATOR '#') AS asset
            FROM (
              SELECT 
                IF(t1.id_input = '$filter[1]' AND t1.dari_input = 2, t1.dari_id_pelanggan, t1.id_pelanggan) AS id_pelanggan,
                IF(t1.id_input = '$filter[1]' AND t1.dari_input = 2, t1.dari_pelanggan, t1.pelanggan) AS pelanggan,
                IF(t1.id_input = '$filter[1]' AND t1.dari_input = 2, t1.alamat_dari_pelanggan, t1.alamat_pelanggan) AS alamat,
                t1.id_asset, 
                t1.asset
              FROM (
                SELECT 
                  tp.id_pelanggan, CONCAT(tp.kode_pelanggan, ' / ', tp.nama_pelanggan) AS pelanggan,
                  tp.alamat AS alamat_pelanggan,
                  ta.id_asset, CONCAT_WS(';', ta.id_asset, ta.qr_code, IFNULL(ta.no_surat_kontrak, '')) AS asset,
                  ta.id_input, ta.dari_input,
                  tm.dari_id_pelanggan, CONCAT(tdp.kode_pelanggan, ' / ', tdp.nama_pelanggan) AS dari_pelanggan,
                  tdp.alamat AS alamat_dari_pelanggan
                  
                FROM tasset ta
                JOIN tpelanggan tp ON tp.id_pelanggan = ta.id_pelanggan
                JOIN tbarang tb ON ta.id_barang = tb.id_barang
                LEFT JOIN tmutasi tm ON ta.id_input = tm.id_mutasi AND ta.dari_input = 2
                LEFT JOIN tpelanggan tdp ON tm.dari_id_pelanggan = tdp.id_pelanggan
                WHERE ta.sta != 0
              ) t1
            ) t1
            WHERE t1.pelanggan LIKE '%$filter[0]%'
            GROUP BY t1.id_pelanggan
            LIMIT " .\get_autocomplete_limit(). "
            ")->getResult();
          case "ke_pelanggan":
            return $this->db->table("tpelanggan")->
            select("id_pelanggan, CONCAT(kode_pelanggan, ' / ', nama_pelanggan) AS pelanggan, alamat")->
            whereIn("id_level", [1, 2])->
            where("sta", 1)->
            like("CONCAT(kode_pelanggan, ' / ', nama_pelanggan)", $filter)->
            limit(\get_autocomplete_limit())->
            get()->getResult();
        }
      break;
      case 3: // input/mutasi/fetch&type=edit
        $id_mutasi = $this->db->escapeString(func_get_arg(1));
        return $this->db->query("
          SELECT 
            t1.id_mutasi,
            t1.no_mutasi,
            t1.dari_id_pelanggan, t1.dari_pelanggan,
            t1.keterangan,
            GROUP_CONCAT(t1.mutasi1 ORDER BY t1.no SEPARATOR '#') AS mutasi1
          FROM (
            SELECT 
              tm.id_mutasi,
              tm.no_mutasi,
              tm.dari_id_pelanggan, CONCAT(tdp.kode_pelanggan, ' / ', tdp.nama_pelanggan) AS dari_pelanggan,
              tm.keterangan,
              tm1.`no`, CONCAT_WS(';', ta.id_asset, ta.qr_code, IFNULL(ta.no_surat_kontrak, ''), tm1.ke_id_pelanggan, CONCAT(tkp.kode_pelanggan, ' / ', tkp.nama_pelanggan), IFNULL(tkp.alamat, '-')) AS mutasi1
            
            FROM tmutasi tm
            JOIN tpelanggan tdp ON tm.dari_id_pelanggan = tdp.id_pelanggan
            JOIN tmutasi1 tm1 ON tm.id_mutasi = tm1.id_mutasi
            JOIN tasset ta ON tm1.id_asset = ta.id_asset
            JOIN tbarang tb ON ta.id_barang = tb.id_barang
            JOIN tpelanggan tkp ON tm1.ke_id_pelanggan = tkp.id_pelanggan

            WHERE tm.id_mutasi = '$id_mutasi' AND tm.sta = 1
          ) t1
        ")->getResult();
      break;
      case 4: // tampil/mutasi/fetch
        $type = func_get_arg(1);
        switch ($type) {
          case "period":
            return $this->db->query("
              SELECT YEAR(tm.tanggal_buat) AS tahun, tb.kode AS bulan, tb.nama AS nama_bulan
              FROM tmutasi tm
              JOIN tbulan tb ON MONTH(tm.tanggal_buat) = tb.kode
              WHERE tm.sta != 0
              GROUP BY YEAR(tm.tanggal_buat), MONTH(tm.tanggal_buat)
              ORDER BY tm.tanggal_buat DESC
            ")->getResult();
          case "mutasi":
            $date1 = $this->db->escapeString(\func_get_arg(2));
            $date2 = $this->db->escapeString(func_get_arg(3));
            $filter = $this->db->escapeLikeString(\func_get_arg(4));
            $id_user = $this->db->escapeString(\func_get_arg(5));
            $limit = "";
            if (!is_empty(\func_get_arg(5))) {
              $page = $this->db->escapeString(\func_get_arg(5));
              $display_per_page = $this->db->escapeString(\func_get_arg(6));
              $limit = "LIMIT " .($page * $display_per_page). ", $display_per_page";
            }
            return $this->db->query("
              SELECT 
                t1.id_mutasi,
                t1.no_mutasi,
                t1.usr,
                t1.dari_pelanggan,
                t1.keterangan,
                t1.tanggal_mutasi,
                t1.can_edit,
                t1.mutasi1
                
              FROM (	
                SELECT 
                  tm.id_mutasi,
                  tm.no_mutasi,
                  CONCAT(tu.kode_user, ' / ', tu.nama_user) AS usr,
                  CONCAT(tpd.kode_pelanggan, ' / ', tpd.nama_pelanggan) AS dari_pelanggan,
                  tm.keterangan,
                  tm.tanggal_buat AS tanggal_mutasi,
                  IF(COUNT(*) = (SELECT COUNT(*) FROM tasset WHERE id_input = tm.id_mutasi AND dari_input = 2), 1, 0) AS can_edit,
                  GROUP_CONCAT(CONCAT_WS(';', ta.qr_code, tb1.nama_brand, tb2.nama_tipe, CONCAT(tpk.kode_pelanggan, ' / ', tpk.nama_pelanggan)) ORDER BY ta.qr_code SEPARATOR '#') AS mutasi1
                  
                FROM tmutasi tm
                JOIN tuser tu ON tm.id_user = tu.id_user
                JOIN tpelanggan tpd ON tm.dari_id_pelanggan = tpd.id_pelanggan
                JOIN tmutasi1 tm1 ON tm.id_mutasi = tm1.id_mutasi
                JOIN tasset ta ON tm1.id_asset = ta.id_asset
                JOIN tbarang tb ON ta.id_barang = tb.id_barang
                JOIN tbarang1 tb1 ON tb.id_brand = tb1.id_brand
                JOIN tbarang2 tb2 ON tb.id_tipe = tb2.id_tipe
                JOIN tpelanggan tpk ON tm1.ke_id_pelanggan = tpk.id_pelanggan
                
                WHERE tm.sta = 1 AND (DATE(tm.tanggal_buat) BETWEEN '$date1' AND '$date2')
                
                GROUP BY tm.id_mutasi
              ) t1
              WHERE 
                t1.no_mutasi LIKE '$filter' OR 
                t1.usr LIKE '$filter' OR 
                t1.dari_pelanggan LIKE '$filter' OR 
                t1.keterangan LIKE '$filter' OR 
                t1.id_mutasi IN (
                  SELECT tm.id_mutasi
                  FROM tmutasi tm
                  JOIN tmutasi1 tm1 ON tm.id_mutasi = tm1.id_mutasi
                  JOIN tasset ta ON tm1.id_asset = ta.id_asset
                  JOIN tbarang tb ON ta.id_barang = tb.id_barang
                  LEFT JOIN tbarang1 tb1 ON tb.id_brand = tb1.id_brand
                  LEFT JOIN tbarang2 tb2 ON tb.id_tipe = tb2.id_tipe
                  JOIN tpelanggan tp ON tm1.ke_id_pelanggan = tp.id_pelanggan
                  WHERE tm.sta = 1 AND (
                    ta.qr_code LIKE '$filter' OR 
                    ta.serial_number LIKE '%$filter%' OR 
                    ta.no_surat_kontrak LIKE '%$filter%' OR 
                    tb.kode_barang LIKE '%$filter%' OR 
                    tb.nama_barang LIKE '%$filter%' OR 
                    tb1.nama_brand LIKE '%$filter%' OR 
                    tb2.nama_tipe LIKE '%$filter%' OR 
                    tp.kode_pelanggan LIKE '%$filter%' OR 
                    tp.nama_pelanggan LIKE '%$filter%'
                  )
                  GROUP BY tm.id_mutasi
                )
              $limit
            ")->getResult();
          case "laporan":
            $date1 = $this->db->escapeString(\func_get_arg(2));
            $date2 = $this->db->escapeString(\func_get_arg(3));
            $dari_pelanggan = $this->db->escapeString(\func_get_arg(4));
            $filter = $this->db->escapeString(\func_get_arg(5));
            return $this->db->query("
              SELECT 
                t1.id_mutasi,
                t1.no_mutasi,
                t1.usr,
                t1.dari_pelanggan,
                t1.keterangan,
                t1.tanggal_mutasi,
                t1.mutasi1
                
              FROM (	
                SELECT 
                  tm.id_mutasi,
                  tm.no_mutasi,
                  tu.nama_user AS usr,
                  tpd.nama_pelanggan AS dari_pelanggan,
                  tm.keterangan,
                  tm.tanggal_buat AS tanggal_mutasi,
                  GROUP_CONCAT(CONCAT_WS(';', ta.qr_code, IFNULL(ta.serial_number, '-'), tb1.nama_brand, IFNULL(tb2.nama_tipe, '-'), tpk.nama_pelanggan) ORDER BY ta.qr_code SEPARATOR '#') AS mutasi1
                  
                FROM tmutasi tm
                JOIN tuser tu ON tm.id_user = tu.id_user
                JOIN tpelanggan tpd ON tm.dari_id_pelanggan = tpd.id_pelanggan
                JOIN tmutasi1 tm1 ON tm.id_mutasi = tm1.id_mutasi
                JOIN tasset ta ON tm1.id_asset = ta.id_asset
                JOIN tbarang tb ON ta.id_barang = tb.id_barang
                JOIN tbarang1 tb1 ON tb.id_brand = tb1.id_brand
                LEFT JOIN tbarang2 tb2 ON tb.id_tipe = tb2.id_tipe
                JOIN tpelanggan tpk ON tm1.ke_id_pelanggan = tpk.id_pelanggan
                
                WHERE tm.sta = 1 AND 
                (DATE(tm.tanggal_buat) BETWEEN '$date1' AND '$date2') AND 
                (tpd.kode_pelanggan LIKE '%$dari_pelanggan%' OR tpd.nama_pelanggan LIKE '%$dari_pelanggan%')
                
                GROUP BY tm.id_mutasi
              ) t1
              WHERE 
                t1.no_mutasi LIKE '%$filter%' OR 
                t1.usr LIKE '%$filter%' OR 
                t1.dari_pelanggan LIKE '%$filter%' OR 
                t1.keterangan LIKE '%$filter%' OR 
                t1.id_mutasi IN (
                  SELECT tm.id_mutasi
                  FROM tmutasi tm
                  JOIN tmutasi1 tm1 ON tm.id_mutasi = tm1.id_mutasi
                  JOIN tasset ta ON tm1.id_asset = ta.id_asset
                  JOIN tbarang tb ON ta.id_barang = tb.id_barang
                  LEFT JOIN tbarang1 tb1 ON tb.id_brand = tb1.id_brand
                  JOIN tbarang2 tb2 ON tb.id_tipe = tb2.id_tipe
                  JOIN tpelanggan tp ON tm1.ke_id_pelanggan = tp.id_pelanggan
                  WHERE tm.sta = 1 AND (
                    ta.qr_code LIKE '%$filter%' OR 
                    ta.serial_number LIKE '%$filter%' OR 
                    ta.no_surat_kontrak LIKE '%$filter%' OR 
                    tb.kode_barang LIKE '%$filter%' OR 
                    tb.nama_barang LIKE '%$filter%' OR 
                    tb1.nama_brand LIKE '%$filter%' OR 
                    tb2.nama_tipe LIKE '%$filter%' OR 
                    tp.kode_pelanggan LIKE '%$filter%' OR 
                    tp.nama_pelanggan LIKE '%$filter%'
                  )
                  GROUP BY tm.id_mutasi
                )
            ")->getResult();
        }
      break;
      case 5: // input/mutasi/save
        $data1 = $this->db->table("tmutasi1")->
        select("id_asset")->
        where("id_mutasi", func_get_arg(1))->
        get()->getResult();
        $data2 = $this->db->table("tasset")->
        select("id_asset")->
        where(["dari_input" => 2, "id_input" => func_get_arg(1), "sta" => 2])->
        get()->getResult();

        return count($data1) === count($data2);
    }
  }

  function updates() {
    switch (func_get_arg(0)) {
      case 1: // tampil/mutasi_asset/delete
        $this->db->table("tmutasi")->where("id_mutasi", func_get_arg(1))->update(["sta" => 0, "alasan" => func_get_arg(2)]);
        return $this->db->affectedRows();
      case 2: // input/mutasi/save
        $id_mutasi = $this->db->escapeString(\func_get_arg(1));
        $this->db->query("
          UPDATE tasset ta SET
          ta.id_pelanggan = (SELECT dari_id_pelanggan FROM tmutasi WHERE id_mutasi = '$id_mutasi'),
          ta.id_input = (SELECT id_input_terakhir FROM tmutasi1 WHERE id_mutasi = '$id_mutasi' AND id_asset = ta.id_asset),
          ta.dari_input = (SELECT dari_input FROM tmutasi1 WHERE id_mutasi = '$id_mutasi' AND id_asset = ta.id_asset)
          WHERE id_input = '$id_mutasi' AND dari_input = 2
        ");
        return $this->db->affectedRows();
    }
  }
}
