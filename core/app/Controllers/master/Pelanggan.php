<?php
namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Libraries\C_Pelanggan;
use App\Models\M_Pelanggan;

class Pelanggan extends BaseController {
  private $tpelanggan;

  function __construct() {
    $this->tpelanggan = new M_Pelanggan();
  }

  function fetch() {
    try {
      $fetch_pelanggan = get_get("fetch_pelanggan");
      if (is_empty($fetch_pelanggan)) throw new \Exception("fetch_pelanggan value not found!");
      $id_user = get_get("id_user");
      switch ($fetch_pelanggan) {
        case "ajax":
          $type = get_get("type");
          $filter = get_get("filter");
          if ($type === "pelanggan" && empty($this->tmenu->read(2, $id_user, "PEL-E"))) \send_response(403, ["message" => "Anda tidak memiliki akses untuk mengedit data pelanggan."]);
          \send_response($this->tpelanggan->read(2, $type, $filter));
        case "edit":
          $id_pelanggan = get_get("id_pelanggan");
          if (empty($this->tmenu->read(2, $id_user, "PEL-E"))) \send_response(403, ["message" => "Anda tidak memiliki akses untuk mengedit data pelanggan."]);
          \send_response($this->tpelanggan->read(3, $id_pelanggan));
        default:
        throw new \Exception("fetch_pelanggan value not valid!");
      }
    } catch (\Exception $e) {
      \send_500_response(format_exception($e));
    }
  } 

  function save() {
    try {
      if (is_empty(get_post("save_pelanggan"))) throw new \Exception("save_pelanggan value not found!");

      $id_user = get_post("id_user");
      $id_pelanggan = get_post("id_pelanggan");
      if (is_empty($id_pelanggan) && empty($this->tmenu->read(2, $id_user, "PEL-I"))) \send_response(403, ["global" => "Anda tidak memiliki akses untuk menginput data pelanggan."]);
      if (!is_empty($id_pelanggan) && empty($this->tmenu->read(2, $id_user, "PEL-E"))) \send_response(403, ["global" => "Anda tidak memiliki akses untuk mengedit data pelanggan."]);
      
      $id_agen = get_post("id_agen");
      $kode_pelanggan = get_post("kode_pelanggan");
      $nama_pelanggan = get_post("nama_pelanggan");
      $no_identitas = get_post("no_identitas");
      
      $no_hp1 = get_post("no_hp1");
      $no_hp2 = get_post("no_hp2");
      $email = get_post("email");
      $id_propinsi = get_post("id_propinsi");
      $id_kabupaten = get_post("id_kabupaten");
      
      $id_kecamatan = get_post("id_kecamatan");
      $id_kelurahan = get_post("id_kelurahan");
      $alamat = get_post("alamat");
      $kode_pos = get_post("kode_pos");
      $keterangan = get_post("keterangan");
      
      $daya_listrik = get_post("daya_listrik");
      $latitude = get_post("latitude");
      $longitude = get_post("longitude");
      $nama_kerabat = get_post("nama_kerabat");
      $no_identitas_kerabat = get_post("no_identitas_kerabat");
      
      $no_hp_kerabat = get_post("no_hp_kerabat");
      $alamat_kerabat = get_post("alamat_kerabat");
      $hubungan = get_post("hubungan");
      $id_level = get_post("id_level");

      $pelanggan = new C_Pelanggan(
        $id_pelanggan, $id_agen, null, $nama_pelanggan, $no_identitas, 
        $no_hp1, $no_hp2, $email, $id_propinsi, $id_kabupaten,
        $id_kecamatan, $id_kelurahan, $alamat, $kode_pos, $keterangan,
        $daya_listrik, $latitude, $longitude, $nama_kerabat, $no_identitas_kerabat,
        $no_hp_kerabat, $alamat_kerabat, $hubungan, $id_level
      );
      $is_valid_pelanggan = $pelanggan->is_valid_pelanggan();
      if (!$is_valid_pelanggan[0]) \send_response(400, $is_valid_pelanggan[1]);
      
      $this->tutils->start();
      $this->tpelanggan->put($pelanggan->get_pelanggan());
      $this->tutils->commit();
      \send_response();
    } catch (\Exception $e) {
      $this->tutils->rollback("tpelanggan");
      \send_500_response(format_exception($e));
    }
  }
}
