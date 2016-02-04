<?php

/**
 * Created By: JOSEF FRIEDRICH S. BALDO
 * Date & Time: 1/14/2016 10:21 AM
 */
class payrollPrinting extends DAO
{
    private $region;
    private $province;
    private $municipality;
    private $barangay;
    private $year;
    private $quarter;
    private $signatory1;
    private $signatory2;
    private $signatory3;

    public function setregion($region)
    {
        $this->region = $region;
    }
    public function setprovince($province)
    {
        $this->province = $province;
    }
    public function setmunicipality($municipality)
    {
        $this->municipality = $municipality;
    }
    public function setbarangay($barangay)
    {
        $this->barangay = $barangay;
    }
    public function setyear($year)
    {
        $this->year = $year;
    }
    public function setquarter($quarter)
    {
        $this->quarter = $quarter;
    }

    protected function getregion()
    {
        return $this->region;
    }
    protected function getprovince()
    {
        return $this->province;
    }
    protected function getmunicipality()
    {
        return $this->municipality;
    }
    protected function getbarangay()
    {
        return $this->barangay;
    }
    protected function getyear()
    {
        return $this->year;
    }
    protected function getquarter()
    {
        return $this->quarter;
    }

    public function __construct($region=NULL,$province=NULL,$municipality=NULL,$barangay=NULL,$year=NULL,$quarter=NULL)
    {
        $this->region = $region;
        $this->province = $province;
        $this->municipality = $municipality;
        $this->barangay = $barangay;
        $this->year = $year;
        $this->quarter = $quarter;
    }
    protected function getPensionerByPsgc()
    {
        if ($this->getregion() <> '')
        {
            $region_where = ' WHERE psgc_region = :region';
        }
        else
        {
            $limit_where = ' LIMIT 1';
        }
        if ($this->getprovince() <> '')
        {
            $province_where = ' AND psgc_province = :province';
        }
        if ($this->getmunicipality() <> '')
        {
            $municipality_where = ' AND psgc_municipality = :municipality';
        }
        if ($this->getbarangay() <> '')
        {
            $barangay_where = ' AND psgc_brgy = :barangay';
        }
        $sql = 'SELECT PensionerID,TIMESTAMPDIFF(YEAR,Birthdate,CURDATE()) as `age` FROM tbl_pensioner'.$region_where.$province_where.$municipality_where.$barangay_where.' ORDER BY PensionerID ASC'.$limit_where;
        /*echo '<pre>' . $sql . '</pre>';*/
        $this->openDB();
        $this->prepareQuery($sql);
        if ($this->getregion() <> '')
        {
            $this->bindQueryParam(':region',$this->getregion());
        }
        if ($this->getprovince() <> '')
        {
            $this->bindQueryParam(':province',$this->getprovince());
        }
        if ($this->getmunicipality() <> '')
        {
            $this->bindQueryParam(':municipality',$this->getmunicipality());
        }
        if ($this->getbarangay() <> '')
        {
            $this->bindQueryParam(':barangay',$this->getbarangay());
        }
        $result = $this->executeQuery();
        $recordlist = array();
        $this->closeDB();
        foreach($result as $i => $rowData)
        {
            $recordlist[$i] = $rowData['PensionerID'];
        }
        return $recordlist;
    }

    protected function getPayrollDetails()
    {
        $quarter = $this->getquarter();
        if ($quarter == 1)
        {
            $month1 = 1;
            $month2 = 3;
        }
        elseif($quarter == 2)
        {
            $month1 = 4;
            $month2 = 6;
        }
        elseif($quarter == 3)
        {
            $month1 = 7;
            $month2 = 9;
        }
        elseif($quarter == 4)
        {
            $month1 = 10;
            $month2 = 12;
        }
        else
        {
            $month1 = 0;
            $month2 = 0;
        }
        $pensionersRaw = $this->getPensionerByPsgc();
        $unformatData = "";
        foreach($pensionersRaw as $pensionerID)
        {
            $unformatData .= '"' . $pensionerID . '",';
        }
        $formattedIds = substr($unformatData,0,-1);
        $sql = 'SELECT *,sum(amount) as `total_amount` FROM tbl_pension_payroll WHERE PensionerID IN ('.$formattedIds.') AND PayrollYear = :year AND cMonth >= :month1 AND cMonth <= :month2 GROUP BY PensionerID ORDER BY cMonth ASC';
        /*echo '<pre>' . $sql . '</pre>';*/
        $this->openDB();
        $this->prepareQuery($sql);
        $this->bindQueryParam(':year',$this->getyear());
        $this->bindQueryParam(':month1',$month1);
        $this->bindQueryParam(':month2',$month2);
        $result = $this->executeQuery();
        $recordlist = array();
        $this->closeDB();
        foreach($result as $i => $rowData)
        {
            $PensionerData = $this->getPensionerDetailsByID($rowData['PensionerID']);
            $recordlist[$i] = array(
                'PayrollID' => $rowData['PayrollID'],
                'PensionerID' => $rowData['PensionerID'],
                'PayrollYear' => $rowData['PayrollYear'],
                'cMonth' => $rowData['cMonth'],
                'amount' => $rowData['amount'],
                'total_amount' => $rowData['total_amount'],
                'Approved' => $rowData['Approved'],
                'Claimed' => $rowData['Claimed'],
                'paymentmodeID' => $rowData['paymentmodeID'],
                'name' => $PensionerData['lastname'] . ' ' . $PensionerData['firstname'] . ' ' . $PensionerData['middlename'] . ' ' . $PensionerData['extname'],
                'region' => $PensionerData['psgc_region'],
                'province' => $PensionerData['psgc_province'],
                'municipality' => $PensionerData['psgc_municipality'],
                'brgy' => $PensionerData['psgc_brgy'],
                'Birthdate' => $PensionerData['Birthdate'],
                'age' => $PensionerData['age'],
                'sex' => $PensionerData['sex']
            );
        }
        return $recordlist;
    }

    protected function getPensionerDetailsByID($PensionerID)
    {
        $sql = 'SELECT *,TIMESTAMPDIFF(YEAR,Birthdate,CURDATE()) as `age` FROM tbl_pensioner WHERE PensionerID=:PensionerID';
        $this->openDB();
        $this->prepareQuery($sql);
        $this->bindQueryParam(':PensionerID',$PensionerID);
        $result = $this->executeQuery();
        $this->closeDB();
        $sex = $result[0]['sex'];
        $is_4Ps = $result[0]['is_4ps'];
        $is_abandoned = $result[0]['abandoned'];
        if ($sex == 0)
        {
            $sex_name = 'Male';
        }
        elseif ($sex == 1)
        {
            $sex_name = 'Female';
        }

        if ($is_4Ps == 0)
        {
            $pppp_name = 'No';
        }
        elseif ($is_4Ps == 1)
        {
            $pppp_name = 'Yes';
        }

        if ($is_abandoned == 0)
        {
            $abandoned_name = 'No';
        }
        elseif ($is_abandoned == 1)
        {
            $abandoned_name = 'Yes';
        }
        return array(
            'SeniorID' => $result[0]['SeniorID'],
            'PensionerID' => $result[0]['PensionerID'],
            'InclusionDate' => $result[0]['InclusionDate'],
            'hh_id' => $result[0]['hh_id'],
            'osca_ID' => $result[0]['osca_ID'],
            'PlaceIssued' => $result[0]['PlaceIssued'],
            'DateIssued' => $result[0]['DateIssued'],
            'firstname' => $result[0]['firstname'],
            'middlename' => $result[0]['middlename'],
            'lastname' => $result[0]['lastname'],
            'extname' => $result[0]['extname'],
            'Birthdate' => date("F j, Y",strtotime($result[0]['Birthdate'])),
            'sex' => $sex_name,
            'MaritalID' => $this->p_marital_name($result[0]['MaritalID']),
            'affliationID' => $result[0]['affliationID'],
            'psgc_region' => $this->p_regionName($result[0]['psgc_region']),
            'psgc_province' => $this->p_provinceName($result[0]['psgc_province']),
            'psgc_municipality' => $this->p_municityName($result[0]['psgc_municipality']),
            'psgc_brgy' => $this->p_brgyName($result[0]['psgc_brgy']),
            'given_add' => $result[0]['given_add'],
            'Status' => $this->p_pensioner_status($result[0]['Status']),
            'paymentmodeID' => $this->p_payment_mode($result[0]['paymentmodeID']),
            'ArrangementID' => $this->p_arrangement($result[0]['ArrangementID']),
            'is_4ps' => $pppp_name,
            'abandoned' => $abandoned_name,
            'age' => $result[0]['age']
        );
    }

    protected function get3MonthsName()
    {
        $sql = "select `desc`
			From lib_month
			where QtrID = :quarter";
        $this->openDB();
        $this->prepareQuery($sql);
        $this->bindQueryParam(':quarter', $this->getquarter());
        $result = $this->executeQuery();
        $Months = array();
        $this->closeDB();
        foreach($result as $i=>$row){
            $Months[$i] = strtoupper($row["desc"]);
        }

        return $Months;
    }

