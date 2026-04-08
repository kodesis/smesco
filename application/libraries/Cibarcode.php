<?php

/**
 * Cibarcode — CodeIgniter Library
 * Generate Code 128 barcode sebagai PNG base64
 * Tidak memerlukan Composer, menggunakan PHP GD library
 *
 * Cara pakai di controller:
 *   $this->load->library('cibarcode');
 *   $base64 = $this->cibarcode->generate('NO-RESI-001');
 *   // hasilnya: "data:image/png;base64,iVBOR..."
 *
 * @author    generated for SMESCO project
 * @requires  PHP GD extension (php-gd)
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Cibarcode
{
	// Tinggi batang barcode dalam pixel
	private $bar_height = 60;

	// Lebar tiap unit bar (narrow bar) dalam pixel
	private $bar_width = 2;

	// Padding kiri-kanan dalam pixel
	private $padding = 10;

	// Warna foreground (batang) — RGB
	private $color_fg = [0, 0, 0];

	// Warna background — RGB
	private $color_bg = [255, 255, 255];

	// -------------------------------------------------------
	// Code 128B — encoding table
	// Index = nilai karakter (ASCII 32–127)
	// Value = pattern 11 bit (6 elemen: bar/space lebar 1-4)
	// -------------------------------------------------------
	private $code128_table = [
		// Setiap entry: [bar1, space1, bar2, space2, bar3, space3] dalam unit
		// Ini adalah pola lebar bar/space Code 128 Set B
		' '  => [2, 1, 2, 2, 2, 2],
		'!'  => [2, 2, 2, 1, 2, 2],
		'"'  => [2, 2, 2, 2, 2, 1],
		'#'  => [1, 2, 1, 2, 2, 3],
		'$'  => [1, 2, 1, 3, 2, 2],
		'%'  => [1, 3, 1, 2, 2, 2],
		'&'  => [1, 2, 2, 2, 1, 3],
		'\'' => [1, 2, 2, 3, 1, 2],
		'('  => [1, 3, 2, 2, 1, 2],
		')'  => [2, 2, 1, 2, 1, 3],
		'*'  => [2, 2, 1, 3, 1, 2],
		'+'  => [2, 3, 1, 2, 1, 2],
		','  => [1, 1, 2, 2, 3, 2],
		'-'  => [1, 2, 2, 1, 3, 2],
		'.'  => [1, 2, 2, 2, 3, 1],
		'/'  => [1, 1, 3, 2, 2, 2],
		'0'  => [1, 2, 3, 1, 2, 2],
		'1'  => [1, 2, 3, 2, 2, 1],
		'2'  => [2, 2, 3, 2, 1, 1],
		'3'  => [2, 2, 1, 1, 3, 2],
		'4'  => [2, 2, 1, 2, 3, 1],
		'5'  => [2, 1, 3, 2, 1, 2],
		'6'  => [2, 2, 3, 1, 1, 2],
		'7'  => [3, 1, 2, 1, 3, 1],
		'8'  => [3, 1, 1, 2, 2, 2],
		'9'  => [3, 2, 1, 1, 2, 2],
		':'  => [3, 2, 1, 2, 2, 1],
		';'  => [3, 1, 2, 2, 1, 2],
		'<'  => [3, 2, 2, 1, 1, 2],
		'='  => [3, 2, 2, 2, 1, 1],
		'>'  => [2, 1, 2, 1, 2, 3],
		'?'  => [2, 1, 2, 3, 2, 1],
		'@'  => [2, 3, 2, 1, 2, 1],
		'A'  => [1, 1, 1, 3, 2, 3],
		'B'  => [1, 3, 1, 1, 2, 3],
		'C'  => [1, 3, 1, 3, 2, 1],
		'D'  => [1, 1, 2, 3, 1, 3],
		'E'  => [1, 3, 2, 1, 1, 3],
		'F'  => [1, 3, 2, 3, 1, 1],
		'G'  => [2, 1, 1, 3, 1, 3],
		'H'  => [2, 3, 1, 1, 1, 3],
		'I'  => [2, 3, 1, 3, 1, 1],
		'J'  => [1, 1, 3, 1, 1, 3],
		'K'  => [1, 1, 3, 3, 1, 1],
		'L'  => [1, 3, 3, 1, 1, 1],
		'M'  => [3, 1, 3, 1, 1, 1],
		'N'  => [2, 1, 1, 1, 3, 3],
		'O'  => [2, 3, 1, 1, 1, 1], // partial — continued below
		// (tabel lebih lanjut di-handle via nilai numerik internal)
	];

	// -------------------------------------------------------
	// Tabel lengkap Code 128B sebagai array numerik
	// Index 0–106 sesuai spesifikasi Code 128
	// Format: string 6 digit (lebar bar/space bergantian)
	// -------------------------------------------------------
	private $patterns = [
		'212222',
		'222122',
		'222221',
		'121223',
		'121322',
		'131222',
		'122213',
		'122312',
		'132212',
		'221213',
		'221312',
		'231212',
		'112232',
		'122132',
		'122231',
		'113222',
		'123122',
		'123221',
		'223211',
		'221132',
		'221231',
		'213212',
		'223112',
		'312131',
		'311222',
		'321122',
		'321221',
		'312212',
		'322112',
		'322211',
		'212123',
		'212321',
		'232121',
		'111323',
		'131123',
		'131321',
		'112313',
		'132113',
		'132311',
		'211313',
		'231113',
		'231311',
		'112133',
		'112331',
		'132131',
		'113123',
		'113321',
		'133121',
		'313121',
		'211331',
		'231131',
		'213113',
		'213311',
		'213131',
		'311123',
		'311321',
		'331121',
		'312113',
		'312311',
		'332111',
		'314111',
		'221411',
		'431111',
		'111224',
		'111422',
		'121124',
		'121421',
		'141122',
		'141221',
		'112214',
		'112412',
		'122114',
		'122411',
		'142112',
		'142211',
		'241211',
		'221114',
		'213111',
		'241112',
		'134111',
		'111242',
		'121142',
		'121241',
		'114212',
		'124112',
		'124211',
		'411212',
		'421112',
		'421211',
		'212141',
		'214121',
		'412121',
		'111143',
		'111341',
		'131141',
		'114113',
		'114311',
		'411113',
		'411311',
		'113141',
		'114131',
		'311141',
		'411131',
		'211412',
		'211214',
		'211232',
		'2331112',
	];

	// ASCII value → Code 128B index (Set B: ASCII 32–127 = index 0–95)
	// Start B = index 104, Stop = index 106

	public function __construct($config = [])
	{
		$this->initialize($config);
	}

	public function initialize($config = [])
	{
		if (isset($config['bar_height']))  $this->bar_height = (int) $config['bar_height'];
		if (isset($config['bar_width']))   $this->bar_width  = (int) $config['bar_width'];
		if (isset($config['padding']))     $this->padding    = (int) $config['padding'];
		if (isset($config['color_fg']))    $this->color_fg   = $config['color_fg'];
		if (isset($config['color_bg']))    $this->color_bg   = $config['color_bg'];
	}

	/**
	 * Generate barcode Code 128B
	 *
	 * @param  string $data   Teks yang akan di-encode
	 * @return string         "data:image/png;base64,..." siap untuk src img
	 */
	public function generate($data)
	{
		// 1. Hitung checksum
		$checksum = 104; // nilai Start B
		foreach (str_split($data) as $i => $char) {
			$val = ord($char) - 32; // Set B: ASCII 32 = index 0
			$checksum += ($i + 1) * $val;
		}
		$checksum = $checksum % 103;

		// 2. Kumpulkan semua pola
		$all_patterns = [];
		$all_patterns[] = $this->patterns[104]; // Start B
		foreach (str_split($data) as $char) {
			$idx = ord($char) - 32;
			$all_patterns[] = $this->patterns[$idx];
		}
		$all_patterns[] = $this->patterns[$checksum]; // Checksum
		$all_patterns[] = $this->patterns[106];        // Stop

		// 3. Hitung total lebar
		$total_units = 0;
		foreach ($all_patterns as $pat) {
			for ($i = 0; $i < strlen($pat); $i++) {
				$total_units += (int)$pat[$i];
			}
		}
		$img_width = ($total_units * $this->bar_width) + ($this->padding * 2);
		$img_height = $this->bar_height;

		// 4. Buat image dengan GD
		$img = imagecreate($img_width, $img_height);

		$bg  = imagecolorallocate($img, $this->color_bg[0], $this->color_bg[1], $this->color_bg[2]);
		$fg  = imagecolorallocate($img, $this->color_fg[0], $this->color_fg[1], $this->color_fg[2]);

		imagefill($img, 0, 0, $bg);

		// 5. Gambar batang
		$x = $this->padding;
		foreach ($all_patterns as $pat) {
			$is_bar = true; // mulai dengan bar (hitam)
			for ($i = 0; $i < strlen($pat); $i++) {
				$unit_width = (int)$pat[$i] * $this->bar_width;
				if ($is_bar) {
					imagefilledrectangle($img, $x, 0, $x + $unit_width - 1, $img_height - 1, $fg);
				}
				$x += $unit_width;
				$is_bar = !$is_bar;
			}
		}

		// 6. Output sebagai base64
		ob_start();
		imagepng($img);
		$png_data = ob_get_clean();
		imagedestroy($img);

		return 'data:image/png;base64,' . base64_encode($png_data);
	}
}

/* end of file Cibarcode.php */
