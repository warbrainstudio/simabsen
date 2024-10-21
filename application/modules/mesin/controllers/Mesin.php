<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH . 'controllers/AppBackend.php');

class Mesin extends AppBackend
{
  function __construct()
  {
    parent::__construct();
    $this->load->model(array(
      'AppMixModel',
      'MesinModel',
    ));
    $this->load->library('form_validation');
  }

  public function index()
  {
    /*$machines = $this->MesinModel->getAll();
    $result = $this->init_list($machines, 'id', 'ipadress');

    foreach ($result as $machine) {
        $ipAddress = $machine['ipadress'];
        $this->checkConnect($ipAddress);
    }*/
    $data = array(
      'app' => $this->app(),
      'main_js' => $this->load_main_js('mesin'),
      'card_title' => 'Mesin',
    );
    $this->template->set('title', $data['card_title'] . ' | ' . $data['app']->app_name, TRUE);
    $this->template->load_view('index', $data, TRUE);
    $this->template->render();
  }

  public function ajax_get_all()
  {
    $filter = $this->input->get('filter');
    $query = $this->MesinModel->getQuery($filter);
    $response = $this->AppMixModel->getdata_dtAjax($query);
    echo json_encode($response);
  }

  public function ajax_save($id = null)
  {
    $this->handle_ajax_request();
    $this->form_validation->set_rules($this->MesinModel->rules());

    if ($this->form_validation->run() === true) {
      if (is_null($id)) {
        echo json_encode($this->MesinModel->insert());
      } else {
        echo json_encode($this->MesinModel->update($id));
      };
    } else {
      $errors = validation_errors('<div>- ', '</div>');
      echo json_encode(array('status' => false, 'data' => $errors));
    };
  }

  public function ajax_check($ip)
  {
    $this->handle_ajax_request();
    echo json_encode($this->MesinModel->checkConnect($ip));
  }

  public function ajax_delete($id)
  {
    $this->handle_ajax_request();
    echo json_encode($this->MesinModel->delete($id));
  }
}
