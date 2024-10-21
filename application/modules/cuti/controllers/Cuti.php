<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH . 'controllers/AppBackend.php');

use alhimik1986\PhpExcelTemplator\PhpExcelTemplator;
use alhimik1986\PhpExcelTemplator\setters\CellSetterArrayValueSpecial;

define('SPECIAL_ARRAY_TYPE', CellSetterArrayValueSpecial::class);

class Cuti extends AppBackend
{
  function __construct()
  {
    parent::__construct();
    $this->load->model(array(
      //'AppModel',
      'AppMixModel',
      'AbsenModel',
      'PegawaiModel',
      'CutiModel',
      'UnitModel'
    ));
    $this->load->library('form_validation');
  }

  public function index()
  {
    $agent = new Mobile_Detect;
    $role = $this->session->userdata('user')['role'];
    $data = array(
      'app' => $this->app(),
      'main_js' => $this->load_main_js('cuti'),
      'role' => $role,
      'controller' => $this,
      'is_mobile' => $agent->isMobile(),
      'card_title' => 'Data Cuti',
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

    // Ref
    $cuti = $this->CutiModel->getDetail(['cuti.id' => $ref]);
    $pegawai = $this->PegawaiModel->getDetail(['pegawai.absen_pegawai_id' => @$cuti->absen_pegawai_id]);
    $unitList = $this->init_list($this->UnitModel->getAll([], 'nama_unit', 'asc'), 'nama_unit', 'nama_unit', @$pegawai->departemen);
    // END ## Ref

    $data = array(
      'app' => $this->app(),
      'main_js' => $this->load_main_js('cuti', false, array(
        'key' => $ref,
        'is_load_partial' => 1,
        'pegawai_id' => @$pegawai->absen_pegawai_id,
        'pegawai_nama_lengkap' => @$pegawai->nama_lengkap,
      )),
      'key' => $ref,
      'card_title' => $actionLabel . $this->_pageTitle,
      'controller' => $this,
      'is_mobile' => $agent->isMobile(),
      'cuti' => $cuti,
      'pegawai' => $pegawai,
      'unit_list' => $unitList,
    );
    $this->template->set_template('sb_admin_modal_partial');
    $this->template->set('title', $data['card_title'] . ' | ' . $data['app']->app_name, TRUE);
    $this->template->load_view('form', $data, TRUE);
    $this->template->render();
  }

  public function ajax_get_all()
  {
    $filter = $this->input->get('filter');
    $query = $this->CutiModel->getQuery($filter);
    $response = $this->AppMixModel->getdata_dtAjax($query);
    echo json_encode($response);
  }

  public function ajax_save()
  {
    $this->handle_ajax_request();
    $this->form_validation->set_rules($this->CutiModel->rules());
    $id = $this->input->post('ref');

    if ($this->form_validation->run() === true) {
      if (is_null($id) || empty($id)) {
        echo json_encode($this->CutiModel->insert());
      } else {
        echo json_encode($this->CutiModel->update($id));
      };
    } else {
      $errors = validation_errors('<div>- ', '</div>');
      echo json_encode(array('status' => false, 'data' => $errors));
    };
  }

  public function ajax_delete($id)
  {
    $this->handle_ajax_request();
    echo json_encode($this->CutiModel->delete($id));
  }

  public function ajax_approve($id)
  {
    $this->handle_ajax_request();
    echo json_encode($this->CutiModel->approve($id));
  }

  /*public function detail()
  {
    //$agent = new Mobile_Detect;
    $ref = $this->input->get('ref');
    $ref = (!is_null($ref) && is_numeric($ref)) ? $ref : null;
    $actionLabel = '<span class="badge badge-info">View</span> ';

    $pegawai = $this->PegawaiModel->getDetail(['pegawai.absen_pegawai_id' => $ref]);
    $data = array(
      'app' => $this->app(),
      'main_js' => $this->load_main_js('pegawai', false, array(
        'action_route' => 'detail',
        'key' => $ref,
        'pegawai_idfinger' => @$pegawai->absen_id,
        'pegawai' => $pegawai,
      )),
      'card_title' => 'Data Absen ID : '.@$pegawai->absen_id,
      'controller' => $this,
      'isnull' => 'false',
      //'is_mobile' => $agent->isMobile(),
      'pegawai' => $pegawai,
    );
    //$this->template->set_template('sb_admin_partial');
    $this->template->set('title', $data['card_title'] . ' | ' . $data['app']->app_name, TRUE);
    $this->template->load_view('view', $data, TRUE);
    $this->template->render();
  }

  public function excel()
  {
    try {
        $absen_id = $this->input->get('absen_id');
        $fileTemplate = FCPATH . 'directory/templates/template-histori-pegawai.xlsx';
        $callbacks = array();

        $pegawai = $this->PegawaiModel->getDetail(array('absen_id' => $absen_id));
        
        if ($pegawai) {
            $payload = $this->AbsenModel->getAll(array('absen_pegawai_id' => $pegawai->absen_id));
        } else {
            show_404();
            return;
        }

        if (!is_null($payload)) {
            $outputFileName = 'histori absen ' . (!empty($pegawai->nama_lengkap) ? $pegawai->nama_lengkap : 'unknown') . '.xlsx';

            $payloadStatic = $this->arrayToSetterSimple(array('nama_pegawai' => $pegawai->nama_lengkap));
            $payloadStatic = array_merge($payloadStatic, $this->arrayToSetterSimple(array('app_export_date' => date('Y-m-d H:i:s'))));
            $payloadSimple = $this->arrayToSetterSimple((array) $pegawai);
            $payload = $this->arrayToSetter($payload);
            $payload = array_merge($payload, $payloadSimple, $payloadStatic);

            PhpExcelTemplator::outputToFile($fileTemplate, $outputFileName, $payload, $callbacks);
        } else {
            show_404();
        }
    } catch (\Throwable $th) {
        log_message('error', $th->getMessage());

        show_error('Terjadi kesalahan ketika memproses data. Detail: ' . $th->getMessage(), 500);
    }
  }*/

}
