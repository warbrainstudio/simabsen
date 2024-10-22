<?php
defined('BASEPATH') or exit('No direct script access allowed');

class CutiModel extends CI_Model
{
  private $_table = 'cuti';
  private $_tableView = '';

  public function rules()
  {
    return array(
      [
        'field' => 'absen_pegawai_id',
        'label' => 'ID Finger',
        'rules' => 'required|trim'
      ],
    );
  }

  public function getQuery($filter = null)
  {
    $query = "
      SELECT t.* FROM (
        SELECT 
            c.*,
            p.absen_pegawai_id,
            p.nama_lengkap,
            COALESCE(NULLIF(p.departemen, ''), '-') AS dept
        FROM cuti c
        LEFT JOIN pegawai p ON c.absen_pegawai_id = p.absen_pegawai_id
        GROUP BY c.id, p.absen_pegawai_id, p.nama_lengkap, p.departemen
      ) t
      WHERE 1=1
    ";
    if (!is_null($filter)) $query .= $filter;
    return $query;
  }

  public function getAll($params = array(), $orderField = null, $orderBy = 'asc')
  {
    $this->db->where($params);

    if (!is_null($orderField)) {
      $this->db->order_by($orderField, $orderBy);
    };

    return $this->db->get($this->_table)->result();
  }

  public function getDetail($params = array())
  {
    $this->db->select('
      cuti.*,
      pegawai.absen_pegawai_id,
      pegawai.nama_lengkap,
      unit.nama_unit
    ');
    $this->db->join('pegawai', 'pegawai.absen_pegawai_id = cuti.absen_pegawai_id', 'left');
    $this->db->join('unit', 'unit.nama_unit = pegawai.departemen', 'left');
    $this->db->where($params);
    $data = $this->db->get($this->_table);
    return $data->row();
  }

  public function insert()
  {
    $response = array('status' => false, 'data' => 'No operation.');
    $jenisDetail = $this->input->post('jeniscutiDetail');
    $jenis_cuti = '';
    if(!empty($jenisDetail)){
      $jenis_cuti = $jenisDetail;
    }else{
      $jenis_cuti = $this->input->post('jeniscuti');
    }
    try {
      $this->jenis_cuti = $jenis_cuti;
      $this->tanggal_pengajuan = $this->input->post('tanggal_pengajuan');
      $this->absen_pegawai_id = $this->input->post('absen_pegawai_id');
      $this->awal_cuti = $this->input->post('awalcuti');
      $this->akhir_cuti = $this->input->post('akhircuti');
      $this->tanggal_bekerja = $this->input->post('tanggalbekerja');
      $this->alamat_cuti = $this->input->post('alamatCuti');
      $this->telepon_cuti = $this->input->post('teleponCuti');
      $this->jumlah_persetujuan = $this->input->post('jumlahpersetujuan');
      $this->status_persetujuan = $this->input->post('statuspersetujuan');
      $this->created_by = $this->session->userdata('user')['id'];
      $this->db->insert($this->_table, $this);

      $response = array('status' => true, 'data' => 'Data has been saved.');
    } catch (\Throwable $th) {
      $response = array('status' => false, 'data' => 'Failed to save your data.');
    };

    /*$awalCuti = $this->input->post('awalcuti');
    $akhirCuti = $this->input->post('akhircuti');
    $awalTimestamp = strtotime($awalCuti);
    $akhirTimestamp = strtotime($akhirCuti);

    // Check if the dates are valid
    if ($awalTimestamp !== false && $akhirTimestamp !== false && $awalTimestamp <= $akhirTimestamp) {
        $currentTimestamp = $awalTimestamp;
        $attendancelog_tes = 'attendancelog';
        while ($currentTimestamp <= $akhirTimestamp) {
            $currentDate = date('Y-m-d', $currentTimestamp);
            $data = [
              'absen_id' => $this->input->post('absen_pegawai_id'),
              'tanggal_absen' => $currentDate,
              'status' => "3",
              'created_by' => $this->session->userdata('user')['id']
            ];
            $this->db->insert($attendancelog_tes, $data);
            $currentTimestamp = strtotime("+1 day", $currentTimestamp);
        }
    } else {
      $response = array('status' => false, 'data' => 'Invalid Date Range');
    }*/


    return $response;
  }

  public function insertBatch($data)
  {
    $failedInsertions = [];
    $existingRecordsCount = 0;
    $importedCount = 0;

    $this->db->trans_start();

    try {
        foreach ($data as $row) {
            $userID = $row['absen_id'];

            $this->db->where('absen_id', $userID);
            $count = $this->db->count_all_results($this->_table);

            if ($count == 0) {
                $insertData = [
                    'absen_id' => $userID,
                    'nama_lengkap' => $row['nama_lengkap'],
                    'departemen' => $row['departemen'],
                    'nomorpin' => $row['nomorpin'],
                    'created_by' => $row['created_by']
                ];

                if ($this->db->insert($this->_table, $insertData)) {
                    $importedCount++;
                } else {
                    $failedInsertions[] = [
                        'absen_id' => $userID,
                        'error' => $this->db->error()['message']
                    ];
                }
            } else {
                $existingRecordsCount++;
            }
        }
        
        $this->db->trans_complete();
    } catch (Exception $e) {
        $this->db->trans_rollback();
        $failedInsertions[] = [
            'absen_id' => isset($userID) ? $userID : 'N/A',
            'error' => $e->getMessage()
        ];
    }
    
    return [
        'importedCount' => $importedCount,
        'existingRecordsCount' => $existingRecordsCount,
        'failedInsertions' => $failedInsertions,
    ];
  }


  public function update($id)
  {
    $response = array('status' => false, 'data' => 'No operation.');

    try {
      $jenisCuti = $this->input->post('jeniscuti');
      if($jenisCuti="on"){
        $jenisCuti = $this->input->post('jeniscutiDetail');
      }
      $this->jenis_cuti = $jenisCuti;
      $this->awal_cuti = $this->input->post('awalcuti');
      $this->akhir_cuti = $this->input->post('akhircuti');
      $this->tanggal_bekerja = $this->input->post('tanggalbekerja');
      $this->alamat_cuti = $this->input->post('alamatCuti');
      $this->telepon_cuti = $this->input->post('teleponCuti');
      $this->updated_by = $this->session->userdata('user')['id'];
      $this->updated_date = date('Y-m-d H:i:s');
      $this->db->update($this->_table, $this, array('id' => $id));

      $response = array('status' => true, 'data' => 'Data has been saved.');
    } catch (\Throwable $th) {
      $response = array('status' => false, 'data' => 'Failed to save your data.');
    };

    return $response;
  }

  public function approve($id)
  {
      $response = array('status' => false, 'data' => 'No operation.');
  
      try {
          // Retrieve the current approvals
          $query = $this->db->select('jumlah_persetujuan, persetujuan_pertama, persetujuan_kedua, persetujuan_ketiga')
                            ->from($this->_table)
                            ->where('id', $id) // Ensure you're filtering by ID
                            ->get()
                            ->row(); // Get a single row
  
          if (!$query) {
              return array('status' => false, 'data' => 'No data found for the specified ID.');
          }

          $p  = (int) $query->jumlah_persetujuan;
          $p1 = $query->persetujuan_pertama;
          $p2 = $query->persetujuan_kedua;
          $p3 = $query->persetujuan_ketiga;
          $status = $this->input->post('persetujuan');
          $newStatus = $status . " " . $this->session->userdata('user')['role'];
          if ($this->session->userdata('user')['role'] === 'Administrator'){
            if ($p > 2) {
                if ($p1 === null) {
                    if($status == 'Ditolak'){
                      $this->persetujuan_pertama = $status;
                      $this->status_persetujuan = $status;
                    }else{
                      $this->persetujuan_pertama = $newStatus;
                    }
                } elseif ($p1 !== null && $p2 === null) {
                    if($status == 'Ditolak'){
                      $this->persetujuan_kedua = $status;
                      $this->status_persetujuan = $status;
                    }else{
                      $this->persetujuan_kedua = $newStatus;
                    }
                } elseif ($p2 !== null && $p3 === null) {
                    if($status == 'Ditolak'){
                      $this->persetujuan_ketiga = $status;
                      $this->status_persetujuan = 'Dipertimbangkan';
                    }else{
                      $this->persetujuan_ketiga = $newStatus;
                    }
                }
            } else {
                if ($p1 === null) {
                    $this->persetujuan_pertama = $newStatus;
                } elseif ($p1 !== null && $p2 === null) {
                    $this->persetujuan_kedua = $newStatus;
                    $this->status_persetujuan = $newStatus;
                }
            }
          } else if ($this->session->userdata('user')['role'] === 'P1'){
            $this->persetujuan_pertama = $newStatus;
          }
          $this->updated_by = $this->session->userdata('user')['id'];
          $this->updated_date = date('Y-m-d H:i:s');
  
          // Update the database
          $this->db->update($this->_table, $this, array('id' => $id));
  
          $response = array('status' => true, 'data' => 'Data has been saved.');
      } catch (\Throwable $th) {
          $response = array('status' => false, 'data' => 'Failed to save your data: ' . $th->getMessage());
      }
  
      return $response;
  }
  
  

  public function delete($id)
  {
    $response = array('status' => false, 'data' => 'No operation.');

    try {
      $this->db->delete($this->_table, array('id' => $id));

      $response = array('status' => true, 'data' => 'Data has been deleted.');
    } catch (\Throwable $th) {
      $response = array('status' => false, 'data' => 'Failed to delete your data.');
    };

    return $response;
  }

  public function truncate()
  {
    $response = array('status' => false, 'data' => 'No operation.');

    try {
      $this->db->truncate($this->_table);

      $response = array('status' => true, 'data' => 'Data has been truncated.');
    } catch (\Throwable $th) {
      $response = array('status' => false, 'data' => 'Failed to truncate your data.');
    };

    return $response;
  }

  function br2nl($text)
  {
    return str_replace("\r\n", '<br/>', htmlspecialchars_decode($text));
  }

  function clean_number($number)
  {
    return preg_replace('/[^0-9]/', '', $number);
  }
}
