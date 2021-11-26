<?php

namespace App\Libraries;

use App\Models\M_Pelanggan;

class C_Pelanggan
{
    private $tpelanggan;

    private $pelanggan;

    private $id_pelanggan;
    private $id_agen;
    private $kode_pelanggan;
    private $nama_pelanggan;
    private $no_identitas;

    private $no_hp1;
    private $no_hp2;
    private $email;
    private $id_propinsi;
    private $id_kabupaten;

    private $id_kecamatan;
    private $id_kelurahan;
    private $alamat;
    private $kode_pos;
    private $keterangan;

    private $daya_listrik;
    private $latitude;
    private $longitude;
    private $nama_kerabat;
    private $no_identitas_kerabat;

    private $no_hp_kerabat;
    private $alamat_kerabat;
    private $hubungan;
    private $id_level;

    function __construct()
    {
        $this->tpelanggan = new M_Pelanggan();

        $this->set_id_level(func_get_arg(23));

        $this->set_id_pelanggan(func_get_arg(0));
        $this->set_id_agen(func_get_arg(1));
        $this->set_kode_pelanggan(func_get_arg(2));
        $this->set_nama_pelanggan(func_get_arg(3));
        $this->set_no_identitas(func_get_arg(4));

        $this->set_no_hp1(func_get_arg(5));
        $this->set_no_hp2(func_get_arg(6));
        $this->set_email(func_get_arg(7));
        $this->set_id_propinsi(func_get_arg(8));
        $this->set_id_kabupaten(func_get_arg(9));

        $this->set_id_kecamatan(func_get_arg(10));
        $this->set_id_kelurahan(func_get_arg(11));
        $this->set_alamat(func_get_arg(12));
        $this->set_kode_pos(func_get_arg(13));
        $this->set_keterangan(func_get_arg(14));

        $this->set_daya_listrik(func_get_arg(15));
        $this->set_latitude(func_get_arg(16));
        $this->set_longitude(func_get_arg(17));
        $this->set_nama_kerabat(func_get_arg(18));
        $this->set_no_identitas_kerabat(func_get_arg(19));

        $this->set_no_hp_kerabat(func_get_arg(20));
        $this->set_alamat_kerabat(func_get_arg(21));
        $this->set_hubungan(func_get_arg(22));

        $this->set_pelanggan();
    }

