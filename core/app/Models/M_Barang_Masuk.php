<?php
namespace app\Models;

use CodeIgniter\Model;

class M_Barang_Masuk extends Model {
  protected $db;

  function __construct() {
    $this->db = db_connect();
  }

  // input/barang_masuk/save
  function deletes() {
    switch (\func_get_arg(0)) {
      case 1:
        $id_barang_masuk = $this->db->escapeString(func_get_arg(1));
        $this->db->query("
          DELETE tbm1 
          FROM tbarang_masuk1 tbm1
          JOIN tasset ta ON tbm1.id_asset = ta.id_asset
          WHERE tbm1.id_barang_masuk = '$id_barang_masuk' AND ta.id_input = '$id_barang_masuk' AND ta.dari_input = 0
        ")->getResult(); 
        return $this->db->affectedRows();
      case 2:
        return $this->db->table("tasset")->where(["dari_input" => 0, "id_input" => func_get_arg(1)])->delete();
    }
  }

  function put() {
    switch (\func_get_arg(0)) {
      case 1: // input/barang_masuk/save
        $id_barang_masuk = \func_get_arg(1);
        $data_barang_masuk = \func_get_arg(2);
        if (!is_empty($id_barang_masuk)) $this->db->query("SET @id_barang_masuk = $id_barang_masuk");
        $this->db->query("CALL save_barang_masuk(@id_barang_masuk, ?, ?, ?, ?, ?, ?, ?, ?)", $data_barang_masuk);
        return $this->db->query("SELECT @id_barang_masuk AS id_barang_masuk")->getResult()[0]->id_barang_masuk;
      case 2: // input/barang_masuk/save
        $data_barang_masuk1 = \func_get_arg(1);
        $result = $this->db->table("tbarang_masuk1")->insertBatch($data_barang_masuk1);
        return count($data_barang_masuk1) === $result;
      case 3: // libraries/c_barang_masuk/is_valid_barang_masuk1
        $data_asset = \func_get_arg(1);
        return $this->db->table("tasset")->insert($data_asset);
    }
  }

  function read() {
    switch (\func_get_arg(0)) {
      case 1:
        $type = \func_get_arg(1);
        switch ($type) {
          case "id_barang_masuk": // libraries/c_barang_masuk/is_valid_barang_masuk
            return $this->db->table("tbarang_masuk")->
            select("id_barang_masuk")->
            where(["id_barang_masuk" => func_get_arg(2), "sta !=" => 0])->
            get()->getResult();
          case "no_masuk":  // libraries/c_barang_masuk/is_valid_barang_masuk
            return $this->db->table("tbarang_masuk")->
            select("id_barang_masuk")->
            where(["no_masuk" => \func_get_arg(2), "id_barang_masuk !=" => \func_get_arg(3)])->
            get()->getResult();
          case "no_faktur": // libraries/c_barang_masuk/is_valid_barang_masuk
            return $this->db->table("tbarang_masuk")->
            select("id_barang_masuk")->
            where(["no_faktur" => \func_get_arg(2), "no_faktur !=" => null, "id_barang_masuk !=" => \func_get_arg(3), "sta" => 1])->
            get()->getResult();
          case "id_penerima": // libraries/c_barang_masuk/is_valid_barang_masuk
            return $this->db->table("tuser")->
            select("id_user, id_level")->
            where(["sta" => 1, "id_user" => func_get_arg(2)])->
            get()->getResult();
          case "id_pesanan":  // libraries/c_barang_masuk/is_valid_barang_masuk
            return $this->db->table("tpesanan")->select("id_pesanan")->where(["id_pesanan" => \func_get_arg(2), "sta" => 1])->get()->getResult();
          case "ke_id_gudang":  // libraries/c_barang_masuk/__construct
            return $this->db->table("tgudang")->
            select("id_gudang")->
            where(["id_kepala_gudang" => func_get_arg(2), "sta" => 1])->
            limit(1)->
            get()->getResult();
          case "dari_id_pelanggan":  // libraries/c_barang_masuk/is_valid_barang_masuk
            return $this->db->table("tasset")->
            select("id_asset, id_input, dari_input")->
            where(["id_asset" => \func_get_arg(2), "id_pelanggan" => \func_get_arg(3), "sta" => 2])->
            get()->getResult();
          case "ke_id_agen":  // libraries/c_barang_masuk/is_valid_barang_masuk
            return $this->db->table("tpelanggan")->select("id_pelanggan")->where(["id_pelanggan" => func_get_arg(2), "id_level" => 2, "sta => 1"])->get()->getResult();
          case "id_asset":  // libraries/c_barang_masuk/is_valid_barang_masuk1
            return $this->db->table("tasset")->
            select("id_asset, id_input, dari_input")->
            where(["id_asset" => \func_get_arg(2), "sta" => 2])->
            get()->getResult();
          case "last_sisa_pesanan":  // libraries/c_barang_masuk/is_valid_barang_masuk
            $id_barang_masuk = $this->db->escapeString(func_get_arg(2));
            return $this->db->query("
              SELECT 
                tp.id_pesanan,
                SUM(tp1.qty) AS qty_pesanan,
                t1.id_barang_masuk,
                IFNULL(t1.qty_masuk, 0) AS qty_masuk,
                IFNULL(SUM(tp1.qty) - IFNULL(t1.qty_masuk, 0), 0) AS qty_sisa
              
              FROM tpesanan tp
              JOIN tpesanan1 tp1 ON tp.id_pesanan = tp1.id_pesanan
              LEFT JOIN (
                SELECT tbm.id_barang_masuk, tbm.dari_id_pesanan, tbm.sta, COUNT(*) AS qty_masuk
                FROM tbarang_masuk tbm
                JOIN tbarang_masuk1 tbm1 ON tbm.id_barang_masuk = tbm1.id_barang_masuk
                JOIN tasset ta ON tbm1.id_asset = ta.id_asset
                WHERE 
                  tbm.id_barang_masuk = '$id_barang_masuk' AND 
                  tbm.sta != 0 AND 
                  (ta.dari_input != 0 OR ta.id_input != tbm.id_barang_masuk)
              ) t1 ON tp1.id_pesanan = t1.dari_id_pesanan
              
              WHERE tp.id_pesanan = (SELECT dari_id_pesanan FROM tbarang_masuk WHERE id_barang_masuk = '$id_barang_masuk') AND tp.sta = 1
            ")->getResult();
          case "last_no": // libraries/c_barang_masuk/is_valid_barang_masuk1
            return $this->db->table("tbarang_masuk1")->
            select("IFNULL(MAX(no), 0) AS last_no")->
            where("id_barang_masuk", func_get_arg(2))->
            get()->getResult();
          case "id_barang": // libraries/c_barang_masuk/is_valid_barang_masuk1
            return $this->db->table("tbarang")->
            select("id_barang")->
            where(["id_barang" => func_get_arg(2), "sta" => 1])->
            get()->getResult();
        }
      break;
      case 2: // input/barang_masuk/fetch&type=ajax
        $type = \func_get_arg(1);
        $filter = \func_get_arg(2);
        switch ($type) {
          case "pesanan":
            $filter = explode(";", $this->db->escapeLikeString($filter));
            return $this->db->query("
              SELECT t1.id_pesanan, t1.no_po, GROUP_CONCAT(t1.pesanan1 ORDER BY t1.id_barang SEPARATOR '#') AS pesanan1
              FROM (
                SELECT 
                  tp.id_pesanan, 
                  tp.no_po,
                  tp1.id_barang, CONCAT_WS(';', tp1.id_barang, CONCAT(tb1.nama_brand, ' / ', tb2.nama_tipe), (tp1.qty - IFNULL(t1.qty_masuk, 0))) AS pesanan1
                  
                FROM tpesanan tp
                JOIN tpesanan1 tp1 ON tp.id_pesanan = tp1.id_pesanan
                JOIN tbarang tb ON tp1.id_barang = tb.id_barang
                JOIN tbarang1 tb1 ON tb.id_brand = tb1.id_brand
                JOIN tbarang2 tb2 ON tb.id_tipe = tb2.id_tipe
                LEFT JOIN (
                  SELECT 
                    tbm.id_barang_masuk, tbm.dari_id_pesanan, tbm1.id_barang, COUNT(*) AS qty_masuk
                  FROM tbarang_masuk tbm
                  JOIN tbarang_masuk1 tbm1 ON tbm.id_barang_masuk = tbm1.id_barang_masuk
                  JOIN tasset ta ON tbm1.id_asset = ta.id_asset
                  WHERE 
                    tbm.id_barang_masuk = '$filter[1]' AND 
                    tbm.tipe IN (0, 2) AND  
                    tbm.sta != 0 AND
                    (ta.dari_input != 0 OR ta.id_input != '$filter[1]') AND
                    ta.sta = 2
                  GROUP BY tbm1.id_barang_masuk, tbm1.id_barang
                ) t1 ON tp.id_pesanan = t1.dari_id_pesanan AND tp1.id_barang = t1.id_barang
                
                WHERE 
                  tp.no_po LIKE '%$filter[0]%' AND 
                  tp.sta = 1 AND 
                  tp.id_pesanan NOT IN (
                    SELECT dari_id_pesanan FROM tbarang_masuk 
                    WHERE dari_id_pesanan IS NOT NULL AND id_barang_masuk != '$filter[1]' AND sta != 0
                  )
              ) t1
              GROUP BY t1.id_pesanan
              LIMIT " .\get_autocomplete_limit(). "
            ")->getResult();
          case "ke_agen":
            return $this->db->table("tpelanggan")->
            select("id_pelanggan AS id_agen, CONCAT(kode_pelanggan, ' / ', nama_pelanggan) AS agen, alamat")->
            where(["id_level" => 2, "sta" => 1])->
            like("CONCAT(kode_pelanggan, ' / ', nama_pelanggan)", $filter)->
            limit(\get_autocomplete_limit())->
            get()->getResult();
          case "dari_pelanggan":
            $filter = $this->db->escapeLikeString($filter);
            return $this->db->query("
              SELECT 
                ta.id_asset, ta.qr_code, tb.id_barang, CONCAT(tb.kode_barang, ' / ', tb.nama_barang) AS barang, 
                ta.id_pelanggan, CONCAT(tp.kode_pelanggan, ' / ', tp.nama_pelanggan) AS pelanggan,
                tp.alamat
              FROM tasset ta
              JOIN tbarang tb ON ta.id_barang = tb.id_barang
              JOIN tpelanggan tp ON ta.id_pelanggan = tp.id_pelanggan
              WHERE ta.qr_code LIKE '%$filter%' AND ta.sta = 2
              LIMIT " .\get_autocomplete_limit(). "
            ")->getResult();
          case "asset":
            $filter = $this->db->escapeLikeString($filter);
            return $this->db->table("tasset ta")->
            join("tgudang tg", "ta.id_gudang = tg.id_gudang")->
            select("tg.nama_gudang")->
            where(["ta.qr_code" => $filter, "ta.sta" => 2, "tg.sta !=" => 0])->
            get()->getResult();
          // case "ke_gudang":
          //   return $this->db->table("tgudang")->
          //   select("id_gudang, CONCAT(kode_gudang, ' / ', nama_gudang) AS gudang")->
          //   where("sta", 1)->
          //   groupStart()->
          //   orLike(["kode_gudang" => $filter, "nama_gudang" => $filter, "CONCAT(kode_gudang, ' / ', nama_gudang)" => $filter])->
          //   groupEnd()->
          //   limit(\get_autocomplete_limit())->
          //   get()->getResult();
        }
      break;
      case 3: // input/barang_masuk/fetch&type=edit
        $id_barang_masuk = $this->db->escapeString(\func_get_arg(1));
        return $this->db->query("
          SELECT 
            t1.id_barang_masuk, t1.no_masuk, t1.tipe, 
            t1.no_faktur, t1.dari_id_pesanan, t1.no_po, 
            t1.ke_id_agen, t1.ke_agen, t1.alamat,
            t1.keterangan,
            GROUP_CONCAT(t1.barang_masuk1 ORDER BY t1.no SEPARATOR '#') AS barang_masuk1
            FROM (
              SELECT 
                tbm.id_barang_masuk, tbm.no_masuk, tbm.tipe, 
                tbm.no_faktur, tbm.dari_id_pesanan, tpsn.no_po, 
                tbm.ke_id_agen, CONCAT(tag.kode_pelanggan, ' / ', tag.nama_pelanggan) AS ke_agen, tag.alamat,
                tbm.keterangan, 
                tbm1.no, CONCAT_WS(';', IF(tbm.dari_id_pesanan IS NOT NULL, tb.id_barang, tbm1.id_asset), ta.qr_code, 
                CONCAT(tb1.nama_brand, ' / ', tb2.nama_tipe), tbm1.dari_id_pelanggan, 
                CONCAT(tpgn.kode_pelanggan, ' / ', tpgn.nama_pelanggan)) AS barang_masuk1
              
              FROM tbarang_masuk tbm
              JOIN tbarang_masuk1 tbm1 ON tbm.id_barang_masuk = tbm1.id_barang_masuk
              LEFT JOIN tpesanan tpsn ON tbm.dari_id_pesanan = tpsn.id_pesanan
              LEFT JOIN tpelanggan tag ON tbm.ke_id_agen = tag.id_pelanggan
              JOIN tasset ta ON tbm1.id_asset = ta.id_asset
              JOIN tbarang tb ON ta.id_barang = tb.id_barang
              JOIN tbarang1 tb1 ON tb.id_brand = tb1.id_brand
              JOIN tbarang2 tb2 ON tb.id_tipe = tb2.id_tipe
              LEFT JOIN tpelanggan tpgn ON tbm1.dari_id_pelanggan = tpgn.id_pelanggan
              
              WHERE 
              tbm.id_barang_masuk = '$id_barang_masuk' AND 
              tbm.sta != 0 AND 
              ta.dari_input = 0 AND 
              ta.id_input = '$id_barang_masuk' AND 
              ta.sta = 2
          ) t1
        ")->getResult();
      break;
      case 4: // tampil/barang_masuk/fetch
        $type = \func_get_arg(1);
        switch ($type) {
          case "period":
            return $this->db->query("
              SELECT YEAR(tbm.tanggal_buat) AS tahun, tb.kode AS bulan, tb.nama AS nama_bulan
              FROM tbarang_masuk tbm
              JOIN tbulan tb ON MONTH(tbm.tanggal_buat) = tb.kode
              WHERE tbm.sta != 0
              GROUP BY YEAR(tbm.tanggal_buat), MONTH(tbm.tanggal_buat)
              ORDER BY tbm.tanggal_buat DESC
            ")->getResult();
          case "barang_masuk":
            $date1 = $this->db->escapeString(\func_get_arg(2));
            $date2 = $this->db->escapeString(func_get_arg(3));
            $filter = $this->db->escapeLikeString(\func_get_arg(4));
            $tipe = \func_get_arg(5);
            if (is_empty($tipe)) $tipe = "";
            else $tipe = "AND tbm.tipe = '" .$this->db->escapeString($tipe). "'"; 
            $id_user = $this->db->escapeString(\func_get_arg(6));
            $v1 = \func_get_arg(7);
            if (!$v1) $v1 = " AND tbm.id_penerima = '$id_user'";
            else $v1 = "";
            $limit = "";
            if (!is_empty(\func_get_arg(8))) {
              $page = $this->db->escapeString(\func_get_arg(8));
              $display_per_page = $this->db->escapeString(\func_get_arg(9));
              $limit = "LIMIT " .($page * $display_per_page). ", $display_per_page";
            }
            return $this->db->query("
              SELECT 
                t1.id_barang_masuk,
                t1.no_masuk,
                t1.tipe,
                t1.penerima,
                t1.no_faktur,
                t1.no_po,
                t1.ke_gudang,
                t1.ke_agen,
                t1.keterangan,
                t1.tanggal_masuk,
                t1.qty_pesan,
                t1.qty_masuk,
                t1.can_edit,
                t1.can_delete,
                t1.barang_masuk1
              FROM (
                SELECT 
                  t1.id_barang_masuk,
                  t1.no_masuk,
                  t1.tipe,
                  t1.penerima,
                  t1.no_faktur,
                  t1.no_po,
                  t1.ke_gudang,
                  t1.ke_agen,
                  t1.keterangan,
                  t1.tanggal_masuk,
                  t1.qty_pesan,
                  COUNT(*) AS qty_masuk,
                  IF(t1.sta = 2 OR COUNT(*) = (SELECT COUNT(*) FROM tasset WHERE id_input = t1.id_barang_masuk AND dari_input = 0), 1, 0) AS can_edit,
	                IF(COUNT(*) = (SELECT COUNT(*) FROM tasset WHERE id_input = t1.id_barang_masuk AND dari_input = 0 AND sta != 0), 1, 0) AS can_delete,
                  GROUP_CONCAT(CONCAT_WS(';', t1.qr_code, t1.nama_brand, t1.nama_tipe, t1.dari_pelanggan) ORDER BY t1.qr_code SEPARATOR '#') AS barang_masuk1	
                FROM (
                  SELECT 
                    tbm.id_barang_masuk,
                    tbm.no_masuk,
                    tbm.tipe,
                    CONCAT(tup.kode_user, ' / ', tup.nama_user) AS penerima,
                    tbm.no_faktur,
                    tp.no_po,
                    CONCAT(tg.kode_gudang, ' / ', tg.nama_gudang) AS ke_gudang,
                    CONCAT(tpa.kode_pelanggan, ' / ', tpa.nama_pelanggan) AS ke_agen,
                    tbm.keterangan,
                    tbm.tanggal_buat AS tanggal_masuk,
                    tbm.sta,
                    SUM(tp1.qty) AS qty_pesan,
                    ta.qr_code,
                    tb1.nama_brand, tb2.nama_tipe,
                    IFNULL(CONCAT(tpg.kode_pelanggan, ' / ', tpg.nama_pelanggan), '-') AS dari_pelanggan
                    
                  FROM tbarang_masuk tbm
                  JOIN tuser tup ON tbm.id_penerima = tup.id_user
                  LEFT JOIN tgudang tg ON tbm.ke_id_gudang = tg.id_gudang
                  LEFT JOIN tpelanggan tpa ON tbm.ke_id_agen = tpa.id_pelanggan
                  JOIN tbarang_masuk1 tbm1 ON tbm.id_barang_masuk = tbm1.id_barang_masuk
                  LEFT JOIN tpesanan tp ON tbm.dari_id_pesanan = tp.id_pesanan
                  LEFT JOIN tpesanan1 tp1 ON tp.id_pesanan = tp1.id_pesanan
                  JOIN tasset ta ON tbm1.id_asset = ta.id_asset
                  JOIN tbarang tb ON ta.id_barang = tb.id_barang
                  JOIN tbarang1 tb1 ON tb.id_brand = tb1.id_brand
                  JOIN tbarang2 tb2 ON tb.id_tipe = tb2.id_tipe
                  LEFT JOIN tpelanggan tpg ON tbm1.dari_id_pelanggan = tpg.id_pelanggan
                  
                  WHERE 
                    tbm.sta != 0 AND (DATE(tbm.tanggal_buat) BETWEEN '$date1' AND '$date2') $tipe $v1
                  
                  GROUP BY tbm.id_barang_masuk, tbm1.id_asset, tp.id_pesanan
                ) t1
                GROUP BY t1.id_barang_masuk
              ) t1
              WHERE
                t1.no_masuk LIKE '%$filter%' OR
                t1.penerima LIKE '%$filter%' OR
                t1.no_faktur LIKE '%$filter%' OR 
                t1.no_po LIKE '%$filter%' OR 
                t1.ke_gudang LIKE '%$filter%' OR
                t1.ke_agen LIKE '%$filter%' OR
                t1.keterangan LIKE '%$filter%' OR
                t1.qty_pesan = '%$filter%' OR
                t1.qty_masuk = '%$filter%' OR
                t1.id_barang_masuk IN (
                  SELECT tbm.id_barang_masuk 
                  FROM tbarang_masuk tbm
                  JOIN tbarang_masuk1 tbm1 ON tbm.id_barang_masuk = tbm1.id_barang_masuk
                  JOIN tasset ta ON tbm1.id_asset = ta.id_asset
                  JOIN tbarang tb ON ta.id_barang = tb.id_barang
                  LEFT JOIN tbarang1 tb1 ON tb.id_brand = tb1.id_brand
                  LEFT JOIN tbarang2 tb2 ON tb.id_tipe = tb2.id_tipe
                  WHERE tbm.sta != 0 AND (
                    ta.qr_code LIKE '%$filter%' OR 
                    ta.serial_number LIKE '%$filter%' OR 
                    ta.no_surat_kontrak LIKE '%$filter%' OR 
                    tb.kode_barang LIKE '%$filter%' OR 
                    tb.nama_barang LIKE '%$filter%' OR 
                    tb1.nama_brand LIKE '%$filter%' OR 
                    tb2.nama_tipe LIKE '%$filter%'
                  )
                )
              $limit
            ")->getResult();
          case "laporan":
            $date1 = $this->db->escapeString(func_get_arg(2));
            $date2 = $this->db->escapeString(func_get_arg(3));
            $tipe = $this->db->escapeString(func_get_arg(4));
            $tipe = !is_empty($tipe) ? " AND tbm.tipe = '$tipe'" : ""; 
            $filter = $this->db->escapeString(func_get_arg(5));
            $id_user = $this->db->escapeString(\func_get_arg(6));
            $r1 = $this->db->escapeString(\func_get_arg(7));
            $r1 = !$r1 ? " AND tup.id_user = '$id_user'" : "";
            
            return $this->db->query("
              SELECT 
                t1.id_barang_masuk,
                t1.no_masuk,
                t1.tipe,
                t1.penerima,
                t1.no_faktur,
                t1.no_po,
                t1.ke_gudang,
                t1.ke_agen,
                t1.keterangan,
                t1.tanggal_masuk,
                t1.qty_pesan,
                t1.qty_masuk,
                t1.barang_masuk1
              FROM (
                SELECT 
                  t1.id_barang_masuk,
                  t1.no_masuk,
                  t1.tipe,
                  t1.penerima,
                  t1.no_faktur,
                  t1.no_po,
                  t1.ke_gudang,
                  t1.ke_agen,
                  t1.keterangan,
                  t1.tanggal_masuk,
                  t1.qty_pesan,
                  COUNT(*) AS qty_masuk,
                  GROUP_CONCAT(CONCAT_WS(';', t1.qr_code, t1.serial_number, t1.nama_brand, t1.nama_tipe, t1.dari_pelanggan) ORDER BY t1.qr_code SEPARATOR '#') AS barang_masuk1	
                FROM (
                  SELECT 
                    tbm.id_barang_masuk,
                    tbm.no_masuk,
                    tbm.tipe,
                    tup.nama_user AS penerima,
                    tbm.no_faktur,
                    tp.no_po,
                    tg.nama_gudang AS ke_gudang,
                    tpa.nama_pelanggan AS ke_agen,
                    tbm.keterangan,
                    tbm.tanggal_buat AS tanggal_masuk,
                    tbm.sta,
                    SUM(tp1.qty) AS qty_pesan,
                    ta.qr_code,
                    IFNULL(ta.serial_number, '-') AS serial_number,
                    IFNULL(tb1.nama_brand, '-') AS nama_brand,
                    IFNULL(tb2.nama_tipe, '-') AS nama_tipe,
                    IFNULL(tpg.nama_pelanggan, '-') AS dari_pelanggan
                    
                  FROM tbarang_masuk tbm
                  JOIN tuser tup ON tbm.id_penerima = tup.id_user
                  LEFT JOIN tgudang tg ON tbm.ke_id_gudang = tg.id_gudang
                  LEFT JOIN tpelanggan tpa ON tbm.ke_id_agen = tpa.id_pelanggan
                  JOIN tbarang_masuk1 tbm1 ON tbm.id_barang_masuk = tbm1.id_barang_masuk
                  LEFT JOIN tpesanan tp ON tbm.dari_id_pesanan = tp.id_pesanan
                  LEFT JOIN tpesanan1 tp1 ON tp.id_pesanan = tp1.id_pesanan
                  JOIN tasset ta ON tbm1.id_asset = ta.id_asset
                  JOIN tbarang tb ON ta.id_barang = tb.id_barang
                  LEFT JOIN tbarang1 tb1 ON tb.id_brand = tb1.id_brand
                  LEFT JOIN tbarang2 tb2 ON tb.id_tipe = tb2.id_tipe
                  LEFT JOIN tpelanggan tpg ON tbm1.dari_id_pelanggan = tpg.id_pelanggan
                  
                  WHERE 
                    tbm.sta != 0 AND (DATE(tbm.tanggal_buat) BETWEEN '$date1' AND '$date2') $tipe $r1
                  
                  GROUP BY tbm.id_barang_masuk, tbm1.id_asset, tp.id_pesanan
                ) t1
                GROUP BY t1.id_barang_masuk
              ) t1
              WHERE
                t1.no_masuk LIKE '%$filter%' OR
                t1.penerima LIKE '%$filter%' OR
                t1.no_faktur LIKE '%$filter%' OR 
                t1.no_po LIKE '%$filter%' OR 
                t1.ke_gudang LIKE '%$filter%' OR
                t1.ke_agen LIKE '%$filter%' OR
                t1.keterangan LIKE '%$filter%' OR
                t1.qty_pesan = '%$filter%' OR
                t1.qty_masuk = '%$filter%' OR
                t1.id_barang_masuk IN (
                  SELECT tbm.id_barang_masuk 
                  FROM tbarang_masuk tbm
                  JOIN tbarang_masuk1 tbm1 ON tbm.id_barang_masuk = tbm1.id_barang_masuk
                  JOIN tasset ta ON tbm1.id_asset = ta.id_asset
                  JOIN tbarang tb ON ta.id_barang = tb.id_barang
                  LEFT JOIN tbarang1 tb1 ON tb.id_brand = tb1.id_brand
                  LEFT JOIN tbarang2 tb2 ON tb.id_tipe = tb2.id_tipe
                  WHERE tbm.sta != 0 AND (
                    ta.qr_code LIKE '%$filter%' OR 
                    ta.serial_number LIKE '%$filter%' OR 
                    ta.no_surat_kontrak LIKE '%$filter%' OR 
                    tb.kode_barang LIKE '%$filter%' OR 
                    tb.nama_barang LIKE '%$filter%' OR 
                    tb1.nama_brand LIKE '%$filter%' OR 
                    tb2.nama_tipe LIKE '%$filter%'
                  )
                )
            ")->getResult();
        }
      break;
      case 5: // input/barang_masuk/fetch, input/barang_masuk/save, tampil/barang_masuk/delete
        $type = func_get_arg(1);
        $id_barang_masuk = func_get_arg(2);
        if ($type === "update") {
          $data = $this->db->table("tbarang_masuk")->
          select("id_barang_masuk")->
          where(["id_barang_masuk" => func_get_arg(2), "sta" => 2])->
          get()->getResult();
          if (!empty($data)) return true;
        }

        $data1 = $this->db->table("tbarang_masuk1")->
        select("id_asset")->
        where("id_barang_masuk", func_get_arg(2))->
        get()->getResult();
        $data2 = $this->db->table("tasset")->
        select("id_asset")->
        where(["dari_input" => 0, "id_input" => func_get_arg(2), "sta !=" => 0])->
        get()->getResult();

        return count($data1) === count($data2);
      case 6: // tampil/barang_masuk/delete
        $id_barang_masuk = \func_get_arg(1);
        return $this->db->table("tbarang_masuk")->
        select("tipe")->
        where(["id_barang_masuk" => $id_barang_masuk, "sta !=" => 0])->
        get()->getResult();
    }
  }

  function updates() {
    switch (\func_get_arg(0)) {
      case 1: // daftar/barang_masuk/delete
        $this->db->table("tbarang_masuk")->where("id_barang_masuk", \func_get_arg(1))->update(["sta" => 0, "alasan" => \func_get_arg(2)]);
        if ($this->db->affectedRows() === 0) return false;
        
        $this->db->query("
          UPDATE tasset SET
          sta = 0,
          alasan = '" .$this->db->escapeString(\func_get_arg(2)). "'
          WHERE id_asset IN (SELECT id_asset FROM tbarang_masuk1 WHERE id_barang_masuk = '" .$this->db->escapeString(\func_get_arg(1)). "') 
        ");
        if ($this->db->affectedRows() === 0) return false;
        return true;
    }
  }

}
