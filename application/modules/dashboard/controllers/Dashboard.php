<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH . 'controllers/AppBackend.php');

class Dashboard extends AppBackend
{
	public $prefs;
	function __construct()
	{
		parent::__construct();

		$this->load->model(array(
			'NotificationModel',
			'DashboardModel',
			'AppMixModel',
			'AbsenModel',
			'MesinModel',
			'TarikDataModel',
		));

		$this->prefs = array(
			'start_day'    => 'senin',
			'month_type'   => 'long',
			'day_type'     => 'long',
			'show_next_prev' => TRUE,
			'next_prev_url'   => base_url('dashboard/index/'),
		);

		$this->prefs['template'] = array(
			'table_open'           		=> '<table class="calendar">',
			'heading_row_start' 		=> '<tr class="header_month">',
			'heading_previous_cell'		=> '<th><a href="{previous_url}"><i class="zmdi zmdi-caret-left-circle"></i></a></th>',
			'heading_title_cell'		=> '<th class="month_name" colspan="{colspan}"><a class="month_content">{heading}</a></th>',
			'heading_next_cell'			=> '<th><a href="{next_url}"><i class="zmdi zmdi-caret-right-circle"></i></a></th>',
			'week_row_start' 			=> '<tr class="header_day">',
			'cal_cell_start'       		=> '<td class="day">',
			'cal_cell_start_today' 		=> '<td class="today">',
			'cal_cell_content'			=> '<a class="content_fill_day" href="'.base_url('dashboard/detail?date=').'{content}" title="Click untuk lihat data absen tanggal {content}">{day}</a>',
			'cal_cell_content_today'	=> '<a class="content_fill_today" href="'.base_url('dashboard/detail?date=').'{content}" title="Click untuk lihat data absen hari ini"><strong>{day}</strong></a>',
			'cal_cell_no_content'		=> '<p class="no_content_fill_day" title="Data absen belum ada. click untuk tarik data">{day}</p>',
			'cal_cell_no_content_today'	=> '<a class="no_content_fill_today" title="Data absen belum ada. Click untuk tarik data hari ini"><strong>{day}</strong></a>'
		);
		
	}

	public function index($year = NULL , $month = NULL)
	{
		if(empty($year)||empty($month)){
			$year = date('Y');
			$month = date('m');
		}
		$data = array(
			'app' => $this->app(),
			'main_js' => $this->load_main_js('dashboard'),
			'page_title' => 'ٱلسَّلَامُ عَلَيْكُمْ‎',
			'calendar' => $this->getcalender($year , $month),
			'list_mesin' => $this->init_list($this->MesinModel->getAll(), 'ipadress','namamesin','status'),
			'page_subTitle' => 'Welcome to ' . $this->app()->app_name . ' v' . $this->app()->app_version,
		);

		$this->template->set('title', $data['app']->app_name, TRUE);
		$this->template->load_view('index', $data, TRUE);
		$this->template->render();
	}

	public function getcalender($year , $month)
	{
		$this->load->library('calendar',$this->prefs);
		$data = $this->get_calender_data($year,$month);
		return $this->calendar->generate($year , $month , $data);
	}

	public function get_calender_data($year , $month)
	{
		$startDate = date('Y-m-d', strtotime("$year-$month-1"));
        $endDate = date('Y-m-d', strtotime("$year-$month-1 +1 month"));
		$query = $this->db->select('DATE(tanggal_absen) AS absendate, COUNT(tanggal_absen) AS attendance_count')
							->from('attendancelog')
          					->where("tanggal_absen BETWEEN '$startDate' AND '$endDate'")
							->group_by('absendate')
							->order_by('absendate')
							->get();

		//echo $this->db->last_query();exit;
		$cal_data = array();
		foreach ($query->result() as $row) {
            $calendar_date = date("Y-m-j", strtotime($row->absendate));
			$cal_data[substr($calendar_date, 8,2)] = $row->absendate;
		}
		
		return $cal_data;
	}

