<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reports extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model(['M_Report', 'M_Shipment']);
	}

	public function index()
	{
		$sess = $this->session->userdata('user');
		$role = $sess['role_slug'];
		$scope = $sess['role_scope'];

		// Filter Default
		$filters = [
			'start'    => $this->input->get('start') ?: date('Y-m-01'),
			'end'      => $this->input->get('end') ?: date('Y-m-t'),
			'status'   => $this->input->get('status'),
			'agent_id' => ($scope === 'agent') ? $sess['agent_id'] : $this->input->get('agent_id')
		];

		$data = [
			'title'   => 'Laporan Transaksi',
			'results' => $this->M_Report->get_shipment_report($filters),
			'agents'  => ($scope === 'global') ? $this->db->get('agents')->result() : [],
			'filters' => $filters,
			'role'    => $role,
			// 'stats'   => $this->M_Shipment->get_kribo_stats($filters)
		];

		$this->render('app/pages/reports/index', $data);
	}

	public function export_excel()
	{
		$sess = $this->session->userdata('user');
		$role = $sess['role_slug'];
		$scope = $sess['role_scope'];

		$filters = [
			'start'    => $this->input->get('start'),
			'end'      => $this->input->get('end'),
			'agent_id' => ($scope === 'agent') ? $sess['agent_id'] : $this->input->get('agent_id'),
			'status'   => $this->input->get('status')
		];

		$results = $this->M_Report->get_shipment_report($filters);

		// 1. Load Library
		require_once FCPATH . 'vendor/autoload.php';
		$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		// 2. Styling Header
		$headerStyle = [
			'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
			'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '206BC4']],
			'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
		];

		// 3. Define Header Columns
		$columns = ['A' => 'No. Resi', 'B' => 'Tanggal', 'C' => 'Status', 'D' => 'Destinasi', 'E' => 'Berat (Kg)'];
		$currentCol = 'F';

		if (in_array($role, ['superadmin', 'finance-kribo', 'admin-kribo'])) {
			$columns[$currentCol++] = 'Cost Kribo';
		}
		if (in_array($role, ['superadmin', 'admin-kribo', 'admin-mitra'])) {
			$columns[$currentCol++] = 'Total Jual';
		}
		if (in_array($role, ['superadmin', 'finance-kribo', 'admin-kribo'])) {
			$columns[$currentCol++] = 'Margin Smesco';
		}

		// Set Header ke Sheet
		foreach ($columns as $col => $title) {
			$sheet->setCellValue($col . '1', $title);
			$sheet->getStyle($col . '1')->applyFromArray($headerStyle);
			$sheet->getColumnDimension($col)->setAutoSize(true);
		}

		// 4. Isi Data
		$row = 2;
		$t_weight = 0;
		$t_cost = 0;
		$t_sell = 0;
		$t_margin = 0;

		foreach ($results as $r) {
			$is_void = ($r->status === 'CANCELLED');
			$total_cost = $r->chargeable_weight * $r->cost_price;

			$sheet->setCellValue('A' . $row, $r->no_resi);
			$sheet->setCellValue('B' . $row, date('d/m/Y', strtotime($r->created_at)));
			$sheet->setCellValue('C' . $row, $r->status);
			$sheet->setCellValue('D' . $row, $r->destination);
			$sheet->setCellValue('E' . $row, $r->chargeable_weight);

			$dataCol = 'F';
			if (in_array($role, ['superadmin', 'finance-kribo', 'admin-kribo'])) {
				$val = $is_void ? 0 : $total_cost;
				$sheet->setCellValue($dataCol++ . $row, $val);
				if (!$is_void) $t_cost += $val;
			}
			if (in_array($role, ['superadmin', 'admin-kribo', 'admin-mitra'])) {
				$val = $is_void ? 0 : $r->total_amount;
				$sheet->setCellValue($dataCol++ . $row, $val);
				if (!$is_void) $t_sell += $val;
			}
			if (in_array($role, ['superadmin', 'finance-kribo', 'admin-kribo'])) {
				$val = $is_void ? 0 : $r->margin_amount;
				$sheet->setCellValue($dataCol++ . $row, $val);
				if (!$is_void) $t_margin += $val;
			}

			if (!$is_void) $t_weight += $r->chargeable_weight;
			$row++;
		}

		// 5. Tambah Baris Total
		$footerRow = $row;
		$sheet->setCellValue('A' . $footerRow, 'TOTAL AKTIF');
		$sheet->mergeCells("A$footerRow:D$footerRow");
		$sheet->getStyle("A$footerRow:$currentCol$footerRow")->getFont()->setBold(true);

		$sheet->setCellValue('E' . $footerRow, $t_weight);

		$footerCol = 'F';
		if (in_array($role, ['superadmin', 'finance-kribo', 'admin-kribo'])) $sheet->setCellValue($footerCol++ . $footerRow, $t_cost);
		if (in_array($role, ['superadmin', 'admin-kribo', 'admin-mitra'])) $sheet->setCellValue($footerCol++ . $footerRow, $t_sell);
		if (in_array($role, ['superadmin', 'finance-kribo', 'admin-kribo'])) $sheet->setCellValue($footerCol++ . $footerRow, $t_margin);

		// 6. Export ke Browser
		$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
		$filename = "Report_Smesco_Express_" . date('Ymd_His') . ".xlsx";

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="' . $filename . '"');
		header('Cache-Control: max-age=0');

		$writer->save('php://output');
		exit;
	}
}
