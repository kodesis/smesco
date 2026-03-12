<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->library(['session', 'PHPExcel']);
		$this->load->helper(['string', 'date']);
		$this->load->model(['M_Auth', 'M_Booking', 'M_Partner']);

		if (!$this->session->userdata('is_logged_in')) {

			$this->session->set_userdata('last_page', current_url());

			$this->session->set_flashdata('message_name', '<div class="alert alert-danger alert-dismissible fade show" role="alert">
			You have to login first.
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			</div>');

			redirect('auth');
		}
	}

	public function index()
	{
		$role_id = $this->session->userdata('role_id');
		$partner_id = $this->session->userdata('partner_id');

		$saldo = $this->M_Partner->getSaldoAkhirPartner($partner_id)['saldo_akhir'];

		$data = [
			'title' => 'Dashboard',
			'segment' => 'dashboard',
			'pages' => 'pages/dashboard/v_dashboard',
			'booking' => $this->M_Booking->dashboard(),
			'drivers' => $this->M_Booking->list_driver(),
			'saldo' => ($role_id == '3') ? $saldo : 0,
		];

		$this->load->view('pages/index', $data);
	}

	public function reset($jenis)
	{
		$data = [
			"detail-awb" => ['search_resi', 'booking/list_detail'],
			"customer" => ['search_customer', 'customer'],
			"user" => ['search_user', 'setting/user'],
			"menu" => ['search_menu', 'setting/menu'],
			"agent" => ['search_agent', 'agent'],
			"partner" => ['search_partner', 'partner'],
			"pendaftaran" => ['search_pendaftaran', 'partner'],
			"booking" => ['search_booking', 'booking'],
			"pricelist" => ['search_pricelist', 'pricelist'],
		];

		if (isset($data[$jenis])) {
			$this->session->unset_userdata($data[$jenis][0]);
			redirect($data[$jenis][1]);
		}
	}

	public function showDepositSummary($partner_id)
	{
		$deposits = $this->M_Partner->getDepositSummary($partner_id); // Ambil data dari model

		$data = [
			'title' => 'Deposit',
			'deposits' => $deposits,
			'keyword' => '',
			'pages' => 'pages/partner/v_rekap_deposit',
			'segment' => 'dashboard'
		];

		$this->load->view('pages/index', $data);
	}

	public function downloadDepositExcel()
	{
		$from = $this->input->post('date_from');
		$to = $this->input->post('date_to');
		$partner_id = $this->session->userdata('partner_id');

		$deposits = $this->M_Partner->getDepositSummary($partner_id, $from, $to);

		if ($deposits) {
			require_once(APPPATH . 'libraries/PHPExcel/IOFactory.php');

			$excel = new PHPExcel();
			// Settingan awal fil excel
			$excel->getProperties()->setCreator('Smesco Express')
				->setLastModifiedBy('Smesco Express')
				->setTitle("Deposit history")
				->setSubject("Deposit")
				->setDescription("Deposit history from " . $from . ' to ' . $to)
				->setKeywords("Deposit history");

			// Buat sebuah variabel untuk menampung pengaturan style dari header tabel
			$style_col = [
				'font' => ['bold' => true],
				'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER],
				'borders' => ['top' => ['style'  => PHPExcel_Style_Border::BORDER_THIN], 'right' => ['style'  => PHPExcel_Style_Border::BORDER_THIN], 'bottom' => ['style'  => PHPExcel_Style_Border::BORDER_THIN], 'left' => ['style'  => PHPExcel_Style_Border::BORDER_THIN]]
			];

			$style_row = [
				'alignment' => ['vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER],
				'borders' => ['top' => ['style'  => PHPExcel_Style_Border::BORDER_THIN], 'right' => ['style'  => PHPExcel_Style_Border::BORDER_THIN], 'bottom' => ['style'  => PHPExcel_Style_Border::BORDER_THIN], 'left' => ['style'  => PHPExcel_Style_Border::BORDER_THIN]]
			];

			// bagian header
			$headers = [
				'A' => "No.",
				'B' => "No. Resi.",
				'C' => "Tanggal",
				'D' => "Asal",
				'E' => "Tujuan",
				'F' => "Komoditi",
				'G' => "Koli",
				'H' => "Berat timbang",
				'I' => "Volume",
				'J' => "Chargeable",
				'K' => "Nominal Resi",
				'L' => "Fee",
				'M' => "Topup",
				'N' => "Sisa saldo"
			];

			$sheet = $excel->setActiveSheetIndex(0);
			foreach ($headers as $columnID => $header) {
				$sheet->setCellValue($columnID . '1', $header);
				$sheet->getColumnDimension($columnID)->setAutoSize(true);
			}

			$no = 1;
			$numrow = 2;

			foreach ($deposits as $t) {
				if ($t->kode_topup) {
					$excel->setActiveSheetIndex(0)->setCellValue('A' . $numrow, $no);
					$excel->setActiveSheetIndex(0)->setCellValue('B' . $numrow, format_indo_non_hari($t->topup_date));
					$excel->setActiveSheetIndex(0)->mergeCells('B' . $numrow . ':E' . $numrow);
					$excel->setActiveSheetIndex(0)->setCellValue('F' . $numrow, 'Top Up Deposit');
					$excel->setActiveSheetIndex(0)->mergeCells('F' . $numrow . ':J' . $numrow);
					$excel->setActiveSheetIndex(0)->setCellValue('K' . $numrow, 0);
					$excel->setActiveSheetIndex(0)->setCellValue('L' . $numrow, 0);
					$excel->setActiveSheetIndex(0)->setCellValue('M' . $numrow, $t->nominal_topup);
					$excel->setActiveSheetIndex(0)->setCellValue('N' . $numrow, $t->sisa_saldo);

					$excel->getActiveSheet()->getStyle('B' . $numrow)->getAlignment()->setHorizontal('center');
					$excel->getActiveSheet()->getStyle('F' . $numrow)->getAlignment()->setHorizontal('center');
				} else {
					$excel->setActiveSheetIndex(0)->setCellValue('A' . $numrow, $no);
					$excel->setActiveSheetIndex(0)->setCellValue('B' . $numrow, $t->no_resi);
					$excel->setActiveSheetIndex(0)->setCellValue('C' . $numrow, format_indo_non_hari($t->tanggal_resi));
					$excel->setActiveSheetIndex(0)->setCellValue('D' . $numrow, $t->origin);
					$excel->setActiveSheetIndex(0)->setCellValue('E' . $numrow, $t->destination);
					$excel->setActiveSheetIndex(0)->setCellValue('F' . $numrow, $t->commodity);
					$excel->setActiveSheetIndex(0)->setCellValue('G' . $numrow, $t->qty);
					$excel->setActiveSheetIndex(0)->setCellValue('H' . $numrow, $t->berat_timbang);
					$excel->setActiveSheetIndex(0)->setCellValue('I' . $numrow, $t->volume);
					$excel->setActiveSheetIndex(0)->setCellValue('J' . $numrow, $t->chargeable);
					$excel->setActiveSheetIndex(0)->setCellValue('K' . $numrow, $t->nominal_resi);
					$excel->setActiveSheetIndex(0)->setCellValue('L' . $numrow, $t->usage_saldo);
					$excel->setActiveSheetIndex(0)->setCellValue('M' . $numrow, 0);
					$excel->setActiveSheetIndex(0)->setCellValue('N' . $numrow, $t->sisa_saldo);
				}

				// foreach (range('A', 'N') as $columnID) {
				// 	$excel->getActiveSheet()->getStyle($columnID . $numrow)->applyFromArray($style_row);
				// }

				$no++; // Tambah 1 setiap kali looping
				$numrow++; // Tambah 1 setiap kali looping
			}

			$excel->getActiveSheet()->getStyle('A1:N1')->applyFromArray($style_col);


			// Redirect output to a client’s web browser (Excel5)
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="Rekap deposit from ' . $from . ' to ' . $to . '.xls"');
			header('Cache-Control: max-age=0');
			// If you're serving to IE 9, then the following may be needed
			header('Cache-Control: max-age=1');

			// If you're serving to IE over SSL, then the following may be needed
			header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
			header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
			header('Pragma: public'); // HTTP/1.0

			$objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
			$objWriter->save('php://output');

			redirect($_HTTP('SERVER_REFERER'));
		} else {

			$this->session->set_flashdata('message_error', 'There is no data between ' . $from . ' and ' . $to);
		}

		redirect('dashboard');
	}
}
