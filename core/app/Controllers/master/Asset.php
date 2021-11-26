<?php
namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Libraries\C_Asset;
use App\Models\M_Asset;

class Asset extends BaseController {
  private $tasset;

  function __construct() {
    $this->tasset = new M_Asset();
  }

  function fetch() {
    try {
      $fetch_asset = get_get("fetch_asset"); 
      if (is_empty($fetch_asset)) throw new \Exception("fetch_asset value not found!");
      $id_user = get_get("id_user");
      switch ($fetch_asset) {
        case "ajax":
          $type = get_get("type");
          $filter = get_get("filter");
          if ($type === "asset" && empty($this->tmenu->read(2, $id_user, "ASS-E"))) \send_response(403, ["message" => "Anda tidak memiliki akses untuk mengedit data asset."]);
          \send_response($this->tasset->read(2, $type, $filter));
        case "edit": 
            $id_asset = get_get("id_asset");
            if (empty($this->tmenu->read(2, $id_user, "ASS-E"))) \send_response(403, ["message" => "Anda tidak memiliki akses untuk mengedit data asset."]);
            \send_response($this->tasset->read(3, $id_asset));
        default:
            throw new \Exception("fetch_asset value not valid!");
      }
    } catch (\Exception $e) {
        \send_500_response(format_exception($e));
    }
  }
    
    function save() {
        try {
          if (is_empty(get_post("save_asset"))) throw new \Exception("save_asset value not found!");
          $id_user = get_post("id_user");
          $id_asset = get_post("id_asset");
          if (is_empty($id_asset) && empty($this->tmenu->read(2, $id_user, "ASS-I"))) \send_response(403, ["global" => "Anda tidak memiliki akses untuk menginput data asset."]);
          if (!is_empty($id_asset) && empty($this->tmenu->read(2, $id_user, "ASS-E"))) \send_response(403, ["global" => "Anda tidak memiliki akses untuk mengedit data asset."]);

          $id_barang = get_post("id_barang");
          $qr_code = get_post("qr_code");
          $serial_number = get_post("serial_number");
          $tanggal_akuisisi_asset = get_post("tanggal_akuisisi_asset");
          $no_surat_kontrak = get_post("no_surat_kontrak");
          
          $tanggal_berakhir_kontrak = get_post("tanggal_berakhir_kontrak");
          $id_kepemilikan = get_post("id_kepemilikan");
          $keterangan = get_post("keterangan");
          $id_gudang = get_post("id_gudang");
          $id_pelanggan = get_post("id_pelanggan");
          
          $sta = get_post("sta");
          $alasan = get_post("alasan");

          $asset = new C_asset(
            $id_asset, $id_barang, $qr_code, $serial_number, $tanggal_akuisisi_asset,
            $no_surat_kontrak, $tanggal_berakhir_kontrak, $id_kepemilikan, $keterangan, $id_gudang, 
            $id_pelanggan, $sta, $alasan
          );
          $is_valid_asset = $asset->is_valid_asset();
          if (!$is_valid_asset[0]) send_response(400, $is_valid_asset[1]);

          $this->tutils->start();
          $this->tasset->put($asset->get_id_asset(), $asset->get_asset());
          $this->tutils->commit();
          \send_response();
        } catch (\Exception $e) {
          $this->tutils->rollback(["tasset"]);
          \send_500_response(format_exception($e));
        } 
    }
}
