<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH . 'controllers/AppBackend.php');

use alhimik1986\PhpExcelTemplator\PhpExcelTemplator;
use alhimik1986\PhpExcelTemplator\setters\CellSetterArrayValueSpecial;

define('SPECIAL_ARRAY_TYPE', CellSetterArrayValueSpecial::class);

class Pegawai extends AppBackend
{
  function __construct()
  {
    parent::__construct();
    $this->load->model(array(
      //'AppModel',
      'AppMixModel',
      'AbsenModel',
      'PegawaiModel',
      'MesinModel',
      'UnitModel'
    ));
    $this->load->library('form_validation');
  }

  public function index()
  {
    $results = $this->AbsenModel->getNull();
    $countNull = count($results);

    $data = array(
      'app' => $this->app(),
      'main_js' => $this->load_main_js('pegawai'),
      'list_unit' => $this->init_list($this->UnitModel->getAll(), 'nama_unit','nama_unit'),
      'card_title' => 'Data Pegawai',
      'countNull' => $countNull,
    );
    $this->template->set('title', $data['card_title'] . ' | ' . $data['app']->app_name, TRUE);
    $this->template->load_view('index', $data, TRUE);
    $this->template->render();
  }

  public function ajax_get_all()
  {
    $filter = $this->input->get('filter');
    $query = $this->PegawaiModel->getQuery($filter);
    $response = $this->AppMixModel->getdata_dtAjax($query);
    echo json_encode($response);
  }

  public function ajax_get_null_all()
  {
    $filter = $this->input->get('filter');
    $query = $this->AbsenModel->getnullpegawaiQuery($filter);
    $response = $this->AppMixModel->getdata_dtAjax($query);
    echo json_encode($response);
  }

  public function ajax_save($id = null)
  {
    $this->handle_ajax_request();
    $this->form_validation->set_rules($this->PegawaiModel->rules());

    if ($this->form_validation->run() === true) {
      if (is_null($id)) {
        echo json_encode($this->PegawaiModel->insert());
      } else {
        echo json_encode($this->PegawaiModel->update($id));
      };
    } else {
      $errors = validation_errors('<div>- ', '</div>');
      echo json_encode(array('status' => false, 'data' => $errors));
    };
  }

  public function ajax_delete($id)
  {
    $this->handle_ajax_request();
    echo json_encode($this->PegawaiModel->delete($id));
  }

  public function detail()
  {
    $agent = new Mobile_Detect;
    $ref = $this->input->get('ref');
    $ref = (!is_null($ref) && is_numeric($ref)) ? $ref : null;
    $actionLabel = '<span class="badge badge-info">View</span> ';

    $pegawai = $this->PegawaiModel->getDetail(['pegawai.absen_pegawai_id' => $ref]);
    $data = array(
      'app' => $this->app(),
      'main_js' => $this->load_main_js('pegawai', false, array(
        'action_route' => 'detail',
        'key' => $ref,
        'pegawai_idfinger' => @$pegawai->absen_pegawai_id,
        'pegawai' => $pegawai,
      )),
      'card_title' => 'Data Absen ID : '.@$pegawai->absen_pegawai_id,
      'controller' => $this,
      'isnull' => 'false',
      'is_mobile' => $agent->isMobile(),
      'pegawai' => $pegawai,
    );
    $this->template->set_template('sb_admin_partial');
    $this->template->set('title', $data['card_title'] . ' | ' . $data['app']->app_name, TRUE);
    $this->template->load_view('view', $data, TRUE);
    $this->template->render();
  }

  public function detailnull()
  {
    $agent = new Mobile_Detect;
    $ref = $this->input->get('ref');
    $ref = (!is_null($ref) && is_numeric($ref)) ? $ref : null;
    $actionLabel = '<span class="badge badge-info">View</span> ';
    $data = array(
      'app' => $this->app(),
      'main_js' => $this->load_main_js('pegawai', false, array(
        'action_route' => 'detail',
        'key' => $ref,
        'pegawai_idfinger' => $ref,
      )),
      'card_title' => 'Data Absen Tanpa Nama. ID : '.$ref,
      'controller' => $this,
      'is_mobile' => $agent->isMobile(),
      'isnull' => 'true',
    );
    $this->template->set_template('sb_admin_partial');
    $this->template->set('title', $data['card_title'] . ' | ' . $data['app']->app_name, TRUE);
    $this->template->load_view('view', $data, TRUE);
    $this->template->render();
  }

  public function ajax_import_data()
  {
    if (isset($_FILES["file"]["name"])) {
        $file_tmp = $_FILES['file']['tmp_name'];
        $file_name = $_FILES['file']['name'];
        $file_size = $_FILES['file']['size'];
        $file_type = $_FILES['file']['type'];
        $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);

        if ($file_extension === 'xls') {
            $object = PHPExcel_IOFactory::load($file_tmp);
            
            foreach ($object->getWorksheetIterator() as $worksheet) {
                $highestRow = $worksheet->getHighestRow();
                $highestColumn = $worksheet->getHighestColumn();

                for ($row = 2; $row <= $highestRow; $row++) {
                    $departemen = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
                    $nama_lengkap = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                    $absen_pegawai_id = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
                    $nomorpin = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
                    $userUpload = $this->session->userdata('user')['id'];
                    if (!is_numeric($absen_pegawai_id) || is_null($absen_pegawai_id)) {
                      $response = array(
                          'status' => false,
                          'notify' => 'warning',
                          'message' => "File Excel tidak terbaca. Cek apakah File Excel berstatus 'Protected View' atau tidak"
                      );
                      $this->output
                        ->set_content_type('application/json')
                        ->set_output(json_encode($response));
                      return $response;
                    }
                    $data[] = array(
                        'absen_pegawai_id'    => $absen_pegawai_id,
                        'nama_lengkap' => $nama_lengkap,
                        'departemen'  => $departemen,
                        'nomorpin'    => $nomorpin,
                        'created_by'  => $userUpload,
                    );

                    $dataUnit[] = array(
                      'nama_unit' => $departemen,
                    );
                }
            }
            $result = $this->PegawaiModel->insertBatch($data);
            $this->UnitModel->insertBatch($dataUnit);
            $dataCount = $result['importedCount'];
            $existingRecordsCount = $result['existingRecordsCount'];
            $failedInsertions = $result['failedInsertions'];

            $response = array(
              'status' => true,
              'data' => array(
                  'dataCount' => $dataCount,
                  'existingRecordsCount' => $existingRecordsCount,
                  'failedInsertions' => $failedInsertions
              )
            );
        } else {
          $response = array(
            'status' => false,
            'notify' => 'warning',
            'message' => "File harus memiliki format .xls!"
          );
        }
    }
      $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($response));
  }

  public function excel()
  {
    try {
        $absen_pegawai_id = $this->input->get('absen_pegawai_id');
        $fileTemplate = FCPATH . 'directory/templates/template-histori-pegawai.xlsx';
        $callbacks = array();

        $pegawai = $this->PegawaiModel->getDetail(array('absen_pegawai_id' => $absen_pegawai_id));
        
        if ($pegawai) {
            $payload = $this->AbsenModel->getAll(array('absen_id' => $pegawai->absen_pegawai_id));
        } else {
            show_404();
            return;
        }

        if (!is_null($payload)) {
            $outputFileName = 'histori absen ' . (!empty($pegawai->nama_lengkap) ? $pegawai->nama_lengkap : 'unknown') . '.xlsx';

            $payloadStatic = $this->arrayToSetterSimple(array('nama_lengkap' => $pegawai->nama_lengkap));
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
  }

}
