<?php
namespace app\Models;

use CodeIgniter\Model;

class M_Asset extends Model {
  protected $db;

  function __construct() {
    $this->db = db_connect();
  }
  
  // master/asset/save
  function put($id_asset, $data_asset) {
    if (!is_empty($id_asset)) $this->db->query("SET @id_asset = $id_asset");
    $this->db->query("CALL save_asset(@id_asset, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", $data_asset);
    return $this->db->query("SELECT @id_asset AS id_asset")->getResult()[0]->id_asset;
  }

  function read() {
    switch (\func_get_arg(0)) {
      case 1: // libraries/c_asset/is_valid_asset
        $type = \func_get_arg(1);
        switch ($type) {
          // case "barang_masuk1":
          //   return $this->db->table("tasset")->
          //   select("id_asset")->
          //   where(["id_input" => func_get_arg(2), "dari_input" => 0])->
          //   get()->getResult();
          case "id_barang":
            return $this->db->table("tbarang")->
            select("id_barang")->
            where(["sta" => 1, "id_barang" => \func_get_arg(2)])->
            get()->getResult();
          case "qr_code":
            return $this->db->table("tasset")->
            select("id_asset")->
            where(["qr_code" => \func_get_arg(2), "qr_code !=" => null, "id_asset !=" => \func_get_arg(3), "sta !=" => 0])->
            get()->getResult();
          case "serial_number":
            return $this->db->table("tasset")->
            select("id_asset")->
            where(["serial_number" => \func_get_arg(2), "id_asset !=" => \func_get_arg(3), "sta !=" => 0])->
            get()->getResult();
          case "no_surat_kontrak":
            return $this->db->table("tasset")->
            select("id_asset")->
            where(["no_surat_kontrak" => \func_get_arg(2), "no_surat_kontrak !=" => null, "id_asset !=" => \func_get_arg(3), "sta !=" => 0])->
            get()->getResult();
          case "id_kepemilikan":
            return $this->db->table("tkepemilikan")->
            select("id")->
            where(["id" => \func_get_arg(2), "sta" => 1])->
            get()->getResult();
          case "id_gudang":
            return $this->db->table("tgudang")->
            select("id_gudang")->
            where(["id_gudang" => func_get_arg(2), "sta" => 1])->
            get()->getResult();
          case "id_pelanggan":
            return $this->db->table("tpelanggan")->
            select("id_pelanggan")->
            where(["id_pelanggan" => func_get_arg(2), "sta" => 1])->
            get()->getResult();
        }
      break;
      case 2: // master/asset/fetch&type=ajax
        $type = \func_get_arg(1);
        $filter = \func_get_arg(2);
        switch ($type) {
          case "asset":
            return $this->db->table("tasset")->
            select("id_asset, qr_code, serial_number, no_surat_kontrak, tanggal_akuisisi_asset, tanggal_berakhir_kontrak, id_kepemilikan, keterangan, sta, alasan")->
            where("sta !=", 0)->
            groupStart()->
            orLike(["qr_code" => $filter, "serial_number" => $filter])->
            groupEnd()->
            limit(\get_autocomplete_limit())->
            get()->getResult();
          case "barang":
            return $this->db->table("tbarang tb")->
            select("tb.id_barang, CONCAT(tb1.nama_brand, ' ', tb2.nama_tipe) AS barang_detail")->
            join("tbarang1 tb1", "tb.id_brand = tb1.id_brand")->
            join("tbarang2 tb2", "tb.id_tipe = tb2.id_tipe")->
            where("tb.sta", 1)->
            groupStart()->
            orLike(["CONCAT(tb1.nama_brand, ' ', tb2.nama_tipe)" => $filter])->
            groupEnd()->
            limit(\get_autocomplete_limit())->
            get()->getResult();
          case "gudang":
            return $this->db->table("tgudang")->
            select("id_gudang, kode_gudang, nama_gudang")->
            where("sta", 1)->
            groupStart()->
            orLike(["kode_gudang" => $filter, "nama_gudang" => $filter, "CONCAT(kode_gudang, ' / ', nama_gudang)" => $filter])->
            groupEnd()->
            limit(\get_autocomplete_limit())->
            get()->getResult();
          case "pelanggan":
            return $this->db->table("tpelanggan")->
            select("id_pelanggan, kode_pelanggan, nama_pelanggan")->
            where(["id_level !=" => 3, "sta" => 1])->
            groupStart()->
            orLike(["kode_pelanggan" => $filter, "nama_pelanggan" => $filter, "CONCAT(kode_pelanggan, ' / ', nama_pelanggan)" => $filter])->
            groupEnd()->
            limit(\get_autocomplete_limit())->
            get()->getResult();
          case "kepemilikan":
            return $this->db->table("tkepemilikan")->
            select("id, nama_kepemilikan")->
            where("sta", 1)->
            get()->getResult(); 
        }
      break;
      case 3: // master/asset/fetch&type=edit
        return $this->db->table("tasset")->
        select("id_asset, qr_code AS asset, qr_code, tanggal_akuisisi_asset, serial_number, no_surat_kontrak, tanggal_berakhir_kontrak, id_kepemilikan, keterangan, sta, alasan")->
        where(["id_asset" => \func_get_arg(1), "sta !=" => 0])->
        get()->getResult();
      case 4: // daftar/asset/fetch
        $type = func_get_arg(1);
        switch ($type) {
          case "period":
            return $this->db->query("
              SELECT YEAR(ta.tanggal_berakhir_kontrak) AS tahun, tb.kode AS bulan, tb.nama AS nama_bulan
              FROM tasset ta
              WHERE ta.sta != 0`
              JOIN tbulan tb ON MONTH(ta.tanggal_berakhir_kontrak) = tb.kode
              GROUP BY YEAR(ta.tanggal_berakhir_kontrak), MONTH(ta.tanggal_berakhir_kontrak)
              ORDER BY ta.tanggal_berakhir_kontrak DESC
            ")->getResult();
          case "asset":
            $date1 = $this->db->escapeLikeString(\func_get_arg(2));
            $date2 = $this->db->escapeLikeString(func_get_arg(3));
            $filter_date = is_empty(func_get_arg(2)) || is_empty(func_get_arg(3)) ? "" : " AND ta.tanggal_berakhir_kontrak BETWEEN '$date1' AND '$date2'";
            $sta = $this->db->escapeLikeString(\func_get_arg(4));
            switch ($sta) {
              case "1":
                $sta = "AND ta.sta = 1"; break;
              case "2":
                $sta = "AND ta.sta = 2"; break;
              default:
                $sta = "";
            }
            $filter = $this->db->escapeLikeString(\func_get_arg(5));
            $id_user = $this->db->escapeString(\func_get_arg(6));
            $v1 = $this->db->escapeString(\func_get_arg(7));
            if (!$v1) $v1 = " AND tu.id_user = '$id_user'";
            else $v1 = "";
            $limit = "";
            if (!is_empty(\func_get_arg(8))) {
                $page = $this->db->escapeString(\func_get_arg(8));
                $display_per_page = $this->db->escapeString(\func_get_arg(9));
                $limit = "LIMIT " .($page * $display_per_page). ", $display_per_page";
            }
            return $this->db->query("
              SELECT ta.id_asset, CONCAT(tb1.nama_brand, ' ', tb2.nama_tipe) AS nama_barang, ta.qr_code, ta.serial_number, ta.tanggal_akuisisi_asset, ta.no_surat_kontrak, ta.tanggal_berakhir_kontrak, tk.nama_kepemilikan, ta.keterangan, tg.nama_gudang, tp.nama_pelanggan, ta.sta
              FROM tasset ta
              JOIN tbarang tb ON ta.id_barang = tb.id_barang
              JOIN tbarang1 tb1 ON tb.id_brand = tb1.id_brand
              JOIN tbarang2 tb2 ON tb.id_tipe = tb2.id_tipe
              JOIN tkepemilikan tk ON ta.id_kepemilikan = tk.id
              LEFT JOIN tgudang tg ON ta.id_gudang = tg.id_gudang
              LEFT JOIN tpelanggan tp ON ta.id_pelanggan = tp.id_pelanggan
              LEFT JOIN tuser tu ON tg.id_kepala_gudang = tu.id_user
              WHERE ta.sta != 0 $sta $v1 AND
              (
                ta.qr_code LIKE '%$filter%' OR
                ta.serial_number LIKE '%$filter%' OR
                ta.no_surat_kontrak LIKE '%$filter%' OR
                tk.nama_kepemilikan LIKE '%$filter%' OR
                ta.keterangan LIKE '%$filter%' OR
                ta.alasan LIKE '%$filter%' OR
                tb.kode_barang LIKE '%$filter%' OR
                tb.nama_barang LIKE '%$filter%' OR
                tg.kode_gudang LIKE '%$filter%' OR
                tg.nama_gudang LIKE '%$filter%' OR
                tp.kode_pelanggan LIKE '%$filter%' OR
                tp.nama_pelanggan LIKE '%$filter%'
              ) $filter_date
              ORDER BY ta.id_asset
              $limit
            ")->getResult();
        }
      break;
      case 5: // daftar/asset/delete
        $type = \func_get_arg(1);
        $id_asset = \func_get_arg(2);
        switch ($type) {
          case "tbarang_keluar":
            return $this->db->table("tbarang_keluar tbk")->
            select("tbk.id_barang_keluar")->
            join("tbarang_keluar1 tbk1", "tbk.id_barang_keluar = tbk1.id_barang_keluar")->
            where(["tbk.sta" => 1, "tbk1.id_asset" => $id_asset])->
            get()->getResult();
          case "tbarang_masuk":
            return $this->db->table("tbarang_masuk tbm")->
            select("tbm.id_barang_masuk")->
            join("tbarang_masuk1 tbm1", "tbm.id_barang_masuk = tbm1.id_barang_masuk")->
            where(["tbm.sta" => 1, "tbm1.id_asset" => $id_asset])->
            get()->getResult();
          case "tmutasi":
            return $this->db->table("tmutasi tm")->
            select("tm.id_mutasi")->
            join("tmutasi1 tm1", "tm.id_mutasi = tm1.id_mutasi")->
            where(["tm.sta" => 1, "tm1.id_asset" => $id_asset])->
            get()->getResult();
          case "id_pelanggan":
            return $this->db->table("tasset")->
            select("id_asset")->
            where(["id_asset" => $id_asset, "id_pelanggan !=", null])->
            get()->getResult();
        }
      break;
      case 6: // laporan/asset/fetch
        switch (\func_get_arg(1)) {
          case "period":
            return $this->db->query("
              SELECT t1.tahun, t1.bulan, t1.nama_bulan
              FROM (
                SELECT 1 AS tipe, YEAR(tbm.tanggal_buat) AS tahun, tb.kode AS bulan, tb.nama AS nama_bulan
                FROM tbarang_masuk tbm
                JOIN tbulan tb ON MONTH(tbm.tanggal_buat) = tb.kode
                WHERE tbm.sta != 0
                GROUP BY YEAR(tbm.tanggal_buat), MONTH(tbm.tanggal_buat)
                
                UNION ALL 
                SELECT 2 AS tipe, YEAR(tbk.tanggal_buat) AS tahun, tb.kode AS bulan, tb.nama AS nama_bulan
                FROM tbarang_keluar tbk
                JOIN tbulan tb ON MONTH(tbk.tanggal_buat) = tb.kode
                WHERE tbk.sta != 0 
                GROUP BY YEAR(tbk.tanggal_buat), MONTH(tbk.tanggal_buat)
                
                UNION ALL 
                SELECT 3 AS tipe, YEAR(tm.tanggal_buat) AS tahun, tb.kode AS bulan, tb.nama AS nama_bulan
                FROM tmutasi tm
                JOIN tbulan tb ON MONTH(tm.tanggal_buat) = tb.kode
                WHERE tm.sta != 0
                GROUP BY YEAR(tm.tanggal_buat), MONTH(tm.tanggal_buat)
              ) t1
              GROUP BY t1.tahun, t1.bulan
              ORDER BY t1.tahun DESC, t1.bulan DESC
            ")->getResult();
          case "laporan":
            $filter_by = func_get_arg(2);
            $id = $this->db->escapeString(\func_get_arg(3));
            $date1 = $this->db->escapeString(\func_get_arg(4));
            $date2 = $this->db->escapeString(\func_get_arg(5));
            $kondisi = $this->db->escapeString(\func_get_arg(6));
            $kondisi = !is_empty($kondisi) ? "ta.sta = '$kondisi' AND " : "";
            $filter = $this->db->escapeLikeString(\func_get_arg(7));
            $id_user = $this->db->escapeString(\func_get_arg(8));
            $r1 = \func_get_arg(9);
            $r1 = !$r1 ? "tu.id_user = '$id_user' AND " : "";

            if ($filter_by === "id") {
              $filter = [
                "$r1 ta.id_asset = '$id'",
                "$r1 ta.id_asset = '$id'",
                "$r1 ta.id_asset = '$id'",
              ];
            } else {
              $filter = [
                "
                  $kondisi $r1
                  DATE(tbm.tanggal_buat) BETWEEN '$date1' AND '$date2' AND 
                  (
                    ta.qr_code LIKE '%$filter%' OR 
                    ta.serial_number LIKE '%$filter%' OR 
                    ta.no_surat_kontrak LIKE '%$filter%' OR 
                    ta.keterangan LIKE '%$filter%' OR 
                    tb.kode_barang LIKE '%$filter%' OR 
                    tb.nama_barang LIKE '%$filter%' OR 
                    tb.ukuran LIKE '%$filter%' OR 
                    tb1.nama_brand LIKE '%$filter%' OR 
                    tb2.nama_tipe LIKE '%$filter%' OR
                    tbm.no_masuk LIKE '%$filter%' OR 
                    tbm.no_faktur LIKE '%$filter%' OR 
                    tbm.keterangan LIKE '%$filter%' OR 
                    td.kode_distributor LIKE '%$filter%' OR
                    td.nama_distributor LIKE '%$filter%' OR
                    tpd.kode_pelanggan LIKE '%$filter%' OR 
                    tpd.nama_pelanggan LIKE '%$filter%' OR 
                    tu.kode_user LIKE '%$filter%' OR 
                    tu.nama_user LIKE '%$filter%' OR 
                    tg.kode_gudang LIKE '%$filter%' OR 
                    tg.nama_gudang LIKE '%$filter%' OR 
                    tpa.kode_pelanggan LIKE '%$filter%' OR 
                    tpa.nama_pelanggan LIKE '%$filter%'
                  )
                ",
                "
                  $kondisi $r1
                  DATE(tbk.tanggal_buat) BETWEEN '$date1' AND '$date2' AND 
                  (
                    ta.qr_code LIKE '%$filter%' OR 
                    ta.serial_number LIKE '%$filter%' OR 
                    ta.no_surat_kontrak LIKE '%$filter%' OR 
                    ta.keterangan LIKE '%$filter%' OR 
                    tb.kode_barang LIKE '%$filter%' OR 
                    tb.nama_barang LIKE '%$filter%' OR 
                    tb.ukuran LIKE '%$filter%' OR 
                    tb1.nama_brand LIKE '%$filter%' OR 
                    tb2.nama_tipe LIKE '%$filter%' OR 
                    tbk.no_keluar LIKE '%$filter%' OR 
                    tbk.keterangan LIKE '%$filter%' OR 
                    tu.kode_user LIKE '%$filter%' OR 
                    tu.nama_user LIKE '%$filter%' OR 
                    tg.kode_gudang LIKE '%$filter%' OR 
                    tg.nama_gudang LIKE '%$filter%'
                  )
                ",
                "
                  $kondisi $r1
                  DATE(tm.tanggal_buat) BETWEEN '$date1' AND '$date2' AND 
                  (
                    ta.qr_code LIKE '%$filter%' OR 
                    ta.serial_number LIKE '%$filter%' OR 
                    ta.no_surat_kontrak LIKE '%$filter%' OR 
                    ta.keterangan LIKE '%$filter%' OR 
                    tb.kode_barang LIKE '%$filter%' OR 
                    tb.nama_barang LIKE '%$filter%' OR 
                    tb.ukuran LIKE '%$filter%' OR 
                    tb1.nama_brand LIKE '%$filter%' OR 
                    tb2.nama_tipe LIKE '%$filter%' OR 
                    tm.no_mutasi LIKE '%$filter%' OR 
                    tm.keterangan LIKE '%$filter%' OR 
                    tu.kode_user LIKE '%$filter%' OR 
                    tu.nama_user LIKE '%$filter%' OR 
                    tpd.kode_pelanggan LIKE '%$filter%' OR 
                    tpd.nama_pelanggan LIKE '%$filter%' OR 
                    tpk.kode_pelanggan LIKE '%$filter%' OR 
                    tpk.nama_pelanggan LIKE '%$filter%'
                  )
                ",
              ];
            }
            
            return $this->db->query("
              SELECT 
                t1.qr_code,
                IFNULL(t1.serial_number, '-') AS serial_number,
                IFNULL(t1.no_surat_kontrak, '-') AS no_surat_kontrak,
                t1.nama_brand,
                IFNULL(t1.nama_tipe, '-') AS nama_tipe,
                IFNULL(t1.keterangan_asset, '-') AS keterangan,
                GROUP_CONCAT(CONCAT_WS(';',
                    t1.tanggal, t1.no_dokumen, t1.usr, t1.dari, t1.ke, IFNULL(t1.keterangan, '-'), t1.tipe
                  )
                  ORDER BY t1.tanggal, t1.tipe, SUBSTRING_INDEX(t1.no_dokumen, '-', -1)
                  SEPARATOR '#'
                ) AS detail
              FROM (
                SELECT 
                  1 AS tipe,
                  ta.qr_code, 
                  ta.serial_number,
                  ta.no_surat_kontrak,
                  tb1.nama_brand, 
                  tb2.nama_tipe,
                  ta.keterangan AS keterangan_asset,
                  DATE(tbm.tanggal_buat) AS tanggal, 
                  tbm.no_masuk AS no_dokumen, 
                  tu.nama_user AS usr,
                  IF(tbm.tipe != 1, td.nama_distributor, tpd.nama_pelanggan) AS dari,
                  IF (tbm.tipe != 2, tg.nama_gudang, tpa.nama_pelanggan) AS ke,
                  tbm.keterangan
                    
                FROM tasset ta
                JOIN tbarang tb ON ta.id_barang = tb.id_barang
                LEFT JOIN tbarang1 tb1 ON tb.id_brand = tb1.id_brand
                LEFT JOIN tbarang2 tb2 ON tb.id_tipe = tb2.id_tipe
                JOIN tbarang_masuk1 tbm1 ON ta.id_asset = tbm1.id_asset
                LEFT JOIN tpelanggan tpd ON tbm1.dari_id_pelanggan = tpd.id_pelanggan
                JOIN tbarang_masuk tbm ON tbm1.id_barang_masuk = tbm.id_barang_masuk
                LEFT JOIN tpesanan tpsn ON tbm.dari_id_pesanan = tpsn.id_pesanan
                LEFT JOIN tdistributor td ON tpsn.id_distributor = td.id_distributor
                JOIN tuser tu ON tbm.id_penerima = tu.id_user
                LEFT JOIN tgudang tg ON tbm.ke_id_gudang = tg.id_gudang
                LEFT JOIN tpelanggan tpa ON tbm.ke_id_agen = tpa.id_pelanggan
                  
                WHERE 
                  ta.sta != 0 AND
                  tbm.sta != 0 AND 
                  $filter[0]
                    
                UNION ALL
                SELECT 
                  2 AS tipe,
                  ta.qr_code, 
                  ta.serial_number,
                  ta.no_surat_kontrak,
                  tb1.nama_brand, 
                  tb2.nama_tipe,
                  ta.keterangan AS keterangan_asset,
                  DATE(tbk.tanggal_buat) AS tanggal,
                  tbk.no_keluar AS no_dokumen,
                  tu.nama_user AS usr,
                  tg.nama_gudang AS dari,
                  '-' AS ke,
                  tbk.keterangan
                    
                FROM tasset ta
                JOIN tbarang tb ON ta.id_barang = tb.id_barang
                JOIN tbarang1 tb1 ON tb.id_brand = tb1.id_brand
                JOIN tbarang2 tb2 ON tb.id_tipe = tb2.id_tipe
                JOIN tbarang_keluar1 tbk1 ON ta.id_asset = tbk1.id_asset
                JOIN tbarang_keluar tbk ON tbk1.id_barang_keluar = tbk.id_barang_keluar
                JOIN tuser tu ON tbk.id_pengurus = tu.id_user
                JOIN tgudang tg ON tbk.id_gudang = tg.id_gudang
                    
                WHERE 
                  ta.sta != 0 AND 
                  tbk.sta != 0 AND 
                  $filter[1]
                    
                UNION ALL
                SELECT 
                  3 AS tipe,
                  ta.qr_code, 
                  ta.serial_number,
                  ta.no_surat_kontrak,
                  tb1.nama_brand, 
                  tb2.nama_tipe,
                  ta.keterangan AS keterangan_asset,
                  DATE(tm.tanggal_buat) AS tanggal,
                  tm.no_mutasi AS no_dokumen,
                  tu.nama_user AS usr,
                  tpd.nama_pelanggan AS dari,
                  tpk.nama_pelanggan AS ke,
                  tm.keterangan
                    
                FROM tasset ta 
                JOIN tbarang tb ON ta.id_barang = tb.id_barang
                JOIN tbarang1 tb1 ON tb.id_brand = tb1.id_brand
                JOIN tbarang2 tb2 ON tb.id_tipe = tb2.id_tipe
                JOIN tmutasi1 tm1 ON ta.id_asset = tm1.id_asset
                JOIN tmutasi tm ON tm1.id_mutasi = tm.id_mutasi
                JOIN tuser tu ON tm.id_user = tu.id_user
                JOIN tpelanggan tpd ON tm.dari_id_pelanggan = tpd.id_pelanggan
                JOIN tpelanggan tpk ON tm1.ke_id_pelanggan = tpk.id_pelanggan
                    
                WHERE 
                  ta.sta != 0 AND 
                  tm.sta != 0 AND 
                  $filter[2]
              ) t1
              GROUP BY t1.qr_code
            ")->getResult();
        }
      case "daftar/asset?export_excel":
        $id_user = $this->db->escapeString(\func_get_arg(1));
        $v1 = $this->db->escapeString(func_get_arg(2));
        if (!$v1) $v1 = " AND tu.id_user = '$id_user'";
        else $v1 = "";
        return $this->db->query("
          SELECT 
            ta.tanggal_akuisisi_asset, ta.qr_code, ta.serial_number, ta.keterangan, ta.no_surat_kontrak, 
            tb2.nama_tipe, tb1.nama_brand, tke.nama_kepemilikan, tg.nama_gudang, t1.nama_agen, 
            t1.nama_pelanggan, t1.alamat, t1.longitude, t1.latitude
          
          FROM tasset ta
          JOIN tbarang tb ON ta.id_barang = tb.id_barang
          JOIN tbarang1 tb1 ON tb.id_brand = tb1.id_brand
          JOIN tbarang2 tb2 ON tb.id_tipe = tb2.id_tipe
          JOIN tkepemilikan tke ON ta.id_kepemilikan = tke.id
          LEFT JOIN tgudang tg ON ta.id_gudang = tg.id_gudang
          LEFT JOIN tuser tu ON tg.id_kepala_gudang = tu.id_user
          LEFT JOIN (
            SELECT tpe.id_pelanggan, tag.nama_pelanggan AS nama_agen, tpe.nama_pelanggan, tpe.alamat, tpe.longitude, tpe.latitude
            FROM tpelanggan tpe
            LEFT JOIN tpelanggan tag ON tpe.id_agen = tag.id_pelanggan
            WHERE tpe.id_pelanggan IN (SELECT id_pelanggan FROM tasset WHERE sta != 0)
          ) t1 ON ta.id_pelanggan = t1.id_pelanggan

          WHERE ta.sta != 0 $v1
        ")->getResult(); 
      case "laporan/penggantian_freezer?fetch":
        switch (\func_get_arg(1)) {
          case "period":
            return $this->db->query("
              SELECT t1.tahun, t1.bulan, t1.nama_bulan
              FROM (
                SELECT 1 AS tipe, YEAR(tlas.tgl_buat) AS tahun, tb.kode AS bulan, tb.nama AS nama_bulan
                FROM tlokasi_awal_asset tlas
                JOIN tasset ta ON tlas.id_asset = ta.id_asset
                JOIN tbulan tb ON MONTH(tlas.tgl_buat) = tb.kode
                WHERE ta.sta != 0
                GROUP BY YEAR(tlas.tgl_buat), MONTH(tlas.tgl_buat)
                
                UNION ALL 
                SELECT 2 AS tipe, YEAR(tm.tanggal_buat) AS tahun, tb.kode AS bulan, tb.nama AS nama_bulan
                FROM tmutasi tm
                JOIN tbulan tb ON MONTH(tm.tanggal_buat) = tb.kode
                WHERE tm.sta != 0
                GROUP BY YEAR(tm.tanggal_buat), MONTH(tm.tanggal_buat)
              ) t1
              GROUP BY t1.tahun, t1.bulan
              ORDER BY t1.tahun DESC, t1.bulan DESC
            ")->getResult();
          case "laporan":
            $date1 = $this->db->escapeString(\func_get_arg(2));
            $date2 = $this->db->escapeString(\func_get_arg(3));
            $filter = $this->db->escapeLikeString(\func_get_arg(4));
            return $this->db->query("
              SELECT 
                t1.nama_pelanggan, t1.alamat, t1.nama_kelurahan, t1.nama_kecamatan, t1.nama_kabupaten, 
                t1.nama_propinsi, t1.kode_pos, GROUP_CONCAT(t1.asset SEPARATOR '#') AS list_asset
              FROM (
                SELECT 
                  0 AS tipe, tlas.id_pelanggan, tp.nama_pelanggan, tp.alamat, tkel.nama_kelurahan, 
                  tkec.nama_kecamatan, tka.nama_kabupaten, tpr.nama_propinsi, tp.kode_pos, 
                  tlas.id_asset, CONCAT_WS(';', ta.qr_code, tb1.nama_brand, tb2.nama_tipe, DATE(tlas.tgl_buat)) AS asset
                  
                FROM tlokasi_awal_asset tlas
                JOIN tasset ta ON tlas.id_asset = ta.id_asset
                JOIN tbarang tb ON ta.id_barang = tb.id_barang
                JOIN tbarang1 tb1 ON tb.id_brand = tb1.id_brand
                JOIN tbarang2 tb2 ON tb.id_tipe = tb2.id_tipe
                LEFT JOIN tpelanggan tp ON tlas.id_pelanggan = tp.id_pelanggan
                LEFT JOIN tpropinsi tpr ON tp.id_propinsi = tpr.id_propinsi
                LEFT JOIN tkabupaten tka ON tp.id_kabupaten = tka.id_kabupaten
                LEFT JOIN tkecamatan tkec ON tp.id_kecamatan = tkec.id_kecamatan
                LEFT JOIN tkelurahan tkel ON tp.id_kelurahan = tkel.id_kelurahan
                
                WHERE tlas.id_gudang IS NULL AND ta.sta != 0 AND (DATE(tlas.tgl_buat) BETWEEN '$date1' AND '$date2') AND (
                  ta.qr_code LIKE '%$filter%' OR 
                  ta.serial_number LIKE '%$filter%' OR 
                  ta.no_surat_kontrak LIKE '%$filter%' OR 
                  ta.keterangan LIKE '%$filter%' OR 
                  ta.sta = (CASE WHEN '%$filter%' IN ('bagus', 'siap pakai') THEN 2 WHEN '%$filter%' = 'rusak' THEN 1 END) OR 
                  tb.kode_barang LIKE '%$filter%' OR 
                  tb1.nama_brand LIKE '%$filter%' OR 
                  tb2.nama_tipe LIKE '%$filter%' OR 
                  tp.kode_pelanggan LIKE '%$filter%' OR 
                  tp.nama_pelanggan LIKE '%$filter%' OR 
                  tpr.nama_propinsi LIKE '%$filter%' OR 
                  tka.nama_kabupaten LIKE '%$filter%'
                )
                
                UNION ALL
                SELECT 
                  1 AS tipe, tp1.id_pelanggan, tp1.nama_pelanggan, tp1.alamat, tkel1.nama_kelurahan, 
                  tkec1.nama_kecamatan, tka1.nama_kabupaten, tpr1.nama_propinsi, tp1.kode_pos, 
                  ta1.id_asset, CONCAT_WS(';', ta1.qr_code, tb11.nama_brand, tb21.nama_tipe, DATE(tm.tanggal_buat)) AS asset
                
                FROM tmutasi tm
                JOIN tmutasi1 tm1 ON tm.id_mutasi = tm1.id_mutasi
                JOIN tuser tu1 ON tm.id_user = tu1.id_user
                JOIN tasset ta1 ON tm1.id_asset = ta1.id_asset
                JOIN tbarang tb01 ON ta1.id_barang = tb01.id_barang
                JOIN tbarang1 tb11 ON tb01.id_brand = tb11.id_brand
                JOIN tbarang2 tb21 ON tb01.id_tipe = tb21.id_tipe
                JOIN tpelanggan tp1 ON tm1.ke_id_pelanggan = tp1.id_pelanggan
                LEFT JOIN tpropinsi tpr1 ON tp1.id_propinsi = tpr1.id_propinsi
                LEFT JOIN tkabupaten tka1 ON tp1.id_kabupaten = tka1.id_kabupaten
                LEFT JOIN tkecamatan tkec1 ON tp1.id_kecamatan = tkec1.id_kecamatan
                LEFT JOIN tkelurahan tkel1 ON tp1.id_kelurahan = tkel1.id_kelurahan
                
                WHERE tm.sta != 0 AND ta1.sta != 0 AND (DATE(tm.tanggal_buat) BETWEEN '$date1' AND '$date2') AND (
                  tm.no_mutasi LIKE '%$filter%' OR 
                  tm.keterangan LIKE '%$filter%' OR 
                  tu1.kode_user LIKE '%$filter%' OR 
                  tu1.nama_user LIKE '%$filter%' OR 
                  ta1.qr_code LIKE '%$filter%' OR 
                  ta1.serial_number LIKE '%$filter%' OR 
                  ta1.no_surat_kontrak LIKE '%$filter%' OR 
                  ta1.keterangan LIKE '%$filter%' OR 
                  ta1.sta = (CASE WHEN '%$filter%' IN ('bagus', 'siap pakai') THEN 2 WHEN '%$filter%' = 'rusak' THEN 1 END) OR 
                  tb01.kode_barang LIKE '%$filter%' OR 
                  tb11.nama_brand LIKE '%$filter%' OR 
                  tb21.nama_tipe LIKE '%$filter%' OR 
                  tp1.kode_pelanggan LIKE '%$filter%' OR 
                  tp1.nama_pelanggan LIKE '%$filter%' OR 
                  tpr1.nama_propinsi LIKE '%$filter%' OR 
                  tka1.nama_kabupaten LIKE '%$filter%'
                )
              ) t1
              GROUP BY t1.id_pelanggan
              ORDER BY t1.nama_pelanggan
            ")->getResult();
        }
    }
  }

  function updates() {
    switch (\func_get_arg(0)) {
      case 1: // daftar/asset/delete
        $this->db->table("tasset")->where("id_asset", \func_get_arg(1))->update(["sta" => 0, "alasan" => \func_get_arg(2)]);
        return $this->db->affectedRows();
      case 2: // libraries/c_barang_masuk/is_valid_barang_masuk1
        $this->db->table("tasset")->where("id_asset", \func_get_arg(1))->update(\func_get_arg(2));
        return $this->db->affectedRows() > 0;
    }
  }

}