    function is_valid_pelanggan()
    {
        $is_valid_id_pelanggan = is_valid_number($this->get_id_pelanggan(), "ID pelanggan", 2, false, true);
        $is_valid_id_agen = !empty($this->id_agen) && empty($this->tpelanggan->read(1, "id_agen", $this->id_agen)) 
        ? [false, "Data agen tidak dapat ditemukan.<br>"] : [true]; 
        $is_valid_kode_pelanggan = is_valid_code($this->get_kode_pelanggan(), "Kode pelanggan", true);
        if ($is_valid_kode_pelanggan[0] && !is_empty($this->kode_pelanggan)) {
            $is_valid_kode_pelanggan = !empty($this->tpelanggan->read(1, "kode_pelanggan", $this->kode_pelanggan, $this->id_pelanggan)) 
            ? [false, "Kode pelanggan telah terdaftar.<br>"]
            : [true];
        }
        $is_valid_nama_pelanggan = is_valid_name($this->get_nama_pelanggan(), "Nama pelanggan", 100);
        $is_valid_no_identitas = is_valid_number($this->get_no_identitas(), "No. identitas", 0, false, true, 20);

        $is_valid_no_hp1 = is_valid_phone($this->no_hp1, "No. hp1", true);
        $is_valid_no_hp2 = is_valid_phone($this->get_no_hp2(), "No. hp2", true);
        $is_valid_email = is_valid_email($this->get_email(), "Email pelanggan", true);
        $is_valid_id_propinsi = !is_empty($this->id_propinsi) && empty($this->tpelanggan->read(1, "propinsi", $this->id_propinsi))
        ? [false, "Data propinsi tidak dapat ditemukan.<br>"]
        : [true];
        $is_valid_id_kabupaten = !is_empty($this->id_kabupaten) && empty($this->tpelanggan->read(1, "kabupaten", $this->id_kabupaten, $this->id_propinsi))
        ? [false, "Data kabupaten tidak dapat ditemukan.<br>"]
        : [true];
        $is_valid_id_kecamatan = !is_empty($this->id_kecamatan) && empty($this->tpelanggan->read(1, "kecamatan", $this->id_kecamatan, $this->id_kabupaten))
        ? [false, "Data kecamatan tidak dapat ditemukan.<br>"]
        : [true];
        $is_valid_id_kelurahan = !is_empty($this->id_kelurahan) && empty($this->tpelanggan->read(1, "kelurahan", $this->id_kelurahan, $this->id_kecamatan))
        ? [false, "Data kelurahan tidak dapat ditemukan.<br>"]
        : [true];
        $is_valid_alamat = is_valid_str($this->get_alamat(), "Alamat", 200, true);
        $is_valid_kode_pos = is_valid_number($this->get_kode_pos(), "Kodepos", 1, false, true);
        $is_valid_keterangan = is_valid_str($this->get_keterangan(), "Keterangan", 200, true);

        $is_valid_daya_listrik = is_valid_number($this->get_daya_listrik(), "Daya listrik", 2, false, true);
        $is_valid_latitude = is_valid_number($this->get_latitude(), "Latitude", 0, true, true);
        $is_valid_longitude = is_valid_number($this->get_longitude(), "Longitude", 0, true, true);
        $is_valid_nama_kerabat = is_valid_name($this->get_nama_kerabat(), "Nama Kerabat", 100, true);
        $is_valid_no_identitas_kerabat = is_valid_number($this->get_no_identitas_kerabat(), "No. identitas kerabat", 0, false, true, 20);

        $is_valid_no_hp_kerabat = is_valid_phone($this->get_no_hp_kerabat(), "No. hp kerabat", true);
        $is_valid_alamat_kerabat = is_valid_str($this->get_alamat_kerabat(), "Alamat kerabat", 200, true);
        $is_valid_hubungan = is_valid_name($this->get_hubungan(), "Hubungan", "50", true);
        if (!in_array($this->get_id_level(), ["1", "2"])) {
            $is_valid_id_level = [false, "ID level tidak valid.<br>"];
        } else {
            $is_valid_id_level = [true];
        }

        if (!$is_valid_id_pelanggan[0]) $errors["kode_pelanggan"] = $is_valid_id_pelanggan[1];
        if (!$is_valid_id_agen[0]) $errors["agen"] = $is_valid_id_agen[1];
        if (!$is_valid_kode_pelanggan[0]) $errors["kode_pelanggan"] = $is_valid_kode_pelanggan[1];
        if (!$is_valid_nama_pelanggan[0]) $errors["nama_pelanggan"] = $is_valid_nama_pelanggan[1];
        if (!$is_valid_no_identitas[0]) $errors["no_identitas"] = $is_valid_no_identitas[1];

        if (!$is_valid_no_hp1[0]) $errors["no_hp1"] = $is_valid_no_hp1[1];
        if (!$is_valid_no_hp2[0]) $errors["no_hp2"] = $is_valid_no_hp2[1];
        if (!$is_valid_email[0]) $errors["email"] = $is_valid_email[1];
        if (!$is_valid_id_propinsi[0]) $errors["nama_propinsi"] = $is_valid_id_propinsi[1];
        if (!$is_valid_id_kabupaten[0]) $errors["nama_kabupaten"] = $is_valid_id_kabupaten[1];

        if (!$is_valid_id_kecamatan[0]) $errors["nama_kecamatan"] = $is_valid_id_kecamatan[1];
        if (!$is_valid_id_kelurahan[0]) $errors["nama_kelurahan"] = $is_valid_id_kelurahan[1];
        if (!$is_valid_alamat[0]) $errors["alamat"] = $is_valid_alamat[1];
        if (!$is_valid_kode_pos[0]) $errors["kode_pos"] = $is_valid_kode_pos[1];
        if (!$is_valid_keterangan[0]) $errors["keterangan"] = $is_valid_keterangan[1];

        if (!$is_valid_daya_listrik[0]) $errors["daya_listrik"] = $is_valid_daya_listrik[1];
        if (!$is_valid_latitude[0]) $errors["latitude"] = $is_valid_latitude[1];
        if (!$is_valid_longitude[0]) $errors["longitude"] = $is_valid_longitude[1];
        if (!$is_valid_nama_kerabat[0]) $errors["nama_kerabat"] = $is_valid_nama_kerabat[1];
        if (!$is_valid_no_identitas_kerabat[0]) $errors["no_identitas_kerabat"] = $is_valid_no_identitas_kerabat[1];

        if (!$is_valid_no_hp_kerabat[0]) $errors["no_hp_kerabat"] = $is_valid_no_hp_kerabat[1];
        if (!$is_valid_alamat_kerabat[0]) $errors["alamat_kerabat"] = $is_valid_alamat_kerabat[1];
        if (!$is_valid_hubungan[0]) $errors["hubungan"] = $is_valid_hubungan[1];
        if (!$is_valid_id_level[0]) $errors["id_level"] = $is_valid_id_level[1];

        if (isset($errors)) {
            return [false, $errors];
        } else {
            return [true];
        }
    }

