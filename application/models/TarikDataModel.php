<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TarikDataModel extends CI_Model {

    private $_table = 'tarikdatalog';
    private $_tableView = '';

    public function __construct() {
        parent::__construct();
    }

    public function getQuery($filter = null)
  {
    $query = "
      SELECT t.* FROM (
        SELECT 
            td.*,
            m.namamesin,
            m.ipadress,
            m.lokasi,
            m.status
        FROM tarikdatalog td
        LEFT JOIN mesin m ON m.ipadress = td.ipmesin
      ) t
      WHERE 1=1
    ";
    if (!is_null($filter)) $query .= $filter;
    return $query;
  }

    public function checkIPMachine($IP) { 
        $timeout = 200; // Timeout in milliseconds
        $Connect = @fsockopen($IP, 80, $errno, $errstr, $timeout);
        
        return $Connect !== false;
    }

    public function fetchDataFromMachine($IP, $Key, $startDate, $endDate) {
        $timeout = 200;
        $Connect = fsockopen($IP, "80", $errno, $errstr, $timeout);
        $filteredData = [];
        if ($Connect) {
            $formattedStartDate = date('Y-m-d\TH:i:s', strtotime($startDate . ' 00:00:00'));
            $formattedEndDate = date('Y-m-d\TH:i:s', strtotime($endDate . ' 23:59:59'));
    
            $soap_request = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
        <soap:Body>
            <GetAttLog xmlns="http://tempuri.org/">
                <ArgComKey xsi:type="xsd:integer">$Key</ArgComKey>
                <Arg></Arg>
                <DateTimeRange>
                    <StartDate>$formattedStartDate</StartDate>
                    <EndDate>$formattedEndDate</EndDate>
                </DateTimeRange>
            </GetAttLog>
        </soap:Body>
</soap:Envelope>
XML;
    
            $newLine = "\r\n";
            fputs($Connect, "POST /iWsService HTTP/1.1" . $newLine);
            fputs($Connect, "Host: $IP" . $newLine);
            fputs($Connect, "Content-Type: text/xml" . $newLine);
            fputs($Connect, "Content-Length: " . strlen($soap_request) . $newLine . $newLine);
            fputs($Connect, $soap_request . $newLine);
    
            $buffer = "";
            while (!feof($Connect)) {
                $Response = fgets($Connect, 1024);
                if ($Response === false) break;
                $buffer .= $Response;
            }
            fclose($Connect);
    
            if (strpos($buffer, '500 Internal Server Error') !== false) {
                echo "The server encountered an error while processing the request.";
                exit;
            }
    
            $this->load->helper('parse');
            $buffer = Parse_Data($buffer, "<GetAttLogResponse>", "</GetAttLogResponse>");
            $buffer = explode("\r\n", $buffer);
    
            foreach ($buffer as $line) {
                $data = Parse_Data($line, "<Row>", "</Row>");
                if ($data) {
                    $PIN = Parse_Data($data, "<PIN>", "</PIN>");
                    $DateTime = Parse_Data($data, "<DateTime>", "</DateTime>");
                    $Verified = Parse_Data($data, "<Verified>", "</Verified>");
                    $Status = Parse_Data($data, "<Status>", "</Status>");
    
                    $dataDateTime = date('Y-m-d', strtotime($DateTime));
    
                    if ($dataDateTime >= $startDate && $dataDateTime <= $endDate) {
                        $filteredData[] = [
                            'PIN' => htmlspecialchars($PIN),
                            'DateTime' => htmlspecialchars($DateTime),
                            'Verified' => htmlspecialchars($Verified),
                            'Status' => htmlspecialchars($Status),
                            'Machine' => htmlspecialchars($IP),
                        ];
                    }
                }
            }
        } else {
            echo "Connection failed: $errstr ($errno)";
        }
    
        return $filteredData;
    }

    public function importData($data) {
      $failedInsertions = [];
      $existingRecordsCount = 0;

      $this->db->trans_start();

      try {
          foreach ($data as $row) {
              $userID = $row['PIN'];
              $dateTime = $row['DateTime'];
              $verified = $row['Verified'];
              $status = $row['Status'];
              $machine = $row['Machine'];

              $this->db->where('absen_id', $userID);
              $this->db->where('tanggal_absen', $dateTime);
              $count = $this->db->count_all_results('attendancelog');

              if ($count == 0) {
                  $data = [
                      'absen_id' => $userID,
                      'tanggal_absen' => $dateTime,
                      'verified' => $verified,
                      'status' => $status,
                      'ipmesin' => $machine
                  ];
                  if (!$this->db->insert('attendancelog', $data)) {
                      $failedInsertions[] = [
                          'absen_id' => $userID,
                          'dateTime' => $dateTime,
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
              'dateTime' => isset($dateTime) ? $dateTime : 'N/A',
              'error' => $e->getMessage()
          ];
      }

      return [
          'failedInsertions' => $failedInsertions,
          'existingRecordsCount' => $existingRecordsCount
      ];
      $this->resetDatabase();
    }

    public function resetDatabase() 
    {
            $db_default = 'default';
            $this->db = $this->load->database($db_default, TRUE);
    }

    public function insertTarikDataLog($tarikdatarecord, $dataCount, $existingRecordsCount) 
    {
        $_table = 'tarikdatalog';
        $startDate = date('d-m-Y', strtotime($tarikdatarecord['start_date']));
        $endDate = date('d-m-Y', strtotime($tarikdatarecord['end_date']));
        $hostname = $this->db->hostname;
        $insertData = [
            'host' => $hostname,
            'ipmesin' => $tarikdatarecord['machine'],
            'jumlahdata' => $dataCount['dataCount'],
            'existsdata' => $existingRecordsCount,
            'tanggaldata' => $startDate . ' to ' . $endDate,
            'created_by' => $this->created_by = $this->session->userdata('user')['id']
        ];

        return $this->db->insert($_table, $insertData);
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
