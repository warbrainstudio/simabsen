<?php
defined('BASEPATH') or exit('No direct script access allowed');

class AbsenModel extends CI_Model
{
  private $_table = 'attendancelog';
  private $_tableView = '';


  public function getQuery($filter = null)
  {
    $query = "
      SELECT t.* FROM (
        SELECT 
          att.*,
          m.ipadress, 
          m.namamesin, 
          COALESCE(p.nama_lengkap, '-') AS nama,
          COALESCE(NULLIF(p.departemen, ''), '-') AS dept,
          COALESCE(NULLIF(p.nomorpin, ''), '-') AS nopin,
          (CASE WHEN att.status = 0 THEN 'Masuk' WHEN att.status = 3 THEN 'Cuti' ELSE 'Pulang' END) AS nama_status,
          (CASE WHEN att.verified = 1 THEN 'Finger' ELSE 'Input' END) AS verifikasi
        FROM attendancelog att
        LEFT JOIN pegawai p ON att.absen_id = p.absen_pegawai_id
        LEFT JOIN mesin m ON m.ipadress = att.ipmesin
      ) t
      WHERE 1=1
    ";

    if (!is_null($filter)) $query .= $filter;
    return $query;
  }

  public function getQueryTime($filter = null)
  {
      // Construct the base SQL query
      $query = "
        SELECT t.* FROM (
              SELECT 
                  att.absen_id,
                  att.tanggal_absen,
                  m.ipadress, 
                  m.namamesin, 
                  COALESCE(p.nama_lengkap, '-') AS nama,
                  COALESCE(NULLIF(p.departemen, ''), '-') AS dept,
                  COALESCE(NULLIF(p.nomorpin, ''), '-') AS nopin,
                  CASE WHEN att.verified = 1 THEN 'Finger' ELSE 'Input' END AS verifikasi,
                  COALESCE(MAX(CASE WHEN att.status = 0 THEN att.tanggal_absen END), NULL) AS masuk,
                  COALESCE(MAX(CASE WHEN att.status = 1 THEN att.tanggal_absen END), NULL) AS pulang,
                  COALESCE(EXTRACT(EPOCH FROM (MAX(CASE WHEN att.status = 1 THEN att.tanggal_absen END) - 
                                                        MAX(CASE WHEN att.status = 0 THEN att.tanggal_absen END))), 0) / 3600 AS jam_kerja
              FROM 
                  attendancelog att
              LEFT JOIN 
                  pegawai p ON att.absen_id = p.absen_pegawai_id
              LEFT JOIN 
                  mesin m ON m.ipadress = att.ipmesin
              GROUP BY 
                  att.absen_id, att.tanggal_absen, att.verified, m.ipadress, m.namamesin, p.nama_lengkap, p.departemen, p.nomorpin
              ORDER BY 
                  att.absen_id
        ) t
        WHERE 1=1
      ";
  
      // Append any additional filters if provided
      if (!is_null($filter)) {
          $query .= " " . $filter; // Ensure $filter is a string
      }
  
      return $query; // Return the constructed SQL query
  }
    

  public function getnullpegawaiQuery($filter = null)
  {
    $query = "
        SELECT t.* FROM (
          SELECT
            att.absen_id, 
            COUNT(att.tanggal_absen) AS datetime_count
          FROM attendancelog att
          LEFT JOIN pegawai p ON att.absen_id = p.absen_pegawai_id
          WHERE p.absen_pegawai_id IS NULL
          GROUP BY att.absen_id
        ) t
         WHERE 1=1
    ";
    if (!is_null($filter)) $query .= $filter;
    return $query;
  }

  public function getNull(){
    $query = "
          SELECT
            attendancelog.absen_id 
          FROM attendancelog
          LEFT JOIN pegawai ON attendancelog.absen_id = pegawai.absen_pegawai_id
          WHERE pegawai.absen_pegawai_id IS NULL
          GROUP BY attendancelog.absen_id
    ";
    
    // Assuming you have a database connection and using CodeIgniter's query builder
    $result = $this->db->query($query);
    return $result->result();
  }



  public function getAll($params = array(), $orderField = null, $orderBy = 'asc')
  {
      if (isset($params['tanggal_absen'])) {
          
        $orderField = 'tanggal_absen';
        $dateParam = $params['tanggal_absen'];

        $this->db->select('attendancelog.absen_id, 
                          attendancelog.tanggal_absen,
                          TO_CHAR(attendancelog.tanggal_absen, \'HH24:MI:SS\') AS jamabsen,
                          CASE WHEN attendancelog.verified = 1 THEN \'Finger\' ELSE \'Input\' END AS verifikasi, 
                          CASE WHEN attendancelog.status = 0 THEN \'Masuk\' ELSE \'Pulang\' END AS nama_status, 
                          COALESCE(pegawai.nama_lengkap, \'-\') AS pegawai_nama,
                          COALESCE(NULLIF(pegawai.departemen, \'\'), \'-\') AS pegawai_departemen, 
                          mesin.namamesin AS mesin_nama');
        $this->db->join('pegawai', 'attendancelog.absen_id = pegawai.absen_pegawai_id', 'left');
        $this->db->join('mesin', 'attendancelog.ipmesin = mesin.ipadress', 'left');

        if (preg_match('/^\d{4}-\d{2}$/', $dateParam)) {

          list($year, $month) = explode('-', $dateParam);
          $startDate = date('Y-m-d', strtotime("$year-$month-1"));
          $endDate = date('Y-m-d', strtotime("last day of $year-$month"));
          $this->db->where("tanggal_absen BETWEEN '$startDate' AND '$endDate'");

        }elseif (preg_match('/^\d{4}$/', $dateParam)) {

          $startDate = date('Y-m-d', strtotime("$dateParam-01-01"));
          $endDate = date('Y-m-d', strtotime("$dateParam-12-31"));
          $this->db->where("tanggal_absen BETWEEN '$startDate' AND '$endDate'");

        }else{  

          $this->db->where("DATE(attendancelog.tanggal_absen)", $dateParam);

        }

        unset($params['tanggal_absen']);
        
        if (!is_null($orderField)) {
          $this->db->order_by($orderField, $orderBy);
        }
    
        return $this->db->get($this->_table)->result();

      }elseif(isset($params['absen_id'])){
        
        $orderField = 'tanggal_absen';

        $this->db->select('attendancelog.absen_id, 
                TO_CHAR(attendancelog.tanggal_absen, \'YYYY-MM-DD\') AS tanggal,
                TO_CHAR(attendancelog.tanggal_absen, \'HH24:MI:SS\') AS jamabsen,
                CASE WHEN attendancelog.verified = 1 THEN \'Finger\' ELSE \'Input\' END AS verifikasi, 
                CASE WHEN attendancelog.status = 0 THEN \'Masuk\' ELSE \'Pulang\' END AS nama_status, 
                mesin.namamesin AS mesin_nama');
        $this->db->join('pegawai', 'attendancelog.absen_id = pegawai.absen_pegawai_id', 'left');
        $this->db->join('mesin', 'attendancelog.ipmesin = mesin.ipadress', 'left');
        $this->db->where($params);

        if (!is_null($orderField)) {
          $this->db->order_by($orderField, $orderBy);
        }
        
        return $this->db->get($this->_table)->result();

      }else{

        $this->db->where($params);

        if (!is_null($orderField)) {
          $this->db->order_by($orderField, $orderBy);
        }
        
        return $this->db->get($this->_table)->result();

      }
  }
   

  public function getDetail($params = array())
  {
    return $this->db->where($params)->get($this->_table)->row();
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

  public function deletepegawai($absen_id)
  {
    $response = array('status' => false, 'data' => 'No operation.');

    try {
      $this->db->delete($this->_table, array('absen_pegawai_id' => $absen_id));

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
