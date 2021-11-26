<?php
namespace app\Models;

use CodeIgniter\Model;

class M_Barang_Keluar extends Model {
  protected $db;

  function __construct() {
    $this->db = db_connect();
  }

  function deletes($id_barang_keluar) {
    return $this->db->table("tbarang_keluar1")->where("id_barang_keluar", $id_barang_keluar)->delete();
  }

  function put() {
    switch (func_get_arg(0)) {
      case 1:
        $id_barang_keluar = func_get_arg(1);
        $data_barang_keluar = func_get_arg(2);
        if (!is_empty($id_barang_keluar)) $this->db->query("SET @id_barang_keluar = $id_barang_keluar");
        $this->db->query("CALL save_barang_keluar(@id_barang_keluar, ?, ?, ?, ?)", $data_barang_keluar);
        return $this->db->query("SELECT @id_barang_keluar AS id_barang_keluar")->getResult()[0]->id_barang_keluar;
      case 2:
        $data_barang_keluar1 = func_get_arg(1);
        $result = $this->db->table("tbarang_keluar1")->insertBatch($data_barang_keluar1);
        return count($data_barang_keluar1) === $result;
    }
  }

  function read() {
    switch (func_get_arg(0)) {
      case 1: 
        $type = func_get_arg(1);
        switch ($type) {
          case "no_keluar": // libraries/input/c_barang_keluar/is_valid_barang_keluar
            return $this->db->table("tbarang_keluar")->
            select("id_barang_keluar")->
            where(["no_keluar" => func_get_arg(2), "id_barang_keluar !=" => func_get_arg(3)])->
            get()->getResult();
          case "id_pengurus": // libraries/input/c_barang_keluar/is_valid_barang_keluar
            return $this->db->table("tuser")->
            select("id_user")->
            where(["id_user" => func_get_arg(2), "sta" => 1])->
            get()->getResult();
          case "id_gudang": // libraries/input/c_barang_keluar/is_valid_barang_keluar
            return $this->db->table("tgudang")->
            select("id_gudang")->
            where(["sta" => 1, "id_gudang" => func_get_arg(2), "id_kepala_gudang" => \func_get_arg(3)])->
            get()->getResult();
          case "id_asset":  // libraries/input/c_barang_keluar/is_valid_barang_keluar1
            $id_asset = $this->db->escapeString(\func_get_arg(2));
            $id_input = $this->db->escapeString(\func_get_arg(3));
            return $this->db->query("
              SELECT id_asset, id_pelanggan, id_input
              FROM tasset
              WHERE sta = 2 AND id_asset = '$id_asset' AND (id_pelanggan IS NULL OR (id_input = '$id_input' AND dari_input = 1))
            ")->getResult();
          case "id_pelanggan":  // libraries/input/c_barang_keluar/is_valid_barang_keluar1
            return $this->db->table("tpelanggan")->
            select("id_pelanggan")->
            where(["sta" => 1, "id_pelanggan" => func_get_arg(2)])->
            get()->getResult();
        }
      break;
      case 2: // input/barang_keluar/fetch&type=ajax
        $type = func_get_arg(1);
        $filter = func_get_arg(2);
        switch ($type) {
          case "barang_keluar":
            return $this->db->table("tbarang_keluar")->
            select("id_barang_keluar, no_keluar, keterangan")->
            where("sta", 1)->
            like("no_keluar", $filter)->
            limit(\get_autocomplete_limit())->
            get()->getResult();
          case "asset_gudang":
            $filter = explode(";", $this->db->escapeString($filter));
            $id_gudang = $filter[0];
            $filter_barang_keluar = is_empty($filter[1]) ? ["", ""] : [" OR tg.sta IS NULL", " OR (ta.id_input = '$filter[1]' AND ta.dari_input = 1)"];
            
            return $this->db->query("
              SELECT GROUP_CONCAT(t1.asset ORDER BY t1.id_asset SEPARATOR '#') AS asset 
              FROM (
                SELECT
                ta.id_asset, CONCAT_WS(';', ta.id_asset, ta.qr_code, CONCAT(tb1.nama_brand, ' / ', tb2.nama_tipe)) AS asset
            
                FROM tasset ta
                LEFT JOIN tgudang tg ON ta.id_gudang = tg.id_gudang
                JOIN tbarang tb ON ta.id_barang = tb.id_barang
                JOIN tbarang1 tb1 ON tb.id_brand = tb1.id_brand
                JOIN tbarang2 tb2 ON tb.id_tipe = tb2.id_tipe
                
                WHERE 
                  ta.sta = 2 AND 
                  (tg.sta = 1 $filter_barang_keluar[0]) AND 
                  tb.sta = 1 AND 
                  (tg.id_gudang = '$id_gudang' $filter_barang_keluar[1])
              ) t1
            ")->getResult();
          case "ke_pelanggan":
            return $this->db->table("tpelanggan")->
            select("id_pelanggan, CONCAT(kode_pelanggan, ' / ', nama_pelanggan) AS pelanggan")->
            where("sta", 1)->
            like("CONCAT(kode_pelanggan, ' / ', nama_pelanggan)", $filter)->
            limit(\get_autocomplete_limit())->
            get()->getResult();
          case "id_gudang":
            return $this->db->table("tgudang")->
            select("id_gudang")->
            where(["id_kepala_gudang" => $filter, "sta" => 1])->
            get()->getResult();
        }
      break;
      case 3: // input/barang_keluar/fetch&type=edit
        $id_barang_keluar = $this->db->escapeString(func_get_arg(1));
        return $this->db->query("
          SELECT 
            t1.id_barang_keluar, t1.no_keluar, t1.keterangan,
            GROUP_CONCAT(t1.barang_keluar1 ORDER BY t1.no SEPARATOR '#') AS barang_keluar1
          FROM (
            SELECT 
              tbk.id_barang_keluar, tbk.no_keluar, tbk.keterangan,
              tbk1.no, CONCAT_WS(';', tbk1.id_asset, ta.qr_code, CONCAT(tb1.nama_brand, ' / ', tb2.nama_tipe), tbk1.ke_id_pelanggan, CONCAT(tp.kode_pelanggan, ' / ', tp.nama_pelanggan)) AS barang_keluar1
            
            FROM tbarang_keluar tbk
            JOIN tbarang_keluar1 tbk1 ON tbk.id_barang_keluar = tbk1.id_barang_keluar
            JOIN tasset ta ON tbk1.id_asset = ta.id_asset
            JOIN tbarang tb ON ta.id_barang = tb.id_barang
            JOIN tbarang1 tb1 ON tb.id_brand = tb1.id_brand
            JOIN tbarang2 tb2 ON tb.id_tipe = tb2.id_tipe
            JOIN tpelanggan tp ON tbk1.ke_id_pelanggan = tp.id_pelanggan

            WHERE tbk.id_barang_keluar = '$id_barang_keluar' AND tbk.sta = 1
          ) t1
        ")->getResult();
      case 4: 
        $type = \func_get_arg(1);
        switch ($type) {
          case "period":  // tampil/barang_keluar/fetch, laporan/barang_keluar/fetch
            return $this->db->query("
              SELECT YEAR(tbk.tanggal_buat) AS tahun, tb.kode AS bulan, tb.nama AS nama_bulan
              FROM tbarang_keluar tbk
              JOIN tbulan tb ON MONTH(tbk.tanggal_buat) = tb.kode
              WHERE tbk.sta != 0
              GROUP BY YEAR(tbk.tanggal_buat), MONTH(tbk.tanggal_buat)
              ORDER BY tbk.tanggal_buat DESC
            ")->getResult();
          case "barang_keluar": // tampil/barang_keluar/fetch
            $date1 = $this->db->escapeString(\func_get_arg(2));
            $date2 = $this->db->escapeString(func_get_arg(3));
            $filter = $this->db->escapeLikeString(\func_get_arg(4));
            $id_user = $this->db->escapeString(\func_get_arg(5));
            $v1 = \func_get_arg(6);
            if (!$v1) $v1 = " AND tbk.id_pengurus = '$id_user'";
            else $v1 = "";
            $limit = "";
            if (!is_empty(\func_get_arg(7))) {
              $page = $this->db->escapeString(\func_get_arg(7));
              $display_per_page = $this->db->escapeString(\func_get_arg(8));
              $limit = "LIMIT " .($page * $display_per_page). ", $display_per_page";
            }
            return $this->db->query("
              SELECT 
                t1.id_barang_keluar,
                t1.no_keluar,
                t1.pengurus,
                t1.gudang,
                t1.keterangan,
                t1.tanggal_keluar,
                t1.can_edit,
                t1.barang_keluar1
              FROM (
                SELECT 
                  tbk.id_barang_keluar,
                  tbk.no_keluar,
                  CONCAT(tu.kode_user, ' / ', tu.nama_user) AS pengurus,
                  CONCAT(tg.kode_gudang, ' / ', tg.nama_gudang) AS gudang,
                  tbk.keterangan,
                  tbk.tanggal_buat AS tanggal_keluar,
                  IF(COUNT(*) = (SELECT COUNT(*) FROM tasset WHERE id_input = tbk.id_barang_keluar AND dari_input = 1), 1, 0) AS can_edit,
                  GROUP_CONCAT(CONCAT_WS(';', ta.qr_code, tb1.nama_brand, tb2.nama_tipe) ORDER BY ta.qr_code SEPARATOR '#') AS barang_keluar1
                  
                  
                FROM tbarang_keluar tbk
                JOIN tuser tu ON tbk.id_pengurus = tu.id_user
                JOIN tgudang tg ON tbk.id_gudang = tg.id_gudang
                JOIN tbarang_keluar1 tbk1 ON tbk.id_barang_keluar = tbk1.id_barang_keluar
                JOIN tasset ta ON tbk1.id_asset = ta.id_asset
                JOIN tbarang tb ON ta.id_barang = tb.id_barang
                JOIN tbarang1 tb1 ON tb.id_brand = tb1.id_brand
                JOIN tbarang2 tb2 ON tb.id_tipe = tb2.id_tipe
                
                WHERE tbk.sta = 1 AND (DATE(tbk.tanggal_buat) BETWEEN '$date1' AND '$date2') $v1
                
                GROUP BY tbk.id_barang_keluar
              ) t1
              WHERE 
                t1.no_keluar LIKE '%$filter%' OR 
                t1.pengurus LIKE '%$filter%' OR 
                t1.gudang LIKE '%$filter%' OR 
                t1.keterangan LIKE '%$filter%' OR 
                t1.id_barang_keluar IN (
                  SELECT tbk.id_barang_keluar
                  FROM tbarang_keluar tbk
                  JOIN tbarang_keluar1 tbk1 ON tbk.id_barang_keluar = tbk1.id_barang_keluar
                  JOIN tasset ta ON tbk1.id_asset = ta.id_asset
                  JOIN tbarang tb ON ta.id_barang = tb.id_barang
                  LEFT JOIN tbarang1 tb1 ON tb.id_brand = tb1.id_brand
                  JOIN tbarang2 tb2 ON tb.id_tipe = tb2.id_tipe
                  WHERE 
                    ta.qr_code LIKE '%$filter%' OR 
                    ta.serial_number LIKE '%$filter%' OR 
                    ta.no_surat_kontrak LIKE '%$filter%' OR 
                    tb.kode_barang LIKE '%$filter%' OR 
                    tb.nama_barang LIKE '%$filter%' OR 
                    tb1.nama_brand LIKE '%$filter%' OR 
                    tb2.nama_tipe LIKE '%$filter%'
                )
              $limit
            ")->getResult();
          case "laporan": // laporan/barang_keluar/print
            $date1 = $this->db->escapeString(\func_get_arg(2));
            $date2 = $this->db->escapeString(\func_get_arg(3));
            $filter = $this->db->escapeString(\func_get_arg(4));
            $id_user = $this->db->escapeString(\func_get_arg(5));
            $r1 = \func_get_arg(6);
            $r1 = !$r1 ? " AND tu.id_user = '$id_user'" : "";
            
            return $this->db->query("
              SELECT 
                t1.id_barang_keluar,
                t1.no_keluar,
                t1.pengurus,
                t1.gudang,
                t1.keterangan,
                t1.tanggal_keluar,
                t1.barang_keluar1
              FROM (
                SELECT 
                  tbk.id_barang_keluar, 
                  tbk.no_keluar,
                  tu.nama_user AS pengurus,
                  tg.nama_gudang AS gudang,
                  tbk.keterangan,
                  tbk.tanggal_buat AS tanggal_keluar,
                  GROUP_CONCAT(
                    CONCAT_WS(';', ta.qr_code, IFNULL(ta.serial_number, '-'), tb1.nama_brand, tb2.nama_tipe) ORDER BY ta.qr_code SEPARATOR'#'
                  ) AS barang_keluar1
                  
                FROM tbarang_keluar tbk
                JOIN tuser tu ON tbk.id_pengurus = tu.id_user
                JOIN tgudang tg ON tbk.id_gudang = tg.id_gudang
                JOIN tbarang_keluar1 tbk1 ON tbk.id_barang_keluar = tbk1.id_barang_keluar
                JOIN tasset ta ON tbk1.id_asset = ta.id_asset
                JOIN tbarang tb ON ta.id_barang = tb.id_barang
                JOIN tbarang1 tb1 ON tb.id_brand = tb1.id_brand
                JOIN tbarang2 tb2 ON tb.id_tipe = tb2.id_tipe
                
                WHERE tbk.sta = 1 AND (DATE(tbk.tanggal_buat) BETWEEN '$date1' AND '$date2') $r1
                
                GROUP BY tbk.id_barang_keluar
              ) t1
              WHERE 
                t1.no_keluar LIKE '%$filter%' OR 
                t1.pengurus LIKE '%$filter%' OR 
                t1.gudang LIKE '%$filter%' OR 
                t1.keterangan LIKE '%$filter%' OR 
                t1.id_barang_keluar IN (
                  SELECT tbk.id_barang_keluar
                  FROM tbarang_keluar tbk
                  JOIN tbarang_keluar1 tbk1 ON tbk.id_barang_keluar = tbk1.id_barang_keluar
                  JOIN tasset ta ON tbk1.id_asset = ta.id_asset
                  JOIN tbarang tb ON ta.id_barang = tb.id_barang
                  LEFT JOIN tbarang1 tb1 ON tb.id_brand = tb1.id_brand
                  LEFT JOIN tbarang2 tb2 ON tb.id_tipe = tb2.id_tipe
                  WHERE 
                    ta.qr_code LIKE '%$filter%' OR 
                    ta.serial_number LIKE '%$filter%' OR 
                    ta.no_surat_kontrak LIKE '%$filter%' OR 
                    tb.kode_barang LIKE '%$filter%' OR 
                    tb.nama_barang LIKE '%$filter%' OR 
                    tb1.nama_brand LIKE '%$filter%' OR 
                    tb2.nama_tipe LIKE '%$filter%'
                )
            ")->getResult();
        }
      break;
      case 5: // input/barang_keluar/save, tampil/barang_keluar/delete
        $data1 = $this->db->table("tbarang_keluar1")->
        select("id_asset")->
        where("id_barang_keluar", func_get_arg(1))->
        get()->getResult();
        $data2 = $this->db->table("tasset")->
        select("id_asset")->
        where(["dari_input" => 1, "id_input" => func_get_arg(1), "sta" => 2])->
        get()->getResult();

        return count($data1) === count($data2);
      case  6:  // input/barang_keluar/save
        $id_barang_keluar = \func_get_arg(1);
        $id_user = \func_get_arg(2);
        return $this->db->query("
          SELECT tg.id_gudang
          FROM tgudang tg
          JOIN tbarang_keluar tbk ON tg.id_gudang = tbk.id_gudang
          WHERE 
            tg.id_kepala_gudang = '$id_user' AND 
            tg.sta = 1 AND 
            tbk.id_barang_keluar = '$id_barang_keluar' AND 
            tbk.id_gudang = tg.id_gudang AND
            tbk.sta = 1
        ")->getResult();
      case 7: // tampil/barang_keluar/print
        $id_barang_keluar = $this->db->escapeString(\func_get_arg(1));
        return $this->db->query("
          SELECT 
            tbk.id_barang_keluar,
            tbk.no_keluar, 
            NULL AS qr_code, NULL AS serial_number, NULL AS nama_barang, NULL AS tipe, NULL AS ukuran	
          FROM tbarang_keluar tbk
          WHERE tbk.id_barang_keluar = '$id_barang_keluar'
          
          UNION ALL 
          SELECT 
            tbk1.id_barang_keluar,
            NULL AS no_keluar,
            ta.qr_code,
            ta.serial_number,
            tb1.nama_brand,
            tb2.nama_tipe,
            tb.ukuran
            
            FROM tbarang_keluar1 tbk1
            JOIN tasset ta ON tbk1.id_asset = ta.id_asset
            JOIN tbarang tb ON ta.id_barang = tb.id_barang
            JOIN tbarang1 tb1 ON tb.id_brand = tb1.id_brand
            JOIN tbarang2 tb2 ON tb.id_tipe = tb2.id_tipe
          WHERE tbk1.id_barang_keluar = '$id_barang_keluar'
        ")->getResult();
    }
  }

  function updates() {
    switch(func_get_arg(0)) {
      case 1: // tampil/barang_keluar/delete
        $this->db->table("tbarang_keluar")->where("id_barang_keluar", func_get_arg(1))->update(["sta" => 0, "alasan" => func_get_arg(2)]);
        return $this->db->affectedRows();
      case 2: // input/barang_keluar/save
        $id_barang_keluar = $this->db->escapeString(\func_get_arg(1));
        $this->db->query("
          UPDATE tasset ta SET
          id_gudang = (SELECT id_gudang FROM tbarang_keluar WHERE id_barang_keluar = '$id_barang_keluar'),
          ta.id_pelanggan = NULL,
          ta.id_input = (SELECT id_input_terakhir FROM tbarang_keluar1 WHERE id_barang_keluar = '$id_barang_keluar' AND id_asset = ta.id_asset),
          ta.dari_input = IF((SELECT id_input_terakhir FROM tbarang_keluar1 WHERE id_barang_keluar = '$id_barang_keluar' AND id_asset = ta.id_asset) IS NULL, NULL, 0)
          WHERE id_input = '$id_barang_keluar' AND dari_input = 1
        ");
        return $this->db->affectedRows();
    }
  }
}
