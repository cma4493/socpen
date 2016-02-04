<?php

/**
 * Created By: JOSEF FRIEDRICH S. BALDO
 * Date & Time: 1/7/2016 4:49 PM
 */
class psgcclass extends DAO
{
    private $region;
    private $province;
    private $muni_city;
    private $brgy;

    function setregion($region){
        $this->region = $region;
    }
    function setprovince($province){
        $this->province = $province;
    }
    function setmuni_city($muni_city){
        $this->muni_city = $muni_city;
    }
    function setbrgy($brgy){
        $this->brgy = $brgy;
    }

    protected function getregion(){
        return $this->region;
    }
    protected function getprovince(){
        return $this->province;
    }
    protected function getmuni_city(){
        return $this->muni_city;
    }
    protected function getbrgy(){
        return $this->brgy;
    }

    function paramGetters($region,$province,$muni_city,$brgy){
        $this->region = $region;
        $this->province = $province;
        $this->muni_city = $muni_city;
        $this->brgy = $brgy;
    }

    function _regionName(){
        $sql = "SELECT region_name FROM lib_regions WHERE region_code=:region";
        $this->openDB();
        $this->prepareQuery($sql);
        $this->bindQueryParam(':region',$this->getregion());
        $result = $this->executeQuery();
        $this->closeDB();
        return $result[0]['region_name'];
    }

    function _provinceName(){
        $sql = "SELECT prov_name FROM lib_provinces WHERE prov_code=:prov";
        $this->openDB();
        $this->prepareQuery($sql);
        $this->bindQueryParam(':prov',$this->getprovince());
        $result = $this->executeQuery();
        $this->closeDB();
        return $result[0]['prov_name'];
    }

    function _municityName(){
        $sql = "SELECT city_name FROM lib_cities WHERE city_code=:muni_city_psgc";
        $this->openDB();
        $this->prepareQuery($sql);
        $this->bindQueryParam(':muni_city_psgc',$this->getmuni_city());
        $result = $this->executeQuery();
        $this->closeDB();
        return $result[0]['city_name'];
    }

    function _brgyName(){
        $sql = "SELECT brgy_name FROM lib_brgy WHERE brgy_code=:brgy_psgc";
        $this->openDB();
        $this->prepareQuery($sql);
        $this->bindQueryParam(':brgy_psgc',$this->getbrgy());
        $result = $this->executeQuery();
        $this->closeDB();
        return $result[0]['brgy_name'];
    }

    protected function getLibRegion()
    {
        $sql = 'SELECT * FROM lib_regions';
        $this->openDB();
        $this->prepareQuery($sql);
        $result = $this->executeQuery();
        $recordlist = array();
        $this->closeDB();
        foreach($result as $i => $rowData)
        {
            $recordlist[$i] = array(
                'region_code' => $rowData['region_code'],
                'region_name' => $rowData['region_name']
            );
        }
        return $recordlist;
    }

    protected function getLibProv($region_code)
    {
        $sql = 'SELECT prov_code,prov_name FROM lib_provinces WHERE region_code=:region_code';
        $this->openDB();
        $this->prepareQuery($sql);
        $this->bindQueryParam(':region_code',$region_code);
        $result = $this->executeQuery();
        $recordlist = array();
        $this->closeDB();
        foreach($result as $i => $rowData)
        {
            $recordlist[$i] = array(
                'prov_code' => $rowData['prov_code'],
                'prov_name' => $rowData['prov_name']
            );
        }
        return $recordlist;
    }

    protected function getLibCity($prov_code)
    {
        $sql = 'SELECT city_code,city_name FROM lib_cities WHERE prov_code=:prov_code';
        $this->openDB();
        $this->prepareQuery($sql);
        $this->bindQueryParam(':prov_code',$prov_code);
        $result = $this->executeQuery();
        $recordlist = array();
        $this->closeDB();
        foreach($result as $i => $rowData)
        {
            $recordlist[$i] = array(
                'city_code' => $rowData['city_code'],
                'city_name' => $rowData['city_name']
            );
        }
        return $recordlist;
    }

    protected function getLibBrgy($city_code)
    {
        $sql = 'SELECT brgy_code,brgy_name FROM lib_brgy WHERE city_code=:city_code';
        $this->openDB();
        $this->prepareQuery($sql);
        $this->bindQueryParam(':city_code',$city_code);
        $result = $this->executeQuery();
        $recordlist = array();
        $this->closeDB();
        foreach($result as $i => $rowData)
        {
            $recordlist[$i] = array(
                'brgy_code' => $rowData['brgy_code'],
                'brgy_name' => $rowData['brgy_name']
            );
        }
        return $recordlist;
    }


    public function regionoption($select_name_id = NULL,$onchange = NULL)
    {
        $html = '<select id="'.$select_name_id.'" name="'.$select_name_id.'" onchange="'.$onchange.'">';
        $html .= '<option value="" selected>Please Select Region</option>';
        foreach($this->getLibRegion() as $SupportData){
            $html .= '<option value="' . $SupportData['region_code'] . '">' . $SupportData['region_name'] . '</option>';
        }
        $html .= '</select>';
        return $html;
    }

    public function provinceoption($region_code = 0,$select_name_id = NULL,$onchange = NULL)
    {
        $html = '<select id="'.$select_name_id.'" name="'.$select_name_id.'" onchange="'.$onchange.'">';
        $html .= '<option value="" selected>Please Select Province</option>';
        foreach($this->getLibProv($region_code) as $SupportData){
            $html .= '<option value="' . $SupportData['prov_code'] . '">' . $SupportData['prov_name'] . '</option>';
        }
        $html .= '</select>';
        return $html;
    }

    public function cityoption($prov_code,$select_name_id = NULL,$onchange = NULL)
    {
        $html = '<select id="'.$select_name_id.'" name="'.$select_name_id.'" onchange="'.$onchange.'">';
        $html .= '<option value="" selected>Please Select City</option>';
        foreach($this->getLibCity($prov_code) as $SupportData){
            $html .= '<option value="' . $SupportData['city_code'] . '">' . $SupportData['city_name'] . '</option>';
        }
        $html .= '</select>';
        return $html;
    }

    public function brgyoption($city_code,$select_name_id = NULL,$onchange = NULL)
    {
        $html = '<select id="'.$select_name_id.'" name="'.$select_name_id.'" onchange="'.$onchange.'">';
        $html .= '<option value="" selected>Please Select Barangay</option>';
        foreach($this->getLibBrgy($city_code) as $SupportData){
            $html .= '<option value="' . $SupportData['brgy_code'] . '">' . $SupportData['brgy_name'] . '</option>';
        }
        $html .= '</select>';
        return $html;
    }

    public function psgc_all_options()
    {
        $html = $this->regionoption('jb_region_code','getProv(this.value)');
        $html .= '<div id="div_prov">
                        <select id="jb_prov_code" name="jb_prov_code">
                            <option value="" selected>Please Select Province</option>
                        </select>
                    </div>';
        $html .= '<div id="div_city">
                        <select id="jb_city_code" name="jb_city_code">
                            <option value="" selected>Please Select City</option>
                        </select>
                    </div>';
        $html .= '<div id="div_brgy">
                        <select id="jb_brgy_code" name="jb_brgy_code">
                            <option value="" selected>Please Select Barangay</option>
                        </select>
                    </div>';
        return $html;
    }
}