    protected function p_regionName($region_code){
        $sql = "SELECT region_name FROM lib_regions WHERE region_code=:region";
        $this->openDB();
        $this->prepareQuery($sql);
        $this->bindQueryParam(':region',$region_code);
        $result = $this->executeQuery();
        $this->closeDB();
        return $result[0]['region_name'];
    }

    protected function p_provinceName($prov_code){
        $sql = "SELECT prov_name FROM lib_provinces WHERE prov_code=:prov";
        $this->openDB();
        $this->prepareQuery($sql);
        $this->bindQueryParam(':prov',$prov_code);
        $result = $this->executeQuery();
        $this->closeDB();
        return $result[0]['prov_name'];
    }

    protected function p_municityName($city_code){
        $sql = "SELECT city_name FROM lib_cities WHERE city_code=:muni_city_psgc";
        $this->openDB();
        $this->prepareQuery($sql);
        $this->bindQueryParam(':muni_city_psgc',$city_code);
        $result = $this->executeQuery();
        $this->closeDB();
        return $result[0]['city_name'];
    }

    protected function p_brgyName($brgy_code){
        $sql = "SELECT brgy_name FROM lib_brgy WHERE brgy_code=:brgy_psgc";
        $this->openDB();
        $this->prepareQuery($sql);
        $this->bindQueryParam(':brgy_psgc',$brgy_code);
        $result = $this->executeQuery();
        $this->closeDB();
        return $result[0]['brgy_name'];
    }

    protected function p_marital_name($id){
        $sql = "SELECT Description FROM lib_civilstatus WHERE MaritalID=:MaritalID";
        $this->openDB();
        $this->prepareQuery($sql);
        $this->bindQueryParam(':MaritalID',$id);
        $result = $this->executeQuery();
        $this->closeDB();
        return $result[0]['Description'];
    }

    protected function p_pensioner_status($id){
        $sql = "SELECT status FROM lib_status WHERE statusID=:statusID";
        $this->openDB();
        $this->prepareQuery($sql);
        $this->bindQueryParam(':statusID',$id);
        $result = $this->executeQuery();
        $this->closeDB();
        return $result[0]['status'];
    }

    protected function p_payment_mode($id){
        $sql = "SELECT description FROM lib_paymentmode WHERE paymentmodeID=:paymentmodeID";
        $this->openDB();
        $this->prepareQuery($sql);
        $this->bindQueryParam(':paymentmodeID',$id);
        $result = $this->executeQuery();
        $this->closeDB();
        return $result[0]['description'];
    }

    protected function p_arrangement($id){
        $sql = "SELECT Description FROM lib_arrangement WHERE ArrangementID=:ArrangementID";
        $this->openDB();
        $this->prepareQuery($sql);
        $this->bindQueryParam(':ArrangementID',$id);
        $result = $this->executeQuery();
        $this->closeDB();
        return $result[0]['Description'];
    }

