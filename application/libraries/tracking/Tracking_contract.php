<?php
interface Tracking_contract
{
	/**
	 * Ambil tracking dari API vendor, return array yang sudah dinormalisasi.
	 * Format return per item:
	 * [
	 *   'status'     => 'PICKED_UP',   // status internal
	 *   'note'       => 'string',
	 *   'location'   => 'string',
	 *   'created_at' => 'Y-m-d H:i:s',
	 * ]
	 */
	public function fetch(string $connote): array;
}
