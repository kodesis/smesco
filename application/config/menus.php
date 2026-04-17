<?php
defined('BASEPATH') or exit('No direct script access allowed');

$config['menus'] = [

	'superadmin' => [
		['title' => 'Dashboard',       'url' => 'dashboard',       'segment' => 'dashboard',   'icon' => 'dashboard'],
		['title' => 'Manajemen User',   'url' => 'users',           'segment' => 'users',       'icon' => 'users'],
		['title' => 'Daftar Agen',      'url' => 'agents',          'segment' => 'agents',      'icon' => 'building'],

		// MENU BARU: Untuk mengelola akses pihak ketiga
		['title' => 'API Management',   'url' => 'api_management',  'segment' => 'api_management', 'icon' => 'api'],

		// ['title' => 'Semua Transaksi',  'url' => 'shipment',        'segment' => 'shipment',    'icon' => 'truck'],

		[
			'title' => 'Master Data',
			'url' => '#',
			'segment' => 'master',
			'icon' => 'database',
			'children' => [
				['title' => 'Kota Kargo (Hub)', 'url' => 'master/cities'], // Tabel cities untuk mapping agen
				['title' => 'Pricelist Global', 'url' => 'master/pricelist'],
				['title' => 'Layanan & Komoditas', 'url' => 'master/services'],
			]
		],

		[
			'title' => 'Pengaturan',
			'url' => '#',
			'segment' => 'setting',
			'icon' => 'settings',
			'children' => [
				['title' => 'Roles & Permission',  'url' => 'setting/roles'],
				['title' => 'Konfigurasi Sistem',  'url' => 'setting/config'],
				// TAMBAHAN: Biar gampang pantau log dari menu
				['title' => 'Log Aktivitas User',  'url' => 'logs/activity'],
				['title' => 'Log Traffic API',     'url' => 'logs/api'],
			]
		],
	],

	'admin-kribo' => [
		['title' => 'Dashboard',     'url' => 'dashboard',       'segment' => 'dashboard',   'icon' => 'dashboard'],
		[
			'title' => 'Operasional',
			'url' => '#',
			'segment' => 'shipment',
			'icon' => 'package',
			'children' => [
				['title' => 'Semua Shipment', 'url' => 'shipment'],
				['title' => 'Manifest Pickup', 'url' => 'shipment/manifest_list'],
				['title' => 'Manifest AWB', 'url' => 'shipment/manifest'],
				['title' => 'Monitoring Kurir', 'url' => 'shipment/pickup_list'],
			]
		],
		[
			'title' => 'Master Data',
			'url' => '#',
			'segment' => 'master',
			'icon' => 'database',
			'children' => [
				['title' => 'Pricelist Rute', 'url' => 'master/pricelist'],
				['title' => 'Layanan & Komoditas', 'url' => 'master/services'],
			]
		],
		['title' => 'Laporan Global',  'url' => 'reports',         'segment' => 'reports',     'icon' => 'file-spreadsheet'],
	],

	'checker' => [
		['title' => 'Dashboard',     'url' => 'dashboard',       'segment' => 'dashboard',   'icon' => 'dashboard'],
		['title' => 'Scan Acceptance', 'url' => 'shipment/acceptance_scan', 'segment' => 'shipment', 'icon' => 'scan'], // Menu utama Checker
		// ['title' => 'Daftar Barang Masuk', 'url' => 'shipment', 'segment' => 'shipment', 'icon' => 'list-check'],
	],

	'driver' => [
		['title' => 'Dashboard',     'url' => 'dashboard',       'segment' => 'dashboard',   'icon' => 'dashboard'],
		['title' => 'Scan Pickup',    'url' => 'shipment/pickup_scan', 'segment' => 'shipment', 'icon' => 'truck-loading'], // Menu utama Driver
		// ['title' => 'Daftar Jemputan', 'url' => 'shipment?status=READY_TO_PICKUP', 'segment' => 'shipment', 'icon' => 'map-pin'],
	],

	'admin-mitra' => [
		['title' => 'Dashboard',     'url' => 'dashboard',       'segment' => 'dashboard',   'icon' => 'dashboard'],
		[
			'title' => 'Shipment',
			'url' => '#',
			'segment' => 'shipment',
			'icon' => 'box',
			'children' => [
				['title' => 'List Kiriman', 'url' => 'shipment', 'segment' => 'shipment', 'icon' => 'list'],
				['title' => 'Manifest Pickup', 'url' => 'shipment/manifest_list'],
			]
		],
		['title' => 'Manajemen Staff', 'url' => 'users',           'segment' => 'users',       'icon' => 'users'],
		['title' => 'Laporan Agen',   'url' => 'reports',         'segment' => 'reports',     'icon' => 'file-spreadsheet'],
	],

	'staff-mitra' => [
		['title' => 'Dashboard',     'url' => 'dashboard',       'segment' => 'dashboard',   'icon' => 'dashboard'],
		[
			'title' => 'Shipment',   
			'url' => '#',        
			'segment' => 'shipment',    
			'icon' => 'box',
			'children' => [
				['title' => 'List Kiriman', 'url' => 'shipment', 'segment' => 'shipment', 'icon' => 'list'],
				['title' => 'Buat Booking', 'url' => 'shipment/create', 'segment' => 'shipment', 'icon' => 'plus'],
				['title' => 'Manifest Pickup', 'url' => 'shipment/manifest_list'],
			]
		],
	],

	'finance-kribo' => [
		['title' => 'Dashboard',     'url' => 'dashboard',       'segment' => 'dashboard',   'icon' => 'dashboard'],
		['title' => 'Verifikasi',   'url' => 'finance/verifikasi', 'segment' => 'finance',     'icon' => 'message-check'],
		// ['title' => 'Rekonsiliasi',   'url' => 'finance/reconcile', 'segment' => 'finance',    'icon' => 'cash'],
	],
];
