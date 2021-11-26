<?php
namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Libraries\C_Barang;
use App\Models\M_Barang;

class Barang extends BaseController {
    private $tbarang;

    function __construct() {
        $this->tbarang = new M_Barang();
    }

    function fetch() {
        try {
            $fetch_barang = get_get("fetch_barang"); 
            if (is_empty($fetch_barang)) throw new \Exception("fetch_barang value not found!");
            $id_user = get_get("id_user");
            switch ($fetch_barang) {
                case "ajax":
                    $type = get_get("type");
                    $filter = get_get("filter");
                    if ($type === "barang" && empty($this->tmenu->read(2, $id_user, "B-E"))) \send_response(403, ["message" => "Anda tidak memiliki akses untuk mengedit data barang."]);
                    \send_response($this->tbarang->read(2, $type,$filter));
                case "edit": 
                    $id_barang = get_get("id_barang");
                    if (empty($this->tmenu->read(2, $id_user, "B-E"))) \send_response(403, ["message" => "Anda tidak memiliki akses untuk mengedit data barang."]);
                    \send_response($this->tbarang->read(3, $id_barang));
                default:
                    throw new \Exception("fetch_barang value not valid!");
            }
        } catch (\Exception $e) {
            \send_500_response(format_exception($e));
        }
    }
    
    function save() {
        try {
            if (is_empty(get_post("save_barang"))) throw new \Exception("save_barang value not found!");
            
            $id_user = get_post("id_user");
            $id_barang = get_post("id_barang");
            if (is_empty($id_barang) && empty($this->tmenu->read(2, $id_user, "B-I"))) \send_response(403, ["global" => "Anda tidak memiliki akses untuk menginput data barang."]);
            if (!is_empty($id_barang) && empty($this->tmenu->read(2, $id_user, "B-E"))) \send_response(403, ["global" => "Anda tidak memiliki akses untuk mengedit data barang."]);

            $kode_barang = get_post("kode_barang");
            $nama_barang = get_post("nama_barang");
            $id_brand = get_post("id_brand");
            $nama_brand = get_post("nama_brand");
            $id_tipe = get_post("id_tipe");
            $nama_tipe = get_post("nama_tipe");
            $ukuran = get_post("ukuran");
            $keterangan = get_post("keterangan");

            $barang = new C_Barang($id_barang, null, $nama_barang, $id_brand, $nama_brand, $id_tipe, $nama_tipe, $ukuran, $keterangan);
            $is_valid_barang = $barang->is_valid_barang();
            if (!$is_valid_barang[0]) send_response(400, $is_valid_barang[1]);
            
            $this->tutils->start();
            $this->tbarang->put($barang->get_barang());
            $this->tutils->commit();
            \send_response();
        } catch (\Exception $e) {
            $this->tutils->rollback(["tbarang", "tbarang1", "tbarang2"]);
            \send_500_response(format_exception($e));
        } 
    }
}
