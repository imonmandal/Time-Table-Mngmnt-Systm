<?php

require_once('vendor/autoload.php');
require_once('DBController.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class dataImport
{
    private $db = null;

    public function __construct(DBController $db)
    {
        if (!isset($db->con)) return null;
        $this->db = $db;
    }

    public function impData($xlFile, $table, $column) // col->array of col name of table in db
    {
        $arr_file = explode('.', $xlFile['name']);
        $extension = end($arr_file);
        $reader = null;

        if ('csv' == $extension) {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
        } else {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        }

        $spreadsheet = $reader->load($xlFile['tmp_name']);

        // Note that sheets are indexed from 0
        $sheetData = $spreadsheet->getSheet(0)->toArray();
        // $sheetData = $spreadsheet->getActiveSheet()->toArray();

        if (!empty($sheetData)) {
            for ($row = 1; $row < count($sheetData); $row++) { // fetch row (indexing starts from 0)
                $data = "";
                for ($col = 0; $col < count($sheetData[$row]) - 1; $col++) {
                    $d = $sheetData[$row][$col];
                    if (strlen($d) == 0) {
                        $data = $data . "NULL" . ", ";
                    } else {
                        $data = $data . "'" . $d . "'" . ", ";
                    }
                }
                $d2 = $sheetData[$row][count($sheetData[$row]) - 1];
                if (strlen($d2) == 0) {
                    $data = $data . "NULL";
                } else {
                    $data = $data . "'" . $d2 . "'";
                }
                $columns = implode('`,`', array_values($column));
                $columns = "`" . $columns . "`";

                $query_string = "INSERT INTO `{$table}` ({$columns}) VALUES ({$data});";
                $this->db->con->query($query_string);
            }
        }
    }
}
