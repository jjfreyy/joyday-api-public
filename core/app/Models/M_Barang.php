<?php
namespace app\Models;

use CodeIgniter\Model;

class M_Barang extends Model {
    protected $db;

    function __construct() {
        $this->db = db_connect();
    }

    function put($data_barang) {
        return $this->query("CALL save_barang(?, ?, ?, ?, ?, ?, ?, ?, ?)", $data_barang);
    }

    function read() {
        switch (\func_get_arg(0)) {
            case 1: // master/barang/save
                $type = \func_get_arg(1);
                $value = \func_get_arg(2);
                $exclude = \func_get_arg(3);
                switch ($type) {
                    case "kode_barang":
                        return $this->db->table("tbarang")->select("id_barang")->where(["kode_barang" => $value, "id_barang !=" => $exclude])->get()->getResult();
                    case "brand":
                        return $this->db->table("tbarang1")->select("id_brand")->where(["nama_brand" => $value, "id_brand !=" => $exclude])->get()->getResult();
                    case "tipe":
                        return $this->db->table("tbarang2")->select("id_tipe")->where(["nama_tipe" => $value, "id_tipe !=" => $exclude])->get()->getResult();
                }
            break;
            case 2: // master/barang/fetch?type=ajax
                $type = \func_get_arg(1);
                $filter = \func_get_arg(2);
                switch($type) {
                    case "barang":
                        return $this->db->table("tbarang tb")->
                        select("tb.id_barang, tb.kode_barang, tb.id_brand, tb1.nama_brand, tb.id_tipe, tb2.nama_tipe, tb.ukuran, tb.keterangan")->
                        join("tbarang1 tb1", "tb.id_brand = tb1.id_brand", "left")->
                        join("tbarang2 tb2", "tb.id_tipe = tb2.id_tipe")->
                        groupStart()->
                        orLike(["tb.kode_barang" => $filter, "tb.nama_barang" => $filter])->
                        groupEnd()->
                        where("tb.sta", 1)->
                        limit(\get_autocomplete_limit())->
                        get()->getResult();
                    case "brand":
                        return $this->db->table("tbarang1")->
                        select("id_brand, nama_brand")->
                        like("nama_brand", $filter)->
                        where("sta", 1)->
                        limit(\get_autocomplete_limit())->
                        get()->getResult();
                    case "tipe":
                        return $this->db->table("tbarang2")->
                        select("id_tipe, nama_tipe")->
                        like("nama_tipe", $filter)->
                        where("sta", 1)->
                        limit(\get_autocomplete_limit())->
                        get()->getResult();
                }
            break;
            case 3: // master/barang/fetch?type=edit
                return $this->db->table("tbarang tb")->
                select("tb.id_barang, tb.kode_barang, tb.nama_barang, tb.id_brand, tb1.nama_brand, tb.id_tipe, tb2.nama_tipe, tb.ukuran, tb.keterangan")->
                join("tbarang1 tb1", "tb.id_brand = tb1.id_brand", "left")->
                join("tbarang2 tb2", "tb.id_tipe = tb2.id_tipe")->
                where(["tb.id_barang" => \func_get_arg(1), "tb.sta" => 1])->
                get()->getResult();
            case 4: // daftar/barang/fetch
                $filter = $this->db->escapeLikeString(\func_get_arg(1));
                $limit = "";
                if (!is_empty(\func_get_arg(2))) {
                    $page = $this->db->escapeString(\func_get_arg(2));
                    $display_per_page = $this->db->escapeString(\func_get_arg(3));
                    $limit = "LIMIT " .($page * $display_per_page). ", $display_per_page";
                }
                return $this->db->query("
                    SELECT tb.id_barang, tb.kode_barang, tb.nama_barang, tb1.nama_brand, tb2.nama_tipe, tb.ukuran, tb.keterangan
                    FROM tbarang tb
                    LEFT JOIN tbarang1 tb1 ON tb.id_brand = tb1.id_brand
                    JOIN tbarang2 tb2 ON tb.id_tipe = tb2.id_tipe
                    WHERE tb.sta = 1 AND
                    (
                        tb.kode_barang LIKE '%$filter%' OR 
                        tb.nama_barang LIKE '%$filter%' OR 
                        tb1.nama_brand LIKE '%$filter%' OR 
                        tb2.nama_tipe LIKE '%$filter%' OR
                        tb.ukuran = '$filter' OR
                        tb.keterangan LIKE '%$filter%'
                    )
                    ORDER BY tb.id_barang
                    $limit
                ")->getResult();
            case 5: // daftar/barang/delete
                $type = func_get_arg(1);
                $id_barang = func_get_arg(2);
                switch ($type) {
                    case "tasset":
                        return $this->db->table("tasset")->select("id_barang")->where(["id_barang" => $id_barang, "sta" => 1])->get()->getResult();
                    case "tpesanan":
                        return $this->db->table("tpesanan tp")->
                        select("tp.id_pesanan")->
                        join("tpesanan1 tp1", "tp.id_pesanan = tp1.id_pesanan")->
                        where(["tp.sta" => 1, "tp1.id_barang" => $id_barang])->
                        get()->getResult();
                }
            break;
        }
    }

    function updates() {
        switch (\func_get_arg(0)) {
            case 1: // daftar/barang/delete
                $this->db->table("tbarang")->where("id_barang", \func_get_arg(1))->update(["sta" => 0, "alasan" => \func_get_arg(2)]);
                return $this->db->affectedRows();
        }
    }
}