    public function renderPayroll($classname,$table_name_id,$border_value,$width_percentage,$tr_height)
    {
        $quarter = $this->getquarter();
        if ($quarter == 1)
        {
            $quarter_name = '1st Quarter';
        }
        elseif($quarter == 2)
        {
            $quarter_name = '2nd Quarter';
        }
        elseif($quarter == 3)
        {
            $quarter_name = '3rd Quarter';
        }
        elseif($quarter == 4)
        {
            $quarter_name = '4th Quarter';
        }
        else
        {
            $quarter_name = 'N/A';
        }
        $payrollRaw = $this->getPayrollDetails();
        $html = '<p align="center">';
        $html .= 'Republic of the Philippines' . '<br>';
        $html .= 'Department of Social Welfare and Development' . '<br>';
        $html .= 'SOCIAL PENSION PAYROLL' . '<br>';
        if ($this->getregion() <> '' && $this->getprovince() == '' && $this->getmunicipality() == '')
        {
            $location = $this->p_regionName($this->getregion());
        }
        elseif($this->getregion() <> '' && $this->getprovince() <> '' && $this->getmunicipality() == '')
        {
            $location = $this->p_provinceName($this->getprovince()) . ', ' . $this->p_regionName($this->getregion());
        }
        elseif($this->getregion() <> '' && $this->getprovince() <> '' && $this->getmunicipality() <> '')
        {
            $location = $this->p_municityName($this->getmunicipality()) . ', ' . $this->p_provinceName($this->getprovince()) . ', ' . $this->p_regionName($this->getregion());
        }
        $html .= $location . '<br>';
        $html .= $quarter_name . ', ' . $this->getyear() . '<br>';
        $html .= '</p>';
        $html .= '<div align="center" style="width: 100%">';
        $html .= '<table class="'.$classname.'" id="'.$table_name_id.'" name="'.$table_name_id.'" border="'.$border_value.'px" style="width: '.$width_percentage.'%">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th>' . 'Name' . '</th>';
        $html .= '<th>' . 'PensionerID' . '</th>';
        $html .= '<th>' . 'Date of Birth' . '</th>';
        $html .= '<th>' . 'Age' . '</th>';
        $html .= '<th>' . 'Address' . '</th>';
        $html .= '<th>' . 'Amount' . '</th>';
        $html .= '<th>' . 'Signature/Thumb Mark' . '</th>';
        $html .= '<th>' . 'Date Received' . '</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';
        $totalAmount = '';
        foreach($payrollRaw as $payrollData)
        {
            $html .= '<tr style="height: '.$tr_height.'px;">';
            $html .= '<td>' . $payrollData['name'] . '</td>';
            $html .= '<td>' . $payrollData['PensionerID'] . '</td>';
            $html .= '<td>' . $payrollData['Birthdate'] . '</td>';
            $html .= '<td>' . $payrollData['age'] . '</td>';
            $html .= '<td>' . $payrollData['province'] . ' ' . $payrollData['municipality'] . ' ' . $payrollData['brgy'] . '</td>';
            $html .= '<td>' . $payrollData['total_amount'] . '</td>';
            $html .= '<td>' . '' . '</td>';
            $html .= '<td>' . '' . '</td>';
            $html .= '</tr>';
            $totalAmount += $payrollData['total_amount'];
        }
        $html .= '<tr>' . '<td colspan="5" align="right">Sub-Total</td>' . '<td colspan="3">' . '<strong>' . '<span style="font-size:x-large">' . 'Php' . number_format($totalAmount,2) . '</span>' . '</strong>' . '</td>' . '</tr>';
        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '</div>';
        return $html;
    }

