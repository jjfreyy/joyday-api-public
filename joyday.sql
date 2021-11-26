-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versi server:                 10.4.10-MariaDB - mariadb.org binary distribution
-- OS Server:                    Win64
-- HeidiSQL Versi:               11.1.0.6116
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Membuang struktur basisdata untuk joyday
DROP DATABASE IF EXISTS `joyday`;
CREATE DATABASE IF NOT EXISTS `joyday` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `joyday`;

-- membuang struktur untuk table joyday.tasset
CREATE TABLE IF NOT EXISTS `tasset` (
  `id_asset` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_barang` int(10) unsigned NOT NULL,
  `qr_code` varchar(30) NOT NULL,
  `serial_number` varchar(30) DEFAULT NULL,
  `tanggal_akuisisi_asset` date DEFAULT NULL,
  `no_surat_kontrak` varchar(30) DEFAULT NULL,
  `tanggal_berakhir_kontrak` date DEFAULT NULL,
  `kondisi` varchar(20) DEFAULT NULL,
  `id_kepemilikan` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `keterangan` varchar(200) DEFAULT NULL,
  `id_gudang` tinyint(3) unsigned DEFAULT NULL COMMENT 'null jika asset berada dipinjamkan ke pelanggan',
  `id_pelanggan` int(10) unsigned DEFAULT NULL COMMENT 'null jika asset ada di dalam gudang',
  `id_input` int(10) unsigned DEFAULT NULL COMMENT 'id dari barang masuk/barang keluar/mutasi',
  `dari_input` tinyint(1) unsigned DEFAULT NULL COMMENT '0 = barang masuk, 1 = barang keluar, 2 = mutasi',
  `sta` tinyint(1) unsigned NOT NULL DEFAULT 2 COMMENT '0=hapus, 1=rusak, 2=siap pakai',
  `alasan` varchar(200) DEFAULT NULL,
  `tanggal_buat` datetime NOT NULL DEFAULT current_timestamp(),
  `tanggal_update` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_asset`),
  KEY `id_barang#tasset-tbarang` (`id_barang`),
  KEY `id_gudang#tasset-tgudang` (`id_gudang`),
  KEY `id_pelanggan#tasset-tpelanggan` (`id_pelanggan`),
  KEY `id_input` (`id_input`),
  KEY `no_surat_kontrak` (`no_surat_kontrak`),
  KEY `serial_number` (`serial_number`),
  KEY `id_kepemilikan#tasset-tkepemilikan` (`id_kepemilikan`),
  KEY `qr_code` (`qr_code`),
  CONSTRAINT `id_barang#tasset-tbarang` FOREIGN KEY (`id_barang`) REFERENCES `tbarang` (`id_barang`) ON UPDATE CASCADE,
  CONSTRAINT `id_gudang#tasset-tgudang` FOREIGN KEY (`id_gudang`) REFERENCES `tgudang` (`id_gudang`) ON UPDATE CASCADE,
  CONSTRAINT `id_kepemilikan#tasset-tkepemilikan` FOREIGN KEY (`id_kepemilikan`) REFERENCES `tkepemilikan` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `id_pelanggan#tasset-tpelanggan` FOREIGN KEY (`id_pelanggan`) REFERENCES `tpelanggan` (`id_pelanggan`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1138 DEFAULT CHARSET=utf8;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table joyday.tbarang
CREATE TABLE IF NOT EXISTS `tbarang` (
  `id_barang` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `kode_barang` varchar(30) NOT NULL,
  `nama_barang` varchar(100) DEFAULT NULL,
  `id_brand` int(10) unsigned DEFAULT NULL,
  `id_tipe` int(10) unsigned NOT NULL,
  `ukuran` int(11) DEFAULT NULL,
  `keterangan` varchar(200) DEFAULT NULL,
  `sta` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `alasan` varchar(200) DEFAULT NULL,
  `tanggal_buat` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `tanggal_update` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_barang`) USING BTREE,
  UNIQUE KEY `serial_number` (`kode_barang`) USING BTREE,
  KEY `id_tipe#tasset-tasset_tipe` (`id_tipe`),
  KEY `id_brand#tasset-tasset_brand` (`id_brand`) USING BTREE,
  CONSTRAINT `id_brand#tbarang-tbarang1` FOREIGN KEY (`id_brand`) REFERENCES `tbarang1` (`id_brand`) ON UPDATE CASCADE,
  CONSTRAINT `id_tipe#tbarang-tbarang2` FOREIGN KEY (`id_tipe`) REFERENCES `tbarang2` (`id_tipe`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table joyday.tbarang1
CREATE TABLE IF NOT EXISTS `tbarang1` (
  `id_brand` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nama_brand` varchar(50) NOT NULL,
  `sta` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `tanggal_buat` timestamp NOT NULL DEFAULT current_timestamp(),
  `tanggal_update` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_brand`) USING BTREE,
  UNIQUE KEY `brand` (`nama_brand`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table joyday.tbarang2
CREATE TABLE IF NOT EXISTS `tbarang2` (
  `id_tipe` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nama_tipe` varchar(50) NOT NULL,
  `sta` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `tanggal_buat` timestamp NOT NULL DEFAULT current_timestamp(),
  `tanggal_update` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_tipe`) USING BTREE,
  UNIQUE KEY `tipe` (`nama_tipe`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table joyday.tbarang_keluar
CREATE TABLE IF NOT EXISTS `tbarang_keluar` (
  `id_barang_keluar` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `no_keluar` varchar(30) NOT NULL,
  `id_pengurus` int(10) unsigned NOT NULL COMMENT 'pengurus barang keluar',
  `id_gudang` tinyint(3) unsigned NOT NULL,
  `keterangan` varchar(200) DEFAULT NULL,
  `sta` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `alasan` varchar(200) DEFAULT NULL,
  `tanggal_buat` datetime NOT NULL DEFAULT current_timestamp(),
  `tanggal_update` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_barang_keluar`),
  UNIQUE KEY `no_keluar` (`no_keluar`),
  KEY `id_gudang#tbarang_keluar-tgudang` (`id_gudang`),
  KEY `id_pengurus#tbarang_keluar-tuser` (`id_pengurus`) USING BTREE,
  CONSTRAINT `id_gudang#tbarang_keluar-tgudang` FOREIGN KEY (`id_gudang`) REFERENCES `tgudang` (`id_gudang`) ON UPDATE CASCADE,
  CONSTRAINT `id_pengurus#tbarang_keluar-tuser` FOREIGN KEY (`id_pengurus`) REFERENCES `tuser` (`id_user`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table joyday.tbarang_keluar1
CREATE TABLE IF NOT EXISTS `tbarang_keluar1` (
  `id_barang_keluar` int(10) unsigned NOT NULL,
  `no` smallint(5) unsigned NOT NULL,
  `id_asset` int(10) unsigned NOT NULL,
  `ke_id_pelanggan` int(10) unsigned NOT NULL,
  `id_input_terakhir` int(10) unsigned DEFAULT NULL COMMENT 'id barang masuk terakhir',
  `keterangan` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id_barang_keluar`,`no`) USING BTREE,
  KEY `id_asset#tbarang_keluar1-tasset` (`id_asset`),
  KEY `id_input` (`id_input_terakhir`) USING BTREE,
  KEY `id_pelanggan#tbarang_keluar1-tpelanggan` (`ke_id_pelanggan`) USING BTREE,
  CONSTRAINT `id_asset#tbarang_keluar1-tasset` FOREIGN KEY (`id_asset`) REFERENCES `tasset` (`id_asset`) ON UPDATE CASCADE,
  CONSTRAINT `id_barang_keluar#tbarang_keluar1-tbarang_keluar` FOREIGN KEY (`id_barang_keluar`) REFERENCES `tbarang_keluar` (`id_barang_keluar`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `ke_id_pelanggan#tbarang_keluar1-tpelanggan` FOREIGN KEY (`ke_id_pelanggan`) REFERENCES `tpelanggan` (`id_pelanggan`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table joyday.tbarang_masuk
CREATE TABLE IF NOT EXISTS `tbarang_masuk` (
  `id_barang_masuk` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `no_masuk` varchar(30) NOT NULL COMMENT 'BMD = dari distributor, BMP = dari pelanggan, BMA = ke agen',
  `tipe` tinyint(1) unsigned NOT NULL COMMENT '0 = Dari Distributor, 1 = Dari Pelanggan, 2 = Ke Agen',
  `id_penerima` int(10) unsigned NOT NULL COMMENT 'user penerima barang masuk',
  `no_faktur` varchar(30) DEFAULT NULL,
  `dari_id_pesanan` int(10) unsigned DEFAULT NULL COMMENT 'masuk dari distributor / ke agen',
  `ke_id_gudang` tinyint(3) unsigned DEFAULT NULL COMMENT 'masuk ke gudang',
  `ke_id_agen` int(10) unsigned DEFAULT NULL COMMENT 'masuk ke agen',
  `keterangan` varchar(200) DEFAULT NULL,
  `sta` tinyint(1) unsigned NOT NULL DEFAULT 1 COMMENT '0 = hapus, 1 = aktif, 2 = input sebagian',
  `alasan` varchar(200) DEFAULT NULL,
  `tanggal_buat` datetime NOT NULL DEFAULT current_timestamp(),
  `tanggal_update` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_barang_masuk`),
  UNIQUE KEY `no_surat_jalan` (`no_masuk`) USING BTREE,
  KEY `id_penerima#tbarang_masuk-tuser` (`id_penerima`) USING BTREE,
  KEY `id_gudang#tbarang_masuk-tgudang` (`ke_id_gudang`) USING BTREE,
  KEY `id_pesanan#tbarang_masuk-tpesanan` (`dari_id_pesanan`) USING BTREE,
  KEY `ke_id_agen#tbarang_masuk-tpelanggan` (`ke_id_agen`),
  KEY `no_faktur` (`no_faktur`),
  CONSTRAINT `dari_id_pesanan#tbarang_masuk-tpesanan` FOREIGN KEY (`dari_id_pesanan`) REFERENCES `tpesanan` (`id_pesanan`) ON UPDATE CASCADE,
  CONSTRAINT `id_penerima#tbarang_masuk-tuser` FOREIGN KEY (`id_penerima`) REFERENCES `tuser` (`id_user`) ON UPDATE CASCADE,
  CONSTRAINT `ke_id_agen#tbarang_masuk-tpelanggan` FOREIGN KEY (`ke_id_agen`) REFERENCES `tpelanggan` (`id_pelanggan`) ON UPDATE CASCADE,
  CONSTRAINT `ke_id_gudang#tbarang_masuk-tgudang` FOREIGN KEY (`ke_id_gudang`) REFERENCES `tgudang` (`id_gudang`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table joyday.tbarang_masuk1
CREATE TABLE IF NOT EXISTS `tbarang_masuk1` (
  `id_barang_masuk` int(10) unsigned NOT NULL,
  `no` smallint(5) unsigned NOT NULL,
  `id_asset` int(10) unsigned NOT NULL,
  `id_barang` int(10) unsigned NOT NULL,
  `dari_id_pelanggan` int(10) unsigned DEFAULT NULL,
  `id_input_terakhir` int(10) unsigned DEFAULT NULL,
  `dari_input` tinyint(1) unsigned DEFAULT NULL,
  `keterangan` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id_barang_masuk`,`no`),
  KEY `id_asset#tbarang_masuk1-tasset` (`id_asset`),
  KEY `id_input` (`id_input_terakhir`) USING BTREE,
  KEY `dari_id_pelanggan#tbarang_masuk1-tpelanggan` (`dari_id_pelanggan`) USING BTREE,
  KEY `id_barang#tbarang_masuk1-tbarang` (`id_barang`),
  CONSTRAINT `dari_id_pelanggan#tbarang_masuk1-tpelanggan` FOREIGN KEY (`dari_id_pelanggan`) REFERENCES `tpelanggan` (`id_pelanggan`) ON UPDATE CASCADE,
  CONSTRAINT `id_asset#tbarang_masuk1-tasset` FOREIGN KEY (`id_asset`) REFERENCES `tasset` (`id_asset`) ON UPDATE CASCADE,
  CONSTRAINT `id_barang#tbarang_masuk1-tbarang` FOREIGN KEY (`id_barang`) REFERENCES `tbarang` (`id_barang`) ON UPDATE CASCADE,
  CONSTRAINT `id_barang_masuk#tbarang_masuk1-tbarang_masuk` FOREIGN KEY (`id_barang_masuk`) REFERENCES `tbarang_masuk` (`id_barang_masuk`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table joyday.tbulan
CREATE TABLE IF NOT EXISTS `tbulan` (
  `kode` varchar(2) NOT NULL,
  `nama` varchar(20) NOT NULL,
  PRIMARY KEY (`kode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table joyday.tdistributor
CREATE TABLE IF NOT EXISTS `tdistributor` (
  `id_distributor` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `kode_distributor` varchar(30) NOT NULL,
  `nama_distributor` varchar(100) NOT NULL,
  `alamat` varchar(200) NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `email` varchar(50) DEFAULT NULL,
  `keterangan` varchar(200) DEFAULT NULL,
  `sta` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `alasan` varchar(200) DEFAULT NULL,
  `tanggal_buat` datetime NOT NULL DEFAULT current_timestamp(),
  `tanggal_update` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_distributor`) USING BTREE,
  UNIQUE KEY `kode_distributor` (`kode_distributor`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table joyday.test
CREATE TABLE IF NOT EXISTS `test` (
  `id` int(10) unsigned NOT NULL,
  `val` varchar(50) NOT NULL,
  `qty` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `val` (`val`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table joyday.tgudang
CREATE TABLE IF NOT EXISTS `tgudang` (
  `id_gudang` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `id_kepala_gudang` int(10) unsigned DEFAULT NULL,
  `kode_gudang` varchar(30) NOT NULL,
  `nama_gudang` varchar(100) NOT NULL,
  `keterangan` varchar(200) DEFAULT NULL,
  `sta` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `alasan` varchar(200) DEFAULT NULL,
  `tanggal_buat` datetime NOT NULL DEFAULT current_timestamp(),
  `tanggal_update` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_gudang`),
  UNIQUE KEY `kode_gudang` (`kode_gudang`),
  KEY `id_kepala_gudang#tgudang-tuser` (`id_kepala_gudang`),
  CONSTRAINT `id_kepala_gudang#tgudang-tuser` FOREIGN KEY (`id_kepala_gudang`) REFERENCES `tuser` (`id_user`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table joyday.thakakses
CREATE TABLE IF NOT EXISTS `thakakses` (
  `kode_akses` varchar(30) NOT NULL,
  `nama_akses` varchar(100) NOT NULL,
  `keterangan` varchar(200) DEFAULT NULL,
  `sta` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `tanggal_buat` datetime NOT NULL DEFAULT current_timestamp(),
  `tanggal_update` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`kode_akses`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table joyday.tkabupaten
CREATE TABLE IF NOT EXISTS `tkabupaten` (
  `id_kabupaten` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_propinsi` int(10) unsigned NOT NULL,
  `nama_kabupaten` varchar(100) NOT NULL,
  `tanggal_buat` datetime NOT NULL DEFAULT current_timestamp(),
  `tanggal_update` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_kabupaten`) USING BTREE,
  KEY `id_propinsi#tkabupaten-tpropinsi` (`id_propinsi`),
  CONSTRAINT `id_propinsi#tkabupaten-tpropinsi` FOREIGN KEY (`id_propinsi`) REFERENCES `tpropinsi` (`id_propinsi`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=515 DEFAULT CHARSET=utf8;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table joyday.tkecamatan
CREATE TABLE IF NOT EXISTS `tkecamatan` (
  `id_kecamatan` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_kabupaten` int(10) unsigned NOT NULL,
  `nama_kecamatan` varchar(100) NOT NULL,
  `tanggal_buat` datetime NOT NULL DEFAULT current_timestamp(),
  `tanggal_update` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_kecamatan`),
  KEY `id_kabupaten#tkecamatan-tkabupaten` (`id_kabupaten`),
  KEY `nama` (`nama_kecamatan`) USING BTREE,
  CONSTRAINT `id_kabupaten#tkecamatan-tkabupaten` FOREIGN KEY (`id_kabupaten`) REFERENCES `tkabupaten` (`id_kabupaten`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7202 DEFAULT CHARSET=utf8;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table joyday.tkelurahan
CREATE TABLE IF NOT EXISTS `tkelurahan` (
  `id_kelurahan` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_kecamatan` int(10) unsigned NOT NULL,
  `nama_kelurahan` varchar(100) NOT NULL,
  `tanggal_buat` datetime NOT NULL DEFAULT current_timestamp(),
  `tanggal_update` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_kelurahan`),
  KEY `id_kecamatan#tkelurahan-tkecamatan` (`id_kecamatan`),
  CONSTRAINT `id_kecamatan#tkelurahan-tkecamatan` FOREIGN KEY (`id_kecamatan`) REFERENCES `tkecamatan` (`id_kecamatan`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=83437 DEFAULT CHARSET=utf8;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table joyday.tkepemilikan
CREATE TABLE IF NOT EXISTS `tkepemilikan` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `nama_kepemilikan` varchar(100) CHARACTER SET latin1 NOT NULL,
  `keterangan` varchar(200) CHARACTER SET latin1 DEFAULT NULL,
  `sta` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `tanggal_buat` timestamp NOT NULL DEFAULT current_timestamp(),
  `tanggal_update` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `nama_kepemilikan` (`nama_kepemilikan`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table joyday.tlokasi_awal_asset
CREATE TABLE IF NOT EXISTS `tlokasi_awal_asset` (
  `id_asset` int(10) unsigned NOT NULL,
  `id_gudang` tinyint(3) unsigned DEFAULT NULL,
  `id_pelanggan` int(10) unsigned DEFAULT NULL,
  `tgl_buat` datetime NOT NULL DEFAULT current_timestamp(),
  `tgl_update` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_asset`),
  KEY `id_gudang#tlokasi_awal_asset-tgudang` (`id_gudang`),
  KEY `id_pelanggan#tlokasi_awal_asset-tpelanggan` (`id_pelanggan`),
  CONSTRAINT `id_asset#tlokasi_awal_asset-tasset` FOREIGN KEY (`id_asset`) REFERENCES `tasset` (`id_asset`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `id_gudang#tlokasi_awal_asset-tgudang` FOREIGN KEY (`id_gudang`) REFERENCES `tgudang` (`id_gudang`) ON UPDATE CASCADE,
  CONSTRAINT `id_pelanggan#tlokasi_awal_asset-tpelanggan` FOREIGN KEY (`id_pelanggan`) REFERENCES `tpelanggan` (`id_pelanggan`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table joyday.tmenu
CREATE TABLE IF NOT EXISTS `tmenu` (
  `kode` varchar(5) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `link` varchar(50) NOT NULL DEFAULT '#',
  `sta` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `kode_akses` varchar(30) DEFAULT NULL,
  `tanggal_buat` datetime NOT NULL DEFAULT current_timestamp(),
  `tanggal_update` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`kode`),
  KEY `kode_akses#tmenu-thakakses` (`kode_akses`),
  CONSTRAINT `kode_akses#tmenu-thakakses` FOREIGN KEY (`kode_akses`) REFERENCES `thakakses` (`kode_akses`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table joyday.tmutasi
CREATE TABLE IF NOT EXISTS `tmutasi` (
  `id_mutasi` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `no_mutasi` varchar(30) NOT NULL,
  `id_user` int(10) unsigned NOT NULL COMMENT 'User yg menginput',
  `dari_id_pelanggan` int(10) unsigned DEFAULT NULL,
  `keterangan` varchar(200) DEFAULT NULL,
  `sta` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `alasan` varchar(200) DEFAULT NULL,
  `tanggal_buat` datetime NOT NULL DEFAULT current_timestamp(),
  `tanggal_update` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_mutasi`),
  UNIQUE KEY `kode_mutasi` (`no_mutasi`) USING BTREE,
  KEY `dari_id_pelanggan#tmutasi-tpelanggan` (`dari_id_pelanggan`),
  KEY `id_user#tmutasi-tuser` (`id_user`),
  CONSTRAINT `dari_id_pelanggan#tmutasi-tpelanggan` FOREIGN KEY (`dari_id_pelanggan`) REFERENCES `tpelanggan` (`id_pelanggan`) ON UPDATE CASCADE,
  CONSTRAINT `id_user#tmutasi-tuser` FOREIGN KEY (`id_user`) REFERENCES `tuser` (`id_user`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table joyday.tmutasi1
CREATE TABLE IF NOT EXISTS `tmutasi1` (
  `id_mutasi` int(10) unsigned NOT NULL,
  `no` smallint(5) unsigned NOT NULL,
  `id_asset` int(10) unsigned NOT NULL,
  `ke_id_pelanggan` int(10) unsigned NOT NULL,
  `id_input_terakhir` int(10) unsigned DEFAULT NULL,
  `dari_input` tinyint(1) unsigned DEFAULT NULL,
  `keterangan` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id_mutasi`,`no`),
  KEY `id_asset#tmutasi1-tasset` (`id_asset`),
  KEY `ke_id_pelanggan#tmutasi1-tpelanggan` (`ke_id_pelanggan`),
  KEY `id_input_terakhir` (`id_input_terakhir`),
  CONSTRAINT `id_asset#tmutasi1-tasset` FOREIGN KEY (`id_asset`) REFERENCES `tasset` (`id_asset`) ON UPDATE CASCADE,
  CONSTRAINT `id_mutasi#tmutasi1-tmutasi` FOREIGN KEY (`id_mutasi`) REFERENCES `tmutasi` (`id_mutasi`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `ke_id_pelanggan#tmutasi1-tpelanggan` FOREIGN KEY (`ke_id_pelanggan`) REFERENCES `tpelanggan` (`id_pelanggan`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table joyday.tpelanggan
CREATE TABLE IF NOT EXISTS `tpelanggan` (
  `id_pelanggan` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_agen` int(10) unsigned DEFAULT NULL,
  `kode_pelanggan` varchar(30) DEFAULT NULL,
  `nama_pelanggan` varchar(100) NOT NULL,
  `no_identitas` varchar(20) DEFAULT NULL,
  `no_hp1` varchar(20) DEFAULT NULL,
  `no_hp2` varchar(20) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `id_propinsi` int(10) unsigned DEFAULT NULL,
  `id_kabupaten` int(10) unsigned DEFAULT NULL,
  `id_kecamatan` int(10) unsigned DEFAULT NULL,
  `id_kelurahan` int(10) unsigned DEFAULT NULL,
  `alamat` varchar(200) DEFAULT NULL,
  `kode_pos` varchar(10) DEFAULT NULL,
  `keterangan` varchar(200) DEFAULT NULL,
  `daya_listrik` int(11) DEFAULT NULL,
  `latitude` varchar(30) DEFAULT NULL,
  `longitude` varchar(30) DEFAULT NULL,
  `nama_kerabat` varchar(100) DEFAULT NULL,
  `no_identitas_kerabat` varchar(20) DEFAULT NULL,
  `no_hp_kerabat` varchar(20) DEFAULT NULL,
  `alamat_kerabat` varchar(200) DEFAULT NULL,
  `hubungan` varchar(50) DEFAULT NULL,
  `id_level` tinyint(1) unsigned NOT NULL,
  `sta` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `alasan` varchar(200) DEFAULT NULL,
  `tanggal_buat` timestamp NOT NULL DEFAULT current_timestamp(),
  `tanggal_update` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_pelanggan`),
  UNIQUE KEY `kode_pelanggan` (`kode_pelanggan`),
  KEY `id_kabupaten#tpelanggan-tkabupaten` (`id_kabupaten`),
  KEY `id_propinsi#tpelanggan-tpropinsi` (`id_propinsi`),
  KEY `id_kecamatan#tpelanggan-tkecamatan` (`id_kecamatan`),
  KEY `id_kelurahan#tpelanggan-tkelurahan` (`id_kelurahan`),
  KEY `id_level#tpelanggan-tpelanggan1` (`id_level`),
  KEY `id_agen#tpelanggan` (`id_agen`),
  CONSTRAINT `id_agen#tpelanggan` FOREIGN KEY (`id_agen`) REFERENCES `tpelanggan` (`id_pelanggan`) ON UPDATE CASCADE,
  CONSTRAINT `id_kabupaten#tpelanggan-tkabupaten` FOREIGN KEY (`id_kabupaten`) REFERENCES `tkabupaten` (`id_kabupaten`) ON UPDATE CASCADE,
  CONSTRAINT `id_kecamatan#tpelanggan-tkecamatan` FOREIGN KEY (`id_kecamatan`) REFERENCES `tkecamatan` (`id_kecamatan`) ON UPDATE CASCADE,
  CONSTRAINT `id_kelurahan#tpelanggan-tkelurahan` FOREIGN KEY (`id_kelurahan`) REFERENCES `tkelurahan` (`id_kelurahan`) ON UPDATE CASCADE,
  CONSTRAINT `id_level#tpelanggan-tpelanggan1` FOREIGN KEY (`id_level`) REFERENCES `tpelanggan1` (`id_level`) ON UPDATE CASCADE,
  CONSTRAINT `id_propinsi#tpelanggan-tpropinsi` FOREIGN KEY (`id_propinsi`) REFERENCES `tpropinsi` (`id_propinsi`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=876 DEFAULT CHARSET=utf8;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table joyday.tpelanggan1
CREATE TABLE IF NOT EXISTS `tpelanggan1` (
  `id_level` tinyint(1) unsigned NOT NULL,
  `nama_level` varchar(50) NOT NULL,
  `keterangan` varchar(200) DEFAULT NULL,
  `tanggal_buat` datetime NOT NULL DEFAULT current_timestamp(),
  `tanggal_update` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table joyday.tpesanan
CREATE TABLE IF NOT EXISTS `tpesanan` (
  `id_pesanan` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `no_po` varchar(30) NOT NULL,
  `id_distributor` int(10) unsigned NOT NULL,
  `id_pemesan` int(10) unsigned NOT NULL COMMENT 'User pemesan',
  `keterangan` varchar(200) DEFAULT NULL,
  `sta` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `alasan` varchar(200) DEFAULT NULL,
  `tanggal_buat` datetime NOT NULL DEFAULT current_timestamp(),
  `tanggal_update` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_pesanan`),
  UNIQUE KEY `no_po` (`no_po`),
  KEY `id_pemesan#tpesanan-tuser` (`id_pemesan`),
  KEY `id_distributor#tpesanan-tdistributor` (`id_distributor`),
  CONSTRAINT `id_distributor#tpesanan-tdistributor` FOREIGN KEY (`id_distributor`) REFERENCES `tdistributor` (`id_distributor`) ON UPDATE CASCADE,
  CONSTRAINT `id_pemesan#tpesanan-tuser` FOREIGN KEY (`id_pemesan`) REFERENCES `tuser` (`id_user`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table joyday.tpesanan1
CREATE TABLE IF NOT EXISTS `tpesanan1` (
  `id_pesanan` int(10) unsigned NOT NULL,
  `no` smallint(5) unsigned NOT NULL,
  `id_barang` int(10) unsigned NOT NULL,
  `qty` int(10) unsigned NOT NULL,
  `keterangan` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id_pesanan`,`no`),
  KEY `id_asset#tpesanan1-tasset` (`id_barang`) USING BTREE,
  CONSTRAINT `id_barang#tpesanan1-tbarang` FOREIGN KEY (`id_barang`) REFERENCES `tbarang` (`id_barang`) ON UPDATE CASCADE,
  CONSTRAINT `id_pesanan#tpesanan1-tpesanan` FOREIGN KEY (`id_pesanan`) REFERENCES `tpesanan` (`id_pesanan`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table joyday.tpropinsi
CREATE TABLE IF NOT EXISTS `tpropinsi` (
  `id_propinsi` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nama_propinsi` varchar(100) NOT NULL,
  `tanggal_buat` datetime NOT NULL DEFAULT current_timestamp(),
  `tanggal_update` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_propinsi`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table joyday.tuser
CREATE TABLE IF NOT EXISTS `tuser` (
  `id_user` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `kode_user` varchar(30) NOT NULL,
  `nama_user` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `pin` varchar(255) DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `keterangan` varchar(200) DEFAULT NULL,
  `id_level` tinyint(1) unsigned NOT NULL,
  `sta` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `alasan` varchar(200) DEFAULT NULL,
  `tanggal_buat` timestamp NOT NULL DEFAULT current_timestamp(),
  `tanggal_update` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `kode_user` (`kode_user`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `no_hp` (`no_hp`),
  UNIQUE KEY `email` (`email`),
  KEY `id_level#tuser-tuser1` (`id_level`),
  CONSTRAINT `id_level#tuser-tuser1` FOREIGN KEY (`id_level`) REFERENCES `tuser1` (`id_level`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table joyday.tuser1
CREATE TABLE IF NOT EXISTS `tuser1` (
  `id_level` tinyint(1) unsigned NOT NULL,
  `nama_level` varchar(50) NOT NULL,
  `keterangan` varchar(200) DEFAULT NULL,
  `tanggal_buat` datetime NOT NULL DEFAULT current_timestamp(),
  `tanggal_update` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_level`),
  UNIQUE KEY `nama` (`nama_level`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table joyday.tuser2
CREATE TABLE IF NOT EXISTS `tuser2` (
  `id_user` int(10) unsigned NOT NULL,
  `kode_akses` varchar(30) NOT NULL,
  `sta` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `tanggal_buat` datetime NOT NULL DEFAULT current_timestamp(),
  `tanggal_update` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_user`,`kode_akses`) USING BTREE,
  KEY `kode_akses#tuser2-thakakses` (`kode_akses`) USING BTREE,
  CONSTRAINT `id_user#tuser2-tuser` FOREIGN KEY (`id_user`) REFERENCES `tuser` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `kode_akses#tuser2-thakakses` FOREIGN KEY (`kode_akses`) REFERENCES `thakakses` (`kode_akses`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk procedure joyday.get_fk_origin
DELIMITER //
CREATE PROCEDURE `get_fk_origin`(
	IN `constraint_name1` VARCHAR(50)
)
BEGIN

SELECT TABLE_NAME, COLUMN_NAME, CONSTRAINT_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE REFERENCED_TABLE_SCHEMA = 'joyday' AND CONSTRAINT_NAME LIKE CONCAT('%', constraint_name1, '%');

END//
DELIMITER ;

-- membuang struktur untuk procedure joyday.reinsert_hakakses
DELIMITER //
CREATE PROCEDURE `reinsert_hakakses`(
	IN `id_user` INT(10) unsigned,
	IN `id_level` TINYINT(1) unsigned
)
BEGIN

IF id_level = 1 THEN
	INSERT INTO tuser2 (id_user, kode_akses, sta)
	SELECT id_user, kode_akses, IF(kode_akses REGEXP 'BM-I2|BK-I|BK-E|USR-I|AKS-I', 0, 1) AS sta
	FROM thakakses;
ELSEIF id_level = 2 THEN 
	INSERT INTO tuser2 (id_user, kode_akses, sta)
	SELECT id_user, kode_akses, IF((kode_akses REGEXP '^BM|BK' AND kode_akses NOT REGEXP 'I1|-D|V1|VD1|R1') OR kode_akses IN ('ASS-V', 'ASS-R', 'USR-E'), 1, 0) AS sta
	FROM thakakses;
ELSEIF id_level = 3 THEN
	INSERT INTO tuser2 (id_user, kode_akses)
	SELECT id_user, kode_akses FROM thakakses;
END IF;

END//
DELIMITER ;

-- membuang struktur untuk procedure joyday.save_asset
DELIMITER //
CREATE PROCEDURE `save_asset`(
	INOUT `id_asset1` INT(10) unsigned,
	IN `id_barang1` INT(10) unsigned,
	IN `qr_code1` VARCHAR(30),
	IN `serial_number1` VARCHAR(30),
	IN `tanggal_akuisisi_asset1` DATE,
	IN `no_surat_kontrak1` VARCHAR(30),
	IN `tanggal_berakhir_kontrak1` DATE,
	IN `id_kepemilikan1` TINYINT(1) unsigned,
	IN `keterangan1` VARCHAR(200),
	IN `id_gudang1` TINYINT(3) unsigned,
	IN `id_pelanggan1` INT(10) unsigned,
	IN `sta1` TINYINT(1) unsigned,
	IN `alasan1` VARCHAR(200)
)
BEGIN

IF (id_asset1 IS NULL OR NOT EXISTS(SELECT id_asset FROM tasset WHERE id_asset = id_asset1)) THEN
	INSERT INTO tasset (id_barang, qr_code, serial_number, tanggal_akuisisi_asset, no_surat_kontrak, tanggal_berakhir_kontrak, id_kepemilikan, keterangan, id_gudang, id_pelanggan, sta, alasan) 
	VALUES (id_barang1, qr_code1, serial_number1, tanggal_akuisisi_asset1, no_surat_kontrak1, tanggal_berakhir_kontrak1, id_kepemilikan1, keterangan1, id_gudang1, id_pelanggan1, sta1, alasan1);
	SET id_asset1 = LAST_INSERT_ID();
ELSE
	UPDATE tasset SET
	qr_code = qr_code1,
	serial_number = serial_number1,
	tanggal_akuisisi_asset = tanggal_akuisisi_asset1,
	no_surat_kontrak = no_surat_kontrak1,
	tanggal_berakhir_kontrak = tanggal_berakhir_kontrak1,
	id_kepemilikan = id_kepemilikan1,
	keterangan = keterangan1,
	sta = sta1,
	alasan = alasan1
	WHERE id_asset = id_asset1;
END IF;

END//
DELIMITER ;

-- membuang struktur untuk procedure joyday.save_barang
DELIMITER //
CREATE PROCEDURE `save_barang`(
	IN `id_barang1` INT(10) unsigned,
	IN `kode_barang1` VARCHAR(30),
	IN `nama_barang1` VARCHAR(100),
	IN `id_brand1` INT(10) unsigned,
	IN `nama_brand1` VARCHAR(50),
	IN `id_tipe1` INT(10) unsigned,
	IN `nama_tipe1` VARCHAR(50),
	IN `ukuran1` INT,
	IN `keterangan1` VARCHAR(200)
)
BEGIN

IF (id_brand1 IS NULL OR NOT EXISTS (SELECT id_brand FROM tbarang1 WHERE id_brand = id_brand1)) THEN
	IF nama_brand1 IS NOT NULL THEN
		INSERT INTO tbarang1 (nama_brand) VALUES (nama_brand1);
		SET id_brand1 = LAST_INSERT_ID();
	END IF;
ELSE 
	UPDATE tbarang1 SET nama_brand = nama_brand1 WHERE id_brand = id_brand1;
END IF;

IF (id_tipe1 IS NULL OR NOT EXISTS (SELECT id_tipe FROM tbarang2 WHERE id_tipe = id_tipe1)) THEN
	INSERT INTO tbarang2 (nama_tipe) VALUES (nama_tipe1);
	SET id_tipe1 = LAST_INSERT_ID();
ELSE 
	UPDATE tbarang2 SET nama_tipe = nama_tipe1 WHERE id_tipe = id_tipe1;
END IF;

IF (id_barang1 IS NULL OR NOT EXISTS (SELECT id_barang FROM tbarang WHERE id_barang = id_barang1)) THEN
	INSERT INTO tbarang (kode_barang, nama_barang, id_brand, id_tipe, ukuran, keterangan)
	VALUES (kode_barang1, nama_barang1, id_brand1, id_tipe1, ukuran1, keterangan1);
ELSE
	UPDATE tbarang SET 
	nama_barang = nama_barang1,
	id_brand = id_brand1,
	id_tipe = id_tipe1,
	keterangan = keterangan1
	WHERE id_barang = id_barang1;
END IF;

END//
DELIMITER ;

-- membuang struktur untuk procedure joyday.save_barang_keluar
DELIMITER //
CREATE PROCEDURE `save_barang_keluar`(
	INOUT `id_barang_keluar1` INT(10) unsigned,
	IN `no_keluar1` VARCHAR(30),
	IN `id_pengurus1` INT(10) unsigned,
	IN `id_gudang1` TINYINT(3) unsigned,
	IN `keterangan1` VARCHAR(200)
)
BEGIN

IF (id_barang_keluar1 IS NULL OR NOT EXISTS (SELECT id_barang_keluar FROM tbarang_keluar WHERE id_barang_keluar = id_barang_keluar1)) THEN
	INSERT INTO tbarang_keluar (id_pengurus, id_gudang, keterangan)
	VALUES (id_pengurus1, id_gudang1, keterangan1);
	SET id_barang_keluar1 = LAST_INSERT_ID();
ELSE
	UPDATE tbarang_keluar SET
	keterangan = keterangan1
	WHERE id_barang_keluar = id_barang_keluar1;
END IF;

END//
DELIMITER ;

-- membuang struktur untuk procedure joyday.save_barang_masuk
DELIMITER //
CREATE PROCEDURE `save_barang_masuk`(
	INOUT `id_barang_masuk1` INT(10) unsigned,
	IN `tipe1` INT,
	IN `no_masuk1` VARCHAR(30),
	IN `id_penerima1` INT(10) unsigned,
	IN `no_faktur1` VARCHAR(30),
	IN `dari_id_pesanan1` INT(10) unsigned,
	IN `ke_id_gudang1` TINYINT(3) unsigned,
	IN `ke_id_agen1` INT,
	IN `keterangan1` VARCHAR(200)
)
BEGIN

IF (id_barang_masuk1 IS NULL OR NOT EXISTS (SELECT id_barang_masuk FROM tbarang_masuk WHERE id_barang_masuk = id_barang_masuk1)) THEN
	INSERT INTO tbarang_masuk (tipe, id_penerima, no_faktur, dari_id_pesanan, ke_id_gudang, ke_id_agen, keterangan)
	VALUES (tipe1, id_penerima1, no_faktur1, dari_id_pesanan1, ke_id_gudang1, ke_id_agen1, keterangan1);
	SET id_barang_masuk1 = LAST_INSERT_ID();
ELSE
	IF tipe1 IN (0, 2) THEN
		UPDATE tbarang_masuk SET
		no_faktur = no_faktur1,
		dari_id_pesanan = dari_id_pesanan1,
		ke_id_agen = ke_id_agen1,
		keterangan = keterangan1
		WHERE id_barang_masuk = id_barang_masuk1;
	END IF;
	
	IF tipe1 = 1 THEN
		UPDATE tbarang_masuk SET
		keterangan = keterangan1
		WHERE id_barang_masuk = id_barang_masuk1;
	END IF;
END IF;

END//
DELIMITER ;

-- membuang struktur untuk procedure joyday.save_distributor
DELIMITER //
CREATE PROCEDURE `save_distributor`(
	IN `id_distributor1` INT(10) unsigned,
	IN `kode_distributor1` VARCHAR(30),
	IN `nama_distributor1` VARCHAR(100),
	IN `alamat1` VARCHAR(200),
	IN `no_hp1` VARCHAR(20),
	IN `email1` VARCHAR(50),
	IN `keterangan1` VARCHAR(200)
)
BEGIN

IF (id_distributor1 IS NULL OR NOT EXISTS (SELECT id_distributor FROM tdistributor WHERE id_distributor = id_distributor1)) THEN
	INSERT INTO tdistributor (kode_distributor, nama_distributor, alamat, no_hp, email, keterangan)
	VALUES (kode_distributor1, nama_distributor1, alamat1, no_hp1, email1, keterangan1);
ELSE
	UPDATE tdistributor SET
	nama_distributor = nama_distributor1,
	alamat = alamat1,
	no_hp = no_hp1,
	email = email1,
	keterangan = keterangan1
	WHERE id_distributor = id_distributor1;
END IF;

END//
DELIMITER ;

-- membuang struktur untuk procedure joyday.save_gudang
DELIMITER //
CREATE PROCEDURE `save_gudang`(
	IN `id_gudang1` TINYINT(3) unsigned,
	IN `id_kepala_gudang1` INT(10) unsigned,
	IN `kode_gudang1` VARCHAR(30),
	IN `nama_gudang1` VARCHAR(100),
	IN `keterangan1` VARCHAR(200)
)
BEGIN

IF (id_gudang1 IS NULL OR NOT EXISTS (SELECT id_gudang FROM tgudang WHERE id_gudang = id_gudang1)) THEN
	INSERT INTO tgudang (id_kepala_gudang, kode_gudang, nama_gudang, keterangan)
	VALUES (id_kepala_gudang1, kode_gudang1, nama_gudang1, keterangan1);
ELSE
	UPDATE tgudang SET
	id_kepala_gudang = id_kepala_gudang1,
	nama_gudang = nama_gudang1,
	keterangan = keterangan1
	WHERE id_gudang = id_gudang1;
END IF;

END//
DELIMITER ;

-- membuang struktur untuk procedure joyday.save_mutasi
DELIMITER //
CREATE PROCEDURE `save_mutasi`(
	INOUT `id_mutasi1` INT(10) unsigned,
	IN `no_mutasi1` VARCHAR(30),
	IN `id_user1` INT(10) unsigned,
	IN `dari_id_pelanggan1` INT(10) unsigned,
	IN `keterangan1` VARCHAR(200)
)
BEGIN

IF (id_mutasi1 IS NULL OR NOT EXISTS(SELECT id_mutasi FROM tmutasi WHERE id_mutasi = id_mutasi1)) THEN
	INSERT INTO tmutasi (id_user, dari_id_pelanggan, keterangan)
	VALUES (id_user1, dari_id_pelanggan1, keterangan1);
	SET id_mutasi1 = LAST_INSERT_ID();
ELSE 
	UPDATE tmutasi SET
	dari_id_pelanggan = dari_id_pelanggan1,
	keterangan = keterangan1
	WHERE id_mutasi = id_mutasi1;
END IF;

END//
DELIMITER ;

-- membuang struktur untuk procedure joyday.save_pelanggan
DELIMITER //
CREATE PROCEDURE `save_pelanggan`(
	IN `id_pelanggan1` INT(10) unsigned,
	IN `id_agen1` INT(10) unsigned,
	IN `kode_pelanggan1` VARCHAR(30),
	IN `nama_pelanggan1` VARCHAR(100),
	IN `no_identitas1` VARCHAR(20),
	IN `no_hp11` VARCHAR(20),
	IN `no_hp21` VARCHAR(20),
	IN `email1` VARCHAR(50),
	IN `id_propinsi1` INT(10) unsigned,
	IN `id_kabupaten1` INT(10) unsigned,
	IN `id_kecamatan1` INT(10) unsigned,
	IN `id_kelurahan1` INT(10) unsigned,
	IN `alamat1` VARCHAR(200),
	IN `kode_pos1` VARCHAR(10),
	IN `keterangan1` VARCHAR(200),
	IN `daya_listrik1` INT,
	IN `latitude1` VARCHAR(10),
	IN `longitude1` VARCHAR(10),
	IN `nama_kerabat1` VARCHAR(100),
	IN `no_identitas_kerabat1` VARCHAR(20),
	IN `no_hp_kerabat1` VARCHAR(20),
	IN `alamat_kerabat1` VARCHAR(200),
	IN `hubungan1` VARCHAR(50),
	IN `id_level1` TINYINT(1) unsigned
)
BEGIN

IF (id_pelanggan1 IS NULL OR NOT EXISTS(SELECT id_pelanggan FROM tpelanggan WHERE id_pelanggan = id_pelanggan1)) THEN
	INSERT INTO tpelanggan (
		id_agen, kode_pelanggan, nama_pelanggan, no_identitas, no_hp1,
		no_hp2, email, id_propinsi, id_kabupaten, id_kecamatan,
		id_kelurahan, alamat, kode_pos, keterangan, daya_listrik,
		latitude, longitude, nama_kerabat, no_identitas_kerabat, no_hp_kerabat,
		alamat_kerabat, hubungan, id_level
	)
	VALUES (
		id_agen1, kode_pelanggan1, nama_pelanggan1, no_identitas1, no_hp11,
		no_hp21, email1, id_propinsi1, id_kabupaten1, id_kecamatan1,
		id_kelurahan1, alamat1, kode_pos1, keterangan1, daya_listrik1,
		latitude1, longitude1, nama_kerabat1, no_identitas_kerabat1, no_hp_kerabat1,
		alamat_kerabat1, hubungan1, id_level1
	);
ELSE 
	UPDATE tpelanggan SET
	id_agen = id_agen1,
	nama_pelanggan = nama_pelanggan1,
	no_identitas = no_identitas1,
	no_hp1 = no_hp11,
	no_hp2 = no_hp21,
	
	email = email1,
	id_propinsi = id_propinsi1,
	id_kabupaten = id_kabupaten1,
	id_kecamatan = id_kecamatan1,
	id_kelurahan = id_kelurahan1,
	
	alamat = alamat1,
	kode_pos = kode_pos1,
	keterangan = keterangan1,
	daya_listrik = daya_listrik1,
	latitude = latitude1,
	
	longitude = longitude1,
	nama_kerabat = nama_kerabat1,
	no_identitas_kerabat = no_identitas_kerabat1,
	no_hp_kerabat = no_hp_kerabat1,
	alamat_kerabat = alamat_kerabat1,
	
	hubungan = hubungan1,
	id_level = id_level1
	WHERE id_pelanggan = id_pelanggan1;
END IF;

END//
DELIMITER ;

-- membuang struktur untuk procedure joyday.save_pesanan
DELIMITER //
CREATE PROCEDURE `save_pesanan`(
	INOUT `id_pesanan1` INT(10) unsigned,
	IN `no_po1` VARCHAR(30),
	IN `id_distributor1` INT(10) unsigned,
	IN `id_pemesan1` INT(10) unsigned,
	IN `keterangan1` VARCHAR(200)
)
BEGIN
	
IF (id_pesanan1 IS NULL OR NOT EXISTS(SELECT id_pesanan FROM tpesanan WHERE id_pesanan = id_pesanan1)) THEN
	INSERT INTO tpesanan (no_po, id_distributor, id_pemesan, keterangan)
	VALUES (no_po1, id_distributor1, id_pemesan1, keterangan1);
	SET id_pesanan1 = LAST_INSERT_ID();
ELSE
	UPDATE tpesanan SET 
	no_po = no_po1,
	id_distributor = id_distributor1,
	keterangan = keterangan1,
	tanggal_update = CURRENT_TIMESTAMP()
	WHERE id_pesanan = id_pesanan1;
END IF;	
	
END//
DELIMITER ;

-- membuang struktur untuk procedure joyday.save_test
DELIMITER //
CREATE PROCEDURE `save_test`(
	INOUT `p1` VARCHAR(50),
	OUT `p2` VARCHAR(50)
)
BEGIN

	INSERT INTO test (val, qty)
	VALUES ('b', 100);
	SET p1 = LAST_INSERT_ID();
	
	
	INSERT INTO test (val, qty)
	VALUES ('b', 100);
	SET p2 = LAST_INSERT_ID();
	
END//
DELIMITER ;

-- membuang struktur untuk procedure joyday.save_user
DELIMITER //
CREATE PROCEDURE `save_user`(
	IN `id_user1` INT(10) unsigned,
	IN `nama_user1` VARCHAR(100),
	IN `username1` VARCHAR(50),
	IN `password1` VARCHAR(255),
	IN `pin1` VARCHAR(255),
	IN `no_hp1` VARCHAR(20),
	IN `email1` VARCHAR(50),
	IN `keterangan1` VARCHAR(200),
	IN `id_level1` TINYINT(1)
)
BEGIN

IF (id_user1 IS NULL OR NOT EXISTS(SELECT id_user FROM tuser WHERE id_user = id_user1)) THEN 
	INSERT INTO tuser(nama_user, username, password, pin, no_hp, email, keterangan, id_level)
	VALUES (nama_user1, username1, password1, pin1, no_hp1, email1, keterangan1, id_level1);
ELSE
	UPDATE tuser SET 
	nama_user = nama_user1,
	username = username1,
	password = IFNULL(password1, password),
	pin = pin1,
	no_hp = no_hp1,
	email = email1,
	keterangan = keterangan1
	WHERE id_user = id_user1;
END IF;

END//
DELIMITER ;

-- membuang struktur untuk function joyday.format_kode
DELIMITER //
CREATE FUNCTION `format_kode`(`prefix` VARCHAR(5),
	`id` INT(10) unsigned,
	`len` TINYINT(1) unsigned
) RETURNS varchar(30) CHARSET utf8
    DETERMINISTIC
BEGIN

SET @kode = CONCAT(prefix, '-', IF(LENGTH(id) > len, id, RIGHT(CONCAT(REPEAT('0', len), id), len)));

RETURN @kode;

END//
DELIMITER ;

-- membuang struktur untuk function joyday.format_tanggal
DELIMITER //
CREATE FUNCTION `format_tanggal`(`tanggal` VARCHAR(10)
) RETURNS date
    DETERMINISTIC
BEGIN

SET @split = MID(tanggal, REGEXP_INSTR(tanggal, '[-/]'), 1);
SET @tgl = SUBSTRING_INDEX(tanggal, @split, 1);
SET @bln = SUBSTRING_INDEX(SUBSTRING_INDEX(tanggal, @split, 2), @split, -1);
SET @thn = SUBSTRING_INDEX(tanggal, @split, -1);
RETURN CONCAT_WS(@split, @thn, @bln, @tgl);

END//
DELIMITER ;

-- membuang struktur untuk function joyday.get_abbreviation
DELIMITER //
CREATE FUNCTION `get_abbreviation`(`nama` VARCHAR(50)
) RETURNS varchar(3) CHARSET utf8
    DETERMINISTIC
BEGIN

DECLARE abbrev VARCHAR(3);
DECLARE len INT;

SET len = LENGTH(nama) - LENGTH(REPLACE(nama, ' ', ''));
IF len > 1 THEN
	SET abbrev = LEFT(SUBSTRING_INDEX(nama, ' ', 1), 1);
	SET abbrev = CONCAT(abbrev, LEFT(SUBSTRING_INDEX(SUBSTRING_INDEX(nama, ' ', 2), ' ', -1), 1));
	SET abbrev = CONCAT(abbrev, LEFT(SUBSTRING_INDEX(SUBSTRING_INDEX(nama, ' ', 3), ' ', -1), 1));
ELSEIF len = 1 THEN
	SET abbrev = LEFT(SUBSTRING_INDEX(nama, ' ', 1), 1);
	SET abbrev = CONCAT(abbrev, LEFT(SUBSTRING_INDEX(nama, ' ', -1), 1));
ELSE
	SET abbrev = LEFT(nama, 3);
END IF;
RETURN UPPER(abbrev);

END//
DELIMITER ;

-- membuang struktur untuk trigger joyday.tasset_after_insert
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `tasset_after_insert` AFTER INSERT ON `tasset` FOR EACH ROW BEGIN

INSERT INTO tlokasi_awal_asset(id_asset, id_gudang, id_pelanggan)
VALUES(NEW.id_asset, NEW.id_gudang, NEW.id_pelanggan);

END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- membuang struktur untuk trigger joyday.tbarang_before_insert
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `tbarang_before_insert` BEFORE INSERT ON `tbarang` FOR EACH ROW BEGIN

IF (NEW.kode_barang IS NULL OR NEW.kode_barang = '') THEN
	SET NEW.kode_barang = format_kode('B', (SELECT IFNULL(MAX(SUBSTRING_INDEX(kode_barang, '-', -1)) + 1, 1) FROM tbarang), 5);
END IF;

END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- membuang struktur untuk trigger joyday.tbarang_before_update
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `tbarang_before_update` BEFORE UPDATE ON `tbarang` FOR EACH ROW BEGIN

IF NEW.kode_barang IS NULL OR NEW.kode_barang = '' THEN
	SET NEW.kode_barang = OLD.kode_barang;
END IF;

END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- membuang struktur untuk trigger joyday.tbarang_keluar_before_insert
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `tbarang_keluar_before_insert` BEFORE INSERT ON `tbarang_keluar` FOR EACH ROW BEGIN

IF (NEW.no_keluar IS NULL OR NEW.no_keluar = '') THEN
	SET NEW.no_keluar = format_kode('BK', (SELECT IFNULL(MAX(SUBSTRING_INDEX(no_keluar, '-', -1)) + 1, 1) FROM tbarang_keluar), 5);
END IF;

END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- membuang struktur untuk trigger joyday.tbarang_keluar_before_update
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `tbarang_keluar_before_update` BEFORE UPDATE ON `tbarang_keluar` FOR EACH ROW BEGIN

IF NEW.no_keluar IS NULL OR NEW.no_keluar = '' THEN
	SET NEW.no_keluar = OLD.no_keluar;
END IF;

END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- membuang struktur untuk trigger joyday.tbarang_masuk1_after_insert
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `tbarang_masuk1_after_insert` AFTER INSERT ON `tbarang_masuk1` FOR EACH ROW BEGIN
	
	IF (SELECT tipe FROM tbarang_masuk WHERE id_barang_masuk = NEW.id_barang_masuk) IN (0, 2) THEN
		IF (SELECT SUM(qty) FROM tpesanan1 WHERE id_pesanan = (SELECT dari_id_pesanan FROM tbarang_masuk WHERE id_barang_masuk = NEW.id_barang_masuk)) >
			(SELECT COUNT(*) FROM tbarang_masuk1 WHERE id_barang_masuk = NEW.id_barang_masuk) THEN
			UPDATE tbarang_masuk SET sta = 2 WHERE id_barang_masuk = NEW.id_barang_masuk;
		ELSE 
			UPDATE tbarang_masuk SET sta = 1 WHERE id_barang_masuk = NEW.id_barang_masuk;
		END IF;
	END IF;
	
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- membuang struktur untuk trigger joyday.tbarang_masuk_before_insert
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `tbarang_masuk_before_insert` BEFORE INSERT ON `tbarang_masuk` FOR EACH ROW BEGIN

IF (NEW.no_masuk IS NULL OR NEW.no_masuk = '') THEN
	SET NEW.no_masuk = format_kode(
	CASE NEW.tipe
		WHEN 0 THEN 'BMD'
		WHEN 1 THEN 'BMP'
		WHEN 2 THEN 'BMA'
	END, 
	(SELECT IFNULL(MAX(SUBSTRING_INDEX(no_masuk, '-', -1)) + 1, 1) FROM tbarang_masuk), 5);
END IF;

END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- membuang struktur untuk trigger joyday.tbarang_masuk_before_update
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `tbarang_masuk_before_update` BEFORE UPDATE ON `tbarang_masuk` FOR EACH ROW BEGIN

IF (NEW.no_masuk IS NULL OR NEW.no_masuk = '') THEN
	SET NEW.no_masuk = format_kode(
	CASE NEW.tipe
		WHEN 0 THEN 'BMD'
		WHEN 1 THEN 'BMP'
		WHEN 2 THEN 'BMA'
	END, 
	NEW.id_barang_masuk, 5);
END IF;

END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- membuang struktur untuk trigger joyday.tdistributor_before_insert
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `tdistributor_before_insert` BEFORE INSERT ON `tdistributor` FOR EACH ROW BEGIN

IF (NEW.kode_distributor IS NULL OR NEW.kode_distributor = '') THEN
	SET NEW.kode_distributor = format_kode('DIS', (SELECT IFNULL(MAX(SUBSTRING_INDEX(kode_distributor, '-', -1)) + 1, 1) FROM tdistributor), 3);
END IF;

END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- membuang struktur untuk trigger joyday.tdistributor_before_update
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `tdistributor_before_update` BEFORE UPDATE ON `tdistributor` FOR EACH ROW BEGIN

IF NEW.kode_distributor IS NULL OR NEW.kode_distributor = '' THEN
	SET NEW.kode_distributor = OLD.kode_distributor;
END IF;

END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- membuang struktur untuk trigger joyday.tgudang_before_insert
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `tgudang_before_insert` BEFORE INSERT ON `tgudang` FOR EACH ROW BEGIN

IF (NEW.kode_gudang IS NULL OR NEW.kode_gudang = '') THEN
	SET NEW.kode_gudang = format_kode('GUD', (SELECT IFNULL(MAX(SUBSTRING_INDEX(kode_gudang, '-', -1)) + 1, 1) FROM tgudang), 3);
END IF;

END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- membuang struktur untuk trigger joyday.tgudang_before_update
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `tgudang_before_update` BEFORE UPDATE ON `tgudang` FOR EACH ROW BEGIN

IF NEW.kode_gudang IS NULL OR NEW.kode_gudang = '' THEN
	SET NEW.kode_gudang = OLD.kode_gudang;
END IF;

END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- membuang struktur untuk trigger joyday.thakakses_after_insert
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `thakakses_after_insert` AFTER INSERT ON `thakakses` FOR EACH ROW BEGIN

INSERT INTO tuser2 (id_user, kode_akses, sta)
SELECT id_user, NEW.kode_akses, CASE id_level WHEN 1 THEN 1 ELSE 0 END AS sta
FROM tuser;
	
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- membuang struktur untuk trigger joyday.tmutasi_before_insert
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `tmutasi_before_insert` BEFORE INSERT ON `tmutasi` FOR EACH ROW BEGIN

IF (NEW.no_mutasi IS NULL OR NEW.no_mutasi = '') THEN
	SET NEW.no_mutasi = format_kode('MUT', (SELECT IFNULL(MAX(SUBSTRING_INDEX(no_mutasi, '-', -1)) + 1, 1) FROM tmutasi), 5);
END IF;

END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- membuang struktur untuk trigger joyday.tmutasi_before_update
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `tmutasi_before_update` BEFORE UPDATE ON `tmutasi` FOR EACH ROW BEGIN

IF NEW.no_mutasi IS NULL OR NEW.no_mutasi = '' THEN
	SET NEW.no_mutasi = OLD.no_mutasi;
END IF;

END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- membuang struktur untuk trigger joyday.tpelanggan_before_insert
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `tpelanggan_before_insert` BEFORE INSERT ON `tpelanggan` FOR EACH ROW BEGIN

IF (NEW.kode_pelanggan IS NULL OR NEW.kode_pelanggan = '') THEN
	SET NEW.kode_pelanggan = format_kode(
		CASE NEW.id_level 
			WHEN 1 THEN "RET"
			WHEN 2 THEN "AGE"
			WHEN 3 THEN "SLS"
		END, 
		(SELECT IFNULL(MAX(SUBSTRING_INDEX(kode_pelanggan, '-', -1)) + 1, 1) FROM tpelanggan), 
		4
	);
END IF;

END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- membuang struktur untuk trigger joyday.tpelanggan_before_update
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `tpelanggan_before_update` BEFORE UPDATE ON `tpelanggan` FOR EACH ROW BEGIN

SET NEW.kode_pelanggan = format_kode(
	CASE NEW.id_level WHEN 1 THEN 'RET' WHEN 2 THEN 'AGE' WHEN 3 THEN 'SLS' END, 
	(SUBSTRING_INDEX(NEW.kode_pelanggan, '-', -1)), 
	4
);

END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- membuang struktur untuk trigger joyday.tpesanan_before_insert
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `tpesanan_before_insert` BEFORE INSERT ON `tpesanan` FOR EACH ROW BEGIN

IF (NEW.no_po IS NULL OR NEW.no_po = '') THEN
	SET NEW.no_po = format_kode('PO', (SELECT IFNULL(MAX(SUBSTRING_INDEX(no_po, '-', -1)) + 1, 1) FROM tpesanan), 5);
END IF;

END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- membuang struktur untuk trigger joyday.tpesanan_before_update
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `tpesanan_before_update` BEFORE UPDATE ON `tpesanan` FOR EACH ROW BEGIN

IF NEW.no_po IS NULL OR NEW.no_po = '' THEN
	SET NEW.no_po = OLD.no_po;
END IF;

END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- membuang struktur untuk trigger joyday.tuser_after_insert
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `tuser_after_insert` AFTER INSERT ON `tuser` FOR EACH ROW BEGIN

CALL reinsert_hakakses(NEW.id_user, NEW.id_level);

END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- membuang struktur untuk trigger joyday.tuser_after_update
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `tuser_after_update` AFTER UPDATE ON `tuser` FOR EACH ROW sp: BEGIN

IF OLD.id_level = NEW.id_level THEN
	LEAVE sp;
END IF;

DELETE FROM tuser2 WHERE id_user = NEW.id_user;

CALL reinsert_hakakses(NEW.id_user, NEW.id_level);

END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- membuang struktur untuk trigger joyday.tuser_before_insert
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `tuser_before_insert` BEFORE INSERT ON `tuser` FOR EACH ROW BEGIN

IF (NEW.kode_user IS NULL OR NEW.kode_user = '') THEN
	SET NEW.kode_user = format_kode(
		CASE NEW.id_level WHEN 1 THEN 'ADM' WHEN 2 THEN 'KGD' WHEN 3 THEN 'SLS' END, 
		(SELECT IFNULL(MAX(SUBSTRING_INDEX(kode_user, '-', -1)) + 1, 1) FROM tuser), 
		3
	);
END IF;

END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- membuang struktur untuk trigger joyday.tuser_before_update
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `tuser_before_update` BEFORE UPDATE ON `tuser` FOR EACH ROW BEGIN

SET NEW.kode_user = format_kode(
	CASE NEW.id_level WHEN 1 THEN 'ADM' WHEN 2 THEN 'KGD' WHEN 3 THEN 'SLS' END, 
	(SUBSTRING_INDEX(NEW.kode_user, '-', -1)), 
	3
);

END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
