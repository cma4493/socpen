<?php

/**
 * Created By: JOSEF FRIEDRICH S. BALDO
 * Date & Time: 1/19/2016 2:47 PM
 */
class GeneratedPayrollList extends DAO
{
    public function insertGenPayroll($file_name,$directory,$created_by)
    {
        $sql = 'INSERT INTO tbl_print_payroll(date_generated, file_name, directory, created_by, date_created)
                VALUES(
                NOW(),
                :file_name,
                :directory,
                :created_by,
                NOW()
                )';
        $this->openDB();
        $this->prepareQuery($sql);
        $this->bindQueryParam(':file_name',$file_name . '.pdf');
        $this->bindQueryParam(':directory',$directory);
        $this->bindQueryParam(':created_by',$created_by);
        $this->beginTrans();
        $result = $this->executeUpdate();
        if($result)
        {
            $this->commitTrans();
            $exec_result = 1;
        }
        else
        {
            $this->rollbackTrans();
            $exec_result = 0;
        }
        $this->closeDB();
        return $exec_result;
    }

    public function insertGenAR($file_name,$directory,$created_by)
    {
        $sql = 'INSERT INTO tbl_print_ar(date_generated, file_name, directory, created_by, date_created)
                VALUES(
                NOW(),
                :file_name,
                :directory,
                :created_by,
                NOW()
                )';
        $this->openDB();
        $this->prepareQuery($sql);
        $this->bindQueryParam(':file_name',$file_name . '.pdf');
        $this->bindQueryParam(':directory',$directory);
        $this->bindQueryParam(':created_by',$created_by);
        $this->beginTrans();
        $result = $this->executeUpdate();
        if($result)
        {
            $this->commitTrans();
            $exec_result = 1;
        }
        else
        {
            $this->rollbackTrans();
            $exec_result = 0;
        }
        $this->closeDB();
        return $exec_result;
    }

    protected function generatedList()
    {
        $sql = 'SELECT * FROM tbl_print_payroll';
        $this->openDB();
        $this->prepareQuery($sql);
        $result = $this->executeQuery();
        $recordlist = array();
        $this->closeDB();
        foreach($result as $i => $rowData)
        {
            $recordlist[$i] = array(
                'date_generated' => $rowData['date_generated'],
                'file_name' => $rowData['file_name'],
                'directory' => $rowData['directory'],
                'created_by' => $rowData['created_by']
            );
        }
        return $recordlist;
    }

    protected function generatedListAR()
    {
        $sql = 'SELECT * FROM tbl_print_ar';
        $this->openDB();
        $this->prepareQuery($sql);
        $result = $this->executeQuery();
        $recordlist = array();
        $this->closeDB();
        foreach($result as $i => $rowData)
        {
            $recordlist[$i] = array(
                'date_generated' => $rowData['date_generated'],
                'file_name' => $rowData['file_name'],
                'directory' => $rowData['directory'],
                'created_by' => $rowData['created_by']
            );
        }
        return $recordlist;
    }

    public function ifExisint($filename)
    {
        $sql = 'SELECT COUNT(printgen_id) as counter FROM tbl_print_payroll WHERE file_name=:file_name';
        $this->openDB();
        $this->prepareQuery($sql);
        $this->bindQueryParam(':file_name',$filename);
        $result = $this->executeQuery();
        $this->closeDB();
        return $result[0]['counter'];
    }

    public function ifExisintAR($filename)
    {
        $sql = 'SELECT COUNT(printgen_id) as counter FROM tbl_print_ar WHERE file_name=:file_name';
        $this->openDB();
        $this->prepareQuery($sql);
        $this->bindQueryParam(':file_name',$filename);
        $result = $this->executeQuery();
        $this->closeDB();
        return $result[0]['counter'];
    }

    public function renderListPayroll()
    {
        $html = '<div class="space-6"></div>';
        $html .= '<div class="table-responsive">';
        $html .= '<div class="table-header">Generated Payroll</div>';
        $html .= '<div class="space-2"></div>';
        $html .= '<table class="table table-striped table-bordered table-hover">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<td>' . 'Date Created' . '</td>';
        $html .= '<td>' . 'File Name' . '</td>';
        $html .= '<td>' . 'Created By' . '</td>';
        $html .= '<td>' . ' ' . '</td>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';
        foreach($this->generatedList() as $payrollListData){
            $html .= '<tr>';
            $html .= '<td>' . $payrollListData['date_generated'] . '</td>';
            $html .= '<td>' . $payrollListData['file_name'] . '</td>';
            $html .= '<td>' . $payrollListData['created_by'] . '</td>';
            $html .= '<td>' . '<a target="_blank" href="'.$payrollListData['directory'].'/'.$payrollListData['file_name'].'">'.  'Download' . '</a>' . '</td>';
            $html .= '</tr>';
        }
        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '</div>';
        return $html;
    }

    public function renderListAR()
    {
        $html = '<div class="space-6"></div>';
        $html .= '<div class="table-responsive">';
        $html .= '<div class="table-header">Generated Acknowledment Receipt</div>';
        $html .= '<div class="space-2"></div>';
        $html .= '<table class="table table-striped table-bordered table-hover">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<td>' . 'Date Created' . '</td>';
        $html .= '<td>' . 'File Name' . '</td>';
        $html .= '<td>' . 'Created By' . '</td>';
        $html .= '<td>' . ' ' . '</td>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';
        foreach($this->generatedListAR() as $payrollListData){
            $html .= '<tr>';
            $html .= '<td>' . $payrollListData['date_generated'] . '</td>';
            $html .= '<td>' . $payrollListData['file_name'] . '</td>';
            $html .= '<td>' . $payrollListData['created_by'] . '</td>';
            $html .= '<td>' . '<a target="_blank" href="'.$payrollListData['directory'].'/'.$payrollListData['file_name'].'">'.  'Download' . '</a>' . '</td>';
            $html .= '</tr>';
        }
        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '</div>';
        return $html;
    }
}