	public function detail()
  	{
		//$agent = new Mobile_Detect;
		$ref = $this->input->get('date');
		$searchFilter = "";
		$status = "";
		$card = "";
		if (DateTime::createFromFormat('Y-m-d', $ref) !== false) {
			$dateTime = DateTime::createFromFormat('Y-m-d', $ref);
			$Day = $dateTime->format('D');
            $DayNumber = $dateTime->format('d');
            $monthNumber = $dateTime->format('m');
            $year = $dateTime->format('Y');
            $formattedDay = $this->get_day($Day);
            $formattedMonth = $this->get_month($monthNumber);
            $formattedDate = $formattedDay.', '.$DayNumber.' '.$formattedMonth . ' ' . $year;
			$searchFilter = "AND tanggal_absen::date='$ref'";
			$status = 'true';
			$card = "hari ".$formattedDate;
		}else if(DateTime::createFromFormat('Y-m', $ref) !== false){
			$dateTime = DateTime::createFromFormat('Y-m', $ref);
			$monthNumber = $dateTime->format('m');
			$year = $dateTime->format('Y');
			$formattedMonth = $this->get_month($monthNumber);
			$formattedDate = $formattedMonth . ' ' . $year;
			$startDate = date('Y-m-d', strtotime("$year-$monthNumber-1"));
        	$endDate = date('Y-m-d', strtotime("$year-$monthNumber-1 +1 month"));
			$searchFilter = "AND tanggal_absen BETWEEN '$startDate' AND '$endDate'";
			$status = 'false';
			$card = "Bulan ".$formattedDate;
		}else{
			show_404();
		}
			$data = array(
			'app' => $this->app(),
			'main_js' => $this->load_main_js('dashboard', false, array(
				'action_route' => 'detail',
				'key' => $ref,
				'searchFilter' => $searchFilter,
				'isDaily' => $status,
			)),
			'card_title' => 'Data absen '.$card,
			'controller' => $this,
			//'is_mobile' => $agent->isMobile(),
			'isDaily' => $status,
			'isAll' => 'false',
			'list_mesin' => $this->init_list($this->MesinModel->getAll(), 'ipadress','namamesin','status'),
			);
			//$this->template->set_template('sb_admin_partial');
			$this->template->set('title', $data['card_title'] . ' | ' . $data['app']->app_name, TRUE);
			$this->template->load_view('view', $data, TRUE);
			$this->template->render();
  	}

	public function ajax_get_all()
  	{
		$filter = $this->input->get('filter');
		$query = $this->AbsenModel->getQueryTime($filter);
		$response = $this->AppMixModel->getdata_dtAjax($query);
		echo json_encode($response);
  	}

	public function ajax_fetch_data() {
		$this->handle_ajax_request();
		$tanggal = $this->input->get('tanggal');
		$apiUrl = base_url('api/getData/');
		$response = file_get_contents($apiUrl);
		$data_api = json_decode($response, true);
		$list_mesin = $data_api['list_mesin'];
		$data['filteredData'] = [];
		$count = 0;
		$existRecord = 0;
		foreach ($list_mesin as $mesin){
			$datamesin = $mesin['ip'];
			$tarikdatarecord = array(
				'machine' => $datamesin,
				'start_date' => $tanggal,
				'end_date' => $tanggal,
			);
			if ($this->TarikDataModel->checkIPMachine($datamesin)) {
				$data['filteredData'] = array_merge(
					$data['filteredData'],
					$this->TarikDataModel->fetchDataFromMachine($mesin['ip'], $mesin['commkey'], $tanggal, $tanggal)
				);
		
				usort($data['filteredData'], function($a, $b) {
					return strtotime($a['DateTime']) - strtotime($b['DateTime']);
				});
		
				$dataCount['dataCount'] = count($data['filteredData']);
				$result = $this->TarikDataModel->importData($data['filteredData']);
				$existingRecordsCount = $result['existingRecordsCount'];
				$count = $dataCount['dataCount'];
				$existRecord = $existingRecordsCount;
				if($dataCount['dataCount'] > 0){
					$this->TarikDataModel->insertTarikDataLog($tarikdatarecord, $dataCount, $existingRecordsCount);
				}
			}else{
				$response = array(
					'status' => false,
					'message' => "Gagal koneksi ke mesin. Cek IP mesin!"
				);
			}
		}
			$response = array(
				'status' => true,
				'dataCount' => $count,
				'existRecord' => $existRecord,
			);

			$this->output
				->set_content_type('application/json')
				->set_output(json_encode($response));
	}
}