    /** accessors and mutators */
    function get_pelanggan()
    {
        return $this->pelanggan;
    }

    function set_pelanggan()
    {
        $this->pelanggan[] = $this->get_id_pelanggan();
        $this->pelanggan[] = $this->get_id_agen();
        $this->pelanggan[] = $this->get_kode_pelanggan();
        $this->pelanggan[] = $this->get_nama_pelanggan();
        $this->pelanggan[] = $this->get_no_identitas();

        $this->pelanggan[] = $this->get_no_hp1();
        $this->pelanggan[] = $this->get_no_hp2();
        $this->pelanggan[] = $this->get_email();
        $this->pelanggan[] = $this->get_id_propinsi();
        $this->pelanggan[] = $this->get_id_kabupaten();

        $this->pelanggan[] = $this->get_id_kecamatan();
        $this->pelanggan[] = $this->get_id_kelurahan();
        $this->pelanggan[] = $this->get_alamat();
        $this->pelanggan[] = $this->get_kode_pos();
        $this->pelanggan[] = $this->get_keterangan();

        $this->pelanggan[] = $this->get_daya_listrik();
        $this->pelanggan[] = $this->get_latitude();
        $this->pelanggan[] = $this->get_longitude();
        $this->pelanggan[] = $this->get_nama_kerabat();
        $this->pelanggan[] = $this->get_no_identitas_kerabat();

        $this->pelanggan[] = $this->get_no_hp_kerabat();
        $this->pelanggan[] = $this->get_alamat_kerabat();
        $this->pelanggan[] = $this->get_hubungan();
        $this->pelanggan[] = $this->get_id_level();
    }

    function get_id_pelanggan()
    {
        return $this->id_pelanggan;
    }

    function set_id_pelanggan($id_pelanggan)
    {
        $this->id_pelanggan = if_empty_then($id_pelanggan, null);
    }

    function get_id_agen()
    {
        return $this->id_agen;
    }

    function set_id_agen($id_agen)
    {
        $this->id_agen = if_empty_then($id_agen, null);
    }

    function get_kode_pelanggan()
    {
        return $this->kode_pelanggan;
    }

    function set_kode_pelanggan($kode_pelanggan)
    {
        $this->kode_pelanggan = is_empty($kode_pelanggan) ? null : ($this->get_id_level() === "1" ? "RET" : "AGE") . "-" . substr("0000$kode_pelanggan", -4);
    }

    function get_nama_pelanggan()
    {
        return $this->nama_pelanggan;
    }

    function set_nama_pelanggan($nama_pelanggan)
    {
        $this->nama_pelanggan = $nama_pelanggan;
    }

    function get_no_identitas()
    {
        return $this->no_identitas;
    }

    function set_no_identitas($no_identitas)
    {
        $this->no_identitas = if_empty_then($no_identitas, null);
    }

    function get_no_hp1()
    {
        return $this->no_hp1;
    }

    function set_no_hp1($no_hp1)
    {
        $this->no_hp1 = if_empty_then($no_hp1, null);
    }

    function get_no_hp2()
    {
        return $this->no_hp2;
    }

    function set_no_hp2($no_hp2)
    {
        $this->no_hp2 = if_empty_then($no_hp2, null);
    }

    function get_email()
    {
        return $this->email;
    }

    function set_email($email)
    {
        $this->email = if_empty_then($email, null);
    }

    function get_id_propinsi()
    {
        return $this->id_propinsi;
    }

    function set_id_propinsi($id_propinsi)
    {
        $this->id_propinsi = if_empty_then($id_propinsi, null);
    }

    function get_id_kabupaten()
    {
        return $this->id_kabupaten;
    }

    function set_id_kabupaten($id_kabupaten)
    {
        $this->id_kabupaten = if_empty_then($id_kabupaten, null);
    }

    function get_id_kecamatan()
    {
        return $this->id_kecamatan;
    }

    function set_id_kecamatan($id_kecamatan)
    {
        $this->id_kecamatan = if_empty_then($id_kecamatan, null);
    }

    function get_id_kelurahan()
    {
        return $this->id_kelurahan;
    }

    function set_id_kelurahan($id_kelurahan)
    {
        $this->id_kelurahan = if_empty_then($id_kelurahan, null);
    }

    function get_alamat()
    {
        return $this->alamat;
    }

    function set_alamat($alamat)
    {
        $this->alamat = if_empty_then($alamat, null);
    }

    function get_kode_pos()
    {
        return $this->kode_pos;
    }

    function set_kode_pos($kode_pos)
    {
        $this->kode_pos = if_empty_then($kode_pos, null);
    }

    function get_keterangan()
    {
        return $this->keterangan;
    }

    function set_keterangan($keterangan)
    {
        $this->keterangan = if_empty_then($keterangan, null);
    }

    function get_daya_listrik()
    {
        return $this->daya_listrik;
    }

    function set_daya_listrik($daya_listrik)
    {
        $this->daya_listrik = if_empty_then($daya_listrik, null);
    }

    function get_latitude()
    {
        return $this->latitude;
    }

    function set_latitude($latitude)
    {
        $this->latitude = if_empty_then($latitude, null);
    }

    function get_longitude()
    {
        return $this->longitude;
    }

    function set_longitude($longitude)
    {
        $this->longitude = if_empty_then($longitude, null);
    }

    function get_nama_kerabat()
    {
        return $this->nama_kerabat;
    }

    function set_nama_kerabat($nama_kerabat)
    {
        $this->nama_kerabat = if_empty_then($nama_kerabat, null);
    }

    function get_no_identitas_kerabat()
    {
        return $this->no_identitas_kerabat;
    }

    function set_no_identitas_kerabat($no_identitas_kerabat)
    {
        $this->no_identitas_kerabat = if_empty_then($no_identitas_kerabat, null);
    }

    function get_no_hp_kerabat()
    {
        return $this->no_hp_kerabat;
    }

    function set_no_hp_kerabat($no_hp_kerabat)
    {
        $this->no_hp_kerabat = if_empty_then($no_hp_kerabat, null);
    }

    function get_alamat_kerabat()
    {
        return $this->alamat_kerabat;
    }

    function set_alamat_kerabat($alamat_kerabat)
    {
        $this->alamat_kerabat = if_empty_then($alamat_kerabat, null);
    }

    function get_hubungan()
    {
        return $this->hubungan;
    }

    function set_hubungan($hubungan)
    {
        $this->hubungan = if_empty_then($hubungan, null);
    }

    function get_id_level()
    {
        return $this->id_level;
    }

    function set_id_level($id_level)
    {
        $this->id_level = if_empty_then($id_level, null);
    }
}
