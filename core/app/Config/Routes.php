<?php namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php'))
{
	require SYSTEMPATH . 'Config/Routes.php';
}

/**
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

/**
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->add('/', 'Home::index');

$routes->group("authentication", function($routes) {
	$routes->add("check_privileges", "Authentication::check_privileges");
	$routes->add("check_session", "Authentication::check_session");
	$routes->add("login", "Authentication::login");
});

$routes->add("menu/get_menu", "Menu::get_menu");

$routes->group("master", function($routes) {
	$routes->group("barang", function($routes) {
		$routes->add("fetch", "master/Barang::fetch");
		$routes->add("save", "master/Barang::save");
	});

	$routes->group("asset", function($routes) {
		$routes->add("fetch", "master/Asset::fetch");
		$routes->add("save", "master/Asset::save");
	});

	$routes->group("pelanggan", function($routes) {
		$routes->add("fetch", "master/Pelanggan::fetch");
		$routes->add("save", "master/Pelanggan::save");
	});

	$routes->group("distributor", function($routes) {
		$routes->add("fetch", "master/Distributor::fetch");
		$routes->add("save", "master/Distributor::save");
	});

	$routes->group("gudang", function($routes) {
		$routes->add("fetch", "master/Gudang::fetch");
		$routes->add("save", "master/Gudang::save");
	});
});

$routes->group("input", function($routes){
	$routes->group("pesanan", function($routes) {
		$routes->add("fetch", "input/Pesanan::fetch");
		$routes->add("save", "input/Pesanan::save");
	});

	$routes->group("barang_masuk", function($routes) {
		$routes->add("fetch", "input/Barang_Masuk::fetch");
		$routes->add("save", "input/Barang_Masuk::save");
	});

	$routes->group("barang_keluar", function($routes) {
		$routes->add("fetch", "input/Barang_Keluar::fetch");
		$routes->add("save", "input/Barang_Keluar::save");
	});

	$routes->group("mutasi", function($routes) {
		$routes->add("fetch", "input/Mutasi::fetch");
		$routes->add("save", "input/Mutasi::save");
	});
});

$routes->group("daftar", function($routes) {
	$routes->group("barang", function($routes) {
		$routes->add("delete", "daftar/Barang::delete");
		$routes->add("fetch", "daftar/Barang::fetch");
	});
	
	$routes->group("asset", function($routes) {
		$routes->add("delete", "daftar/Asset::delete");
		$routes->add("fetch", "daftar/Asset::fetch");
	});
	
	$routes->group("pelanggan", function($routes) {
		$routes->add("delete", "daftar/Pelanggan::delete");
		$routes->add("fetch", "daftar/Pelanggan::fetch");
	});
	
	$routes->group("distributor", function($routes) {
		$routes->add("delete", "daftar/Distributor::delete");
		$routes->add("fetch", "daftar/Distributor::fetch");
	});
	
	$routes->group("gudang", function($routes) {
		$routes->add("delete", "daftar/Gudang::delete");
		$routes->add("fetch", "daftar/Gudang::fetch");
	});
});

$routes->group("tampil", function($routes) {
	$routes->group("pesanan", function($routes) {
		$routes->add("delete", "tampil/Pesanan::delete");
		$routes->add("fetch", "tampil/Pesanan::fetch");
	});
	
	$routes->group("barang_masuk", function($routes) {
		$routes->add("delete", "tampil/Barang_Masuk::delete");
		$routes->add("fetch", "tampil/Barang_Masuk::fetch");
	});

	$routes->group("barang_keluar", function($routes) {
		$routes->add("delete", "tampil/Barang_Keluar::delete");
		$routes->add("fetch", "tampil/Barang_Keluar::fetch");
	});
	
	$routes->group("mutasi", function($routes) {
		$routes->add("delete", "tampil/Mutasi::delete");
		$routes->add("fetch", "tampil/Mutasi::fetch");
	});
});

$routes->group("laporan", function($routes) {
	$routes->group("barang_masuk", function($routes) {
		$routes->add("fetch", ("laporan/Barang_Masuk::fetch"));
	});

	$routes->group("barang_keluar", function($routes) {
		$routes->add("fetch", ("laporan/Barang_Keluar::fetch"));
	});
	
	$routes->group("mutasi", function($routes) {
		$routes->add("fetch", ("laporan/Mutasi::fetch"));
	});
	
	$routes->group("asset", function($routes) {
		$routes->add("fetch", ("laporan/Asset::fetch"));
	});

	$routes->group("penggantian_freezer", function($routes) {
		$routes->add("fetch", ("laporan/Penggantian_Freezer::fetch"));
	});
});

$routes->group("sistem", function($routes) {
	$routes->group("tambah_user", function($routes) {
		$routes->add("save", "sistem/Tambah_User::save");
	});

	$routes->group("edit_akun", function($routes) {
		$routes->add("fetch", "sistem/Edit_Akun::fetch");
		$routes->add("save", "sistem/Edit_Akun::save");
	});
});

/**
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php'))
{
	require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