    public function renderAcknowledmentReceipt()
    {
        $quarter = $this->getquarter();
        if ($quarter == 1)
        {
            $quarter_name = '1st Quarter';
        }
        elseif($quarter == 2)
        {
            $quarter_name = '2nd Quarter';
        }
        elseif($quarter == 3)
        {
            $quarter_name = '3rd Quarter';
        }
        elseif($quarter == 4)
        {
            $quarter_name = '4th Quarter';
        }
        else
        {
            $quarter_name = 'N/A';
        }
        if ($this->getregion() <> '' && $this->getprovince() == '' && $this->getmunicipality() == '')
        {
            $location = $this->p_regionName($this->getregion());
        }
        elseif($this->getregion() <> '' && $this->getprovince() <> '' && $this->getmunicipality() == '')
        {
            $location = $this->p_provinceName($this->getprovince()) . ', ' . $this->p_regionName($this->getregion());
        }
        elseif($this->getregion() <> '' && $this->getprovince() <> '' && $this->getmunicipality() <> '')
        {
            $location = $this->p_municityName($this->getmunicipality()) . ', ' . $this->p_provinceName($this->getprovince()) . ', ' . $this->p_regionName($this->getregion());
        }
        $payrollRaw = $this->getPayrollDetails();
        $html = '<style>
                    .custombody {
                        font-family: "CALIBRI";
                        /*height: 3525px;*/
                        width: 100%;
                        border: 1px solid #000000;
                    }
                    table{
                        width: 100%;
                        /*border: 1px solid #000000;*/
                    }
                    .widthClass{
                        text-align: center;
                    }
                    .dswdFontSize{
                        font-size: larger;
                        font-weight: bolder;
                    }
                </style>';
        foreach($payrollRaw as $payrollData):
        $html .= '<body>
                    <table class="custombody"><tr><td>
                                <table>
                                    <tr height="35px">
                                        <td></td>
                                    </tr>
                                </table>
                                <table>
                                    <tr>
                                        <td class="widthClass"><span class="dswdFontSize">Department of Social Welfare and Development</span><br>
                                            Social Pension for Indigent Senior Citizens<br>
                                            '.$location.'
                                        </td>
                                    </tr>
                                </table>
                                <table>
                                    <tr height="75px">
                                        <td></td>
                                    </tr>
                                </table>
                                <table>
                                    <tr align="center"><td><h1>ACKNOWLEDGEMENT RECEIPT</h1></td></tr>
                                </table>
                                <table>
                                    <tr height="75px">
                                        <td></td>
                                    </tr>
                                </table>
                                <table>
                                    <tr>
                                        <td>Name</td><td>'.$payrollData['name'].'</td><td>OSCA ID: ___________________________</td>
                                    </tr>
                                    <tr>
                                        <td>Address</td><td>'.$payrollData['brgy'].'</td><td>HOUSEHOLD ID: ___________________________</td>
                                    </tr>
                                    <tr>
                                        <td>Municipality</td><td>'.$payrollData['municipality'].'</td><td></td>
                                    </tr>
                                    <tr>
                                        <td>Province</td><td>'.$payrollData['province'].'</td><td></td>
                                    </tr>
                                    <tr>
                                        <td>Sex</td><td>'.$payrollData['sex'].'</td><td></td>
                                    </tr>
                                    <tr>
                                        <td></td><td></td><td>Not valid if erasures or alteration are present</td>
                                    </tr>
                                </table>
                                <table>
                                    <tr height="75px">
                                        <td></td>
                                    </tr>
                                </table>
                                <table>
                                    <tr>
                                        <td>Request for Payment for the<br>'.$quarter_name.'</td><td width="350px"></td><td>Please check the following box corresponding<br>to the amount received</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Refer to:<br>
                                            Payroll for '.$payrollData['region'].'<br>
                                            '.$payrollData['brgy'].', '.$payrollData['municipality'].', '.$payrollData['province'].'<br>
                                            Pensioner Number '.$payrollData['PensionerID'].'
                                        </td>
                                        <td width="350px"></td>
                                        <td>';
                                        foreach($this->get3MonthsName() as $ThreeMonths){
                                            $html .= $ThreeMonths . '&nbsp;[&nbsp;&nbsp;&nbsp;]<br>';
                                        }
            $html .= '</td>
                                    </tr>
                                    <tr>
                                        <td></td><td width="350px"></td><td>Total: <u>'.'Php'.number_format($payrollData['total_amount'],2).'</u></td>
                                    </tr>
                                </table>
                                <table>
                                    <tr height="75px">
                                        <td></td>
                                    </tr>
                                </table>
                                <table>
                                    <tr>
                                        <td>Petsa_________________</td>
                                    </tr>
                                    <tr height="20px">
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>Ito ay nagpapatunay na nakatanggap ako mula sa ________________________________ ng perang nagkakahalaga<br>
                                            ng <u>'.'Php'.number_format($payrollData['total_amount'],2).'</u> para sa aking pension sa mga buwan ng';
                                            foreach($this->get3MonthsName() as $ThreeMonths){
                                                $html .= ' ' . $ThreeMonths . ' ';
                                            }
            $html .= $this->getyear() . ' ';
            $html .=                    '</td>
                                    </tr>
                                </table>
                                <table>
                                    <tr height="150px">
                                        <td></td>
                                    </tr>
                                </table>
                                <table>
                                    <tr>
                                        <td>Binayaran Ni</td><td width="350px"></td><td>Tinanggap Ni</td>
                                    </tr>
                                    <tr height="50px">
                                        <td></td><td width="350px"></td><td></td>
                                    </tr>
                                    <tr>
                                        <td>_____________________________</td><td width="350px"></td><td>'.$payrollData['name'].'</td>
                                    </tr>
                                    <tr>
                                        <td>Pangalan at Lagda ng Tagadala</td><td width="350px"></td><td>Lagda/Thumbmark ng Benepisaryo </td>
                                    </tr>
                                    <tr>
                                        <td>Sinaksihan Ni</td><td width="350px"></td><td></td>
                                    </tr>
                                    <tr height="50px">
                                        <td></td><td width="350px"></td><td></td>
                                    </tr>
                                    <tr>
                                        <td>_____________________________</td><td width="350px"></td><td></td>
                                    </tr>
                                    <tr>
                                        <td>Lagda/Thumbmark ng Authorized Representative/<br>Baranggay Captain</td><td width="350px"></td><td></td>
                                    </tr>
                                </table>
                                <table>
                                    <tr>
                                        <td>Note: In the absence of the authorized representative, Barangay Captain may sign as witness on payout.</td>
                                    </tr>
                                </table>
                                <table>
                                    <tr height="35px">
                                        <td></td>
                                    </tr>
                                </table>
                            </td></tr></table>
                    </body>';
            endforeach;
        return $html;
    }
}