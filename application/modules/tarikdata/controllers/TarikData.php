<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH . 'controllers/AppBackend.php');

class Tarikdata extends AppBackend {

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'AppMixModel',
            'TarikDataModel',
            'MesinModel',
          ));
          $this->load->library('form_validation');
    }

    public function index() {
      /*$apiUrl = base_url('api/getData/');
      $response = file_get_contents($apiUrl);
      $data_api = json_decode($response, true);
      $list_mesin_api = '';
      if (isset($data_api['list_mesin']) && is_array($data_api['list_mesin'])) {
        foreach ($data_api['list_mesin'] as $mesin) {
          $list_mesin_api .= '<option value="' . htmlspecialchars($mesin['ip']) . '">' . htmlspecialchars($mesin['name']) . ' (' . htmlspecialchars($mesin['lokasi']) . ') ' . htmlspecialchars($mesin['status']) .' '.htmlspecialchars($mesin['commkey']).'</option>';
        }
      }else{
        $list_mesin_api = 'Cek Token';
      }*/

      $data = array(
          'app' => $this->app(),
          'main_js' => $this->load_main_js('tarikdata'),
          'card_title' => 'Tarik Data',
          'list_mesin' => $this->init_list($this->MesinModel->getAll([], 'namamesin', 'asc'), 'ipadress','namamesin','status'),
          //'list_mesin_api' => $list_mesin_api,
      );
  
      $this->template->set('title', $data['card_title'] . ' | ' . $data['app']->app_name, TRUE);
      $this->template->load_view('index', $data, TRUE);
      $this->template->render();  
  }
  
    
    public function ajax_get_all()
  {
    $filter = $this->input->get('filter');
    $query = $this->TarikDataModel->getQuery($filter);
    $response = $this->AppMixModel->getdata_dtAjax($query);
    echo json_encode($response);
  }
  
  public function ajax_fetch_data() {
    $this->handle_ajax_request();
    $data = $this->input->get();
    
    $data['filteredData'] = [];
    
    $tarikdatarecord = $data;
    $datamesin = $data['machine'];
    if ($this->TarikDataModel->checkIPMachine($datamesin)) {
        $data['filteredData'] = array_merge(
            $data['filteredData'],
            $this->TarikDataModel->fetchDataFromMachine($data['machine'], $data['key'], $data['start_date'], $data['end_date'])
        );

        usort($data['filteredData'], function($a, $b) {
            return strtotime($a['DateTime']) - strtotime($b['DateTime']);
        });

        $dataCount['dataCount'] = count($data['filteredData']);

        $result = $this->TarikDataModel->importData($data['filteredData']);
        $existingRecordsCount = $result['existingRecordsCount'];
        $failedInsertions = $result['failedInsertions'];
        if($dataCount['dataCount'] > 0){
          $this->TarikDataModel->insertTarikDataLog($tarikdatarecord, $dataCount, $existingRecordsCount);
        }

        $response = array(
            'status' => true,
            'data' => array(
                'card_title' => 'Hasil Tarik Data',
                'datamesin' => $datamesin,
                'dataCount' => $dataCount['dataCount'],
                'existingRecordsCount' => $existingRecordsCount,
                'failedInsertions' => $failedInsertions
            )
        );
    }else{
      $response = array(
        'status' => false,
        'message' => "Gagal koneksi ke mesin. Cek IP mesin!"
      );

    }
      $this->output
          ->set_content_type('application/json')
          ->set_output(json_encode($response));
  }


    public function ajax_save($id = null)
  {
    $this->handle_ajax_request();
    $this->form_validation->set_rules($this->TarikDataModel->rules());

    if ($this->form_validation->run() === true) {
      if (is_null($id)) {
        echo json_encode($this->TarikDataModel->insert());
      } else {
        echo json_encode($this->TarikDataModel->update($id));
      };
    } else {
      $errors = validation_errors('<div>- ', '</div>');
      echo json_encode(array('status' => false, 'data' => $errors));
    };
  }

  public function ajax_delete($id)
  {
    $this->handle_ajax_request();
    echo json_encode($this->TarikDataModel->delete($id));
  }
}
