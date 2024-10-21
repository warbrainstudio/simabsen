<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH . 'controllers/AppBackend.php');

use alhimik1986\PhpExcelTemplator\PhpExcelTemplator;
use alhimik1986\PhpExcelTemplator\setters\CellSetterArrayValueSpecial;

define('SPECIAL_ARRAY_TYPE', CellSetterArrayValueSpecial::class);

class Absen extends AppBackend
{
  function __construct()
  {
    parent::__construct();
    $this->load->model(array(
      'AppMixModel',
      //'AppModel',
      'AbsenModel',
      'PegawaiModel',
      'MesinModel'
    ));
    $this->load->library('form_validation');
  }

  public function index()
  {
    $data = array(
      'app' => $this->app(),
      'main_js' => $this->load_main_js('absen'),
      'card_title' => 'Absen',
    );
    $this->template->set('title', $data['card_title'] . ' | ' . $data['app']->app_name, TRUE);
    $this->template->load_view('index', $data, TRUE);
    $this->template->render();
  }

  public function input()
  {
    $this->handle_ajax_request();

    $agent = new Mobile_Detect;
    $ref = $this->input->get('ref');
    $ref = (!is_null($ref) && is_numeric($ref)) ? $ref : null;
    $actionLabel = (!is_null($ref) && is_numeric($ref)) ? 'Edit' : 'New';
    $actionLabel = '<span class="badge badge-info">' . $actionLabel . '</span> ';

    $attendancelog = $this->AbsenModel->getDetail(['attendancelog.absen_pegawai_id' => $ref]);
    $pegawailist = $this->init_list($this->PegawaiModel->getAll([], 'nama_lengkap', 'asc'), 'id', 'nama_lengkap');
    $mesinList = $this->init_list($this->MesinModel->getAll([], 'namamesin', 'asc'), 'id', 'namamesin');

    $data = array(
      'app' => $this->app(),
      'main_js' => $this->load_main_js('attendancelog', false, array(
        'key' => $ref,
        'is_load_partial' => 1,
        'pegawai_list' => $pegawailist,
        'mesin_list' => $mesinList,
      )),
      'key' => $ref,
      'card_title' => $actionLabel . $this->_pageTitle,
      'controller' => $this,
      'is_mobile' => $agent->isMobile(),
      'attendancelog' => $attendancelog,
      'pegawai_list' => $pegawailist,
      'mesin_list' => $mesinList,
    );
    //$this->template->set_template('sb_admin_modal_partial');
    $this->template->set('title', $data['card_title'] . ' | ' . $data['app']->app_name, TRUE);
    $this->template->load_view('form', $data, TRUE);
    $this->template->render();
  }

  public function ajax_get_all()
  {
    $filter = $this->input->get('filter');
    $query = $this->AbsenModel->getQuery($filter);
    $response = $this->AppMixModel->getdata_dtAjax($query);
    echo json_encode($response);
  }

  public function ajax_get_pegawai_item()
  {
    $this->handle_ajax_request();
    $ref = $this->input->get('absen_id');
    $response = $this->AbsenModel->getAll(['absen_id' => $ref], 'asc');
    echo json_encode($response);
  }

  public function ajax_save($id = null)
  {
    $this->handle_ajax_request();
    $this->form_validation->set_rules($this->AbsenModel->rules());

    if ($this->form_validation->run() === true) {
      if (is_null($id)) {
        echo json_encode($this->AbsenModel->insert());
      } else {
        echo json_encode($this->AbsenModel->update($id));
      };
    } else {
      $errors = validation_errors('<div>- ', '</div>');
      echo json_encode(array('status' => false, 'data' => $errors));
    };
  }

  public function ajax_delete($id)
  {
    $this->handle_ajax_request();
    echo json_encode($this->AbsenModel->delete($id));
  }

  public function ajax_delete_pegawai($absen_id)
  {
    $this->handle_ajax_request();
    echo json_encode($this->AbsenModel->deletepegawai($absen_id));
  }

  public function excel()
  {
      try {
          $date = $this->input->get('date');
          $status = '';
          $formattedDate = $date;
          if (DateTime::createFromFormat('Y-m-d', $date) !== false) {
              $fileTemplate = FCPATH . 'directory/templates/template-attendance-harian.xlsx';
              $dateTime = DateTime::createFromFormat('Y-m-d', $date);
              $Day = $dateTime->format('D');
              $DayNumber = $dateTime->format('d');
              $monthNumber = $dateTime->format('m');
              $year = $dateTime->format('Y');
              $formattedDay = $this->get_day($Day);
              $formattedMonth = $this->get_month($monthNumber);
              $formattedDate = $formattedDay.', '.$DayNumber.' '.$formattedMonth . ' ' . $year;
              $status = $formattedDate;
          } else if(DateTime::createFromFormat('Y-m', $date) !== false) {
              $fileTemplate = FCPATH . 'directory/templates/template-attendance.xlsx';
              $dateTime = DateTime::createFromFormat('Y-m', $date);
              $monthNumber = $dateTime->format('m');
              $year = $dateTime->format('Y');
              $formattedMonth = $this->get_month($monthNumber);
              $formattedDate = $formattedMonth . ' ' . $year;
              $status = 'Bulan ' . $formattedDate;
          } else{
              ini_set('memory_limit', '4G');
              $fileTemplate = FCPATH . 'directory/templates/template-attendance.xlsx';
              $status = 'Tahun'; 
          }
          $callbacks = array();

          $payload = $this->AbsenModel->getAll(array('tanggal_absen' => $date));
  
          if (!is_null($payload)) {

              $outputFileName = 'histori absen ' . $formattedDate . '.xlsx';
              $payloadStatic = $this->arrayToSetterSimple(array('tanggal_absen' => $formattedDate,'status' => $status));
              $payloadStatic = array_merge($payloadStatic, $this->arrayToSetterSimple(array('app_export_date' => date('Y-m-d H:i:s'))));
              $payload = $this->arrayToSetter($payload);
              $payload = array_merge($payload, $payloadStatic);
  
              PhpExcelTemplator::outputToFile($fileTemplate, $outputFileName, $payload, $callbacks);
          } else {
              show_404();
          }
      } catch (\Throwable $th) {
          log_message('error', $th->getMessage());
  
          show_error('Terjadi kesalahan ketika memproses data. Detail: ' . $th->getMessage(), 500);
      }
  }

}
