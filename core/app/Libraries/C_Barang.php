<?php
namespace App\Libraries;

use App\Models\M_Barang;

class C_Barang {
    private $tbarang;

    private $barang;
    private $id_barang;
    private $kode_barang;
    private $nama_barang;
    private $id_brand;
    private $nama_brand;
    private $id_tipe;
    private $nama_tipe;
    private $ukuran;
    private $keterangan;

    function __construct() {
        $this->tbarang = new M_Barang();

        $this->set_id_barang(func_get_arg(0));
        $this->set_kode_barang(func_get_arg(1));
        $this->set_nama_barang(func_get_arg(2));
        $this->set_id_brand(func_get_arg(3));
        $this->set_nama_brand(func_get_arg(4));
        $this->set_id_tipe(func_get_arg(5));
        $this->set_nama_tipe(func_get_arg(6));
        $this->set_ukuran(func_get_arg(7));
        $this->set_keterangan(func_get_arg(8));
        
        $this->set_barang();
    }

    function is_valid_barang() {
        $is_valid_id_barang = is_valid_number($this->get_id_barang(), "ID Barang", 2, false, true);
        $is_valid_kode_barang = is_valid_code($this->get_kode_barang(), "Kode barang", true);
        if ($is_valid_kode_barang[0] && !is_empty($this->kode_barang)) {
            $is_valid_kode_barang = !empty($this->tbarang->read(1, "kode_barang", $this->kode_barang, $this->id_barang)) 
            ? [false, "Kode barang telah terdaftar.<br>"]
            : [true];
        }
        $is_valid_nama_barang = is_valid_str($this->get_nama_barang(), "Nama barang", 100, true);
        $is_valid_id_brand = is_valid_number($this->get_id_brand(), "ID Merek", 2, false, true);
        $is_valid_nama_brand = is_valid_str($this->get_nama_brand(), "Nama merek", 50);
        if ($is_valid_nama_brand[0] && !is_empty($this->nama_brand)) {
            $is_valid_nama_brand = !empty($this->tbarang->read(1, "brand", $this->nama_brand, $this->id_brand))
            ? [false, "Nama brand telah terdaftar.<br>"]
            : [true];
        }
        $is_valid_id_tipe = is_valid_number($this->get_id_tipe(), "ID Tipe", 2, false, true);
        $is_valid_nama_tipe = is_valid_str($this->get_nama_tipe(), "Nama tipe", 50);
        if ($is_valid_nama_tipe[0]) {
            $is_valid_nama_tipe = !empty($this->tbarang->read(1, "tipe", $this->nama_tipe, $this->id_tipe))
            ? [false, "Nama tipe telah terdaftar.<br>"]
            : [true];
        }
        $is_valid_ukuran = is_valid_number($this->get_ukuran(), "Ukuran", 2, false, true);
        $is_valid_keterangan = is_valid_str($this->get_keterangan(), "Keterangan", 200, true);
        
        if (!$is_valid_id_barang[0]) $errors["id_barang"] = $is_valid_id_barang[1];
        if (!$is_valid_kode_barang[0]) $errors["kode_barang"] = $is_valid_kode_barang[1];

        if (!$is_valid_nama_barang[0]) $errors["nama_barang"] = $is_valid_nama_barang[1];
        if (!$is_valid_id_brand[0]) $errors["id_brand"] = $is_valid_id_brand[1];
        if (!$is_valid_nama_brand[0]) $errors["nama_brand"] = $is_valid_nama_brand[1];
        if (!$is_valid_id_tipe[0]) $errors["id_tipe"] = $is_valid_id_tipe[1];
        if (!$is_valid_nama_tipe[0]) $errors["nama_tipe"] = $is_valid_tipe[1];
        if (!$is_valid_ukuran[0]) $errors["ukuran"] = $is_valid_ukuran[1];
        if (!$is_valid_keterangan[0]) $errors["keterangan"] = $is_valid_keterangan[1];

        if (isset($errors)) {
            return [false, $errors];
        } else {
            return [true];
        }
    }

    /** accessors and mutators */
    function get_barang() {
        return $this->barang;
    }

    function set_barang() {
        $this->barang[] = $this->get_id_barang();
        $this->barang[] = $this->get_kode_barang();
        $this->barang[] = $this->get_nama_barang();
        $this->barang[] = $this->get_id_brand();
        $this->barang[] = $this->get_nama_brand();
        $this->barang[] = $this->get_id_tipe();
        $this->barang[] = $this->get_nama_tipe();
        $this->barang[] = $this->get_ukuran();
        $this->barang[] = $this->get_keterangan();
    }

    function get_id_barang() {
        return $this->id_barang;
    }

    function set_id_barang($id_barang) {
        $this->id_barang = is_empty($id_barang) ? null : $id_barang;
    }

    function get_kode_barang() {
        return $this->kode_barang;
    }

    function set_kode_barang($kode_barang) {
        $this->kode_barang = is_empty($kode_barang) ? null : "B-" .substr("00000$kode_barang", -5);
    }

    function get_nama_barang() {
        return $this->nama_barang;
    }

    function set_nama_barang($nama_barang) {
        $this->nama_barang = is_empty($nama_barang) ? null : $nama_barang;
    }

    function get_id_brand() {
        return $this->id_brand;
    }

    function set_id_brand($id_brand) {
        $this->id_brand = is_empty($id_brand) ? null : $id_brand;
    }

    function get_nama_brand() {
        return $this->nama_brand;
    }

    function set_nama_brand($nama_brand) {
        $this->nama_brand = $nama_brand;
    }

    function get_id_tipe() {
        return $this->id_tipe;
    }

    function set_id_tipe($id_tipe) {
        $this->id_tipe = is_empty($id_tipe) ? null : $id_tipe;
    }

    function get_nama_tipe() {
        return $this->nama_tipe;
    }

    function set_nama_tipe($nama_tipe) {
        $this->nama_tipe = $nama_tipe;
    }

    function get_ukuran() {
        return $this->ukuran;
    }

    function set_ukuran($ukuran) {
        $this->ukuran = is_empty($ukuran) ? null : $ukuran;
    }

    function get_keterangan() {
        return $this->keterangan;
    }

    function set_keterangan($keterangan) {
        $this->keterangan = is_empty($keterangan) ? NULL : $keterangan;
    }
}
