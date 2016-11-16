<?php

/*

* Commercial Codebase by WP Realty - RETS PRO Development Team.

* Copyright - WP Realty - RETS PRO - 2009 - 2016 - All Rights Reserved

* License: http://retspro.com/faq/license/

*/

Class SearchEngineClass

{

    public $tab_field_type;

    public $tab_input_option;

    public $options_type_tab;

    function __construct()

    {

        $this->tab_field_type = array(

            "text" => "Text",

            "list" => "List",

            "min" => "Minimum",

            "max" => "Maximum",

            "minmax" => "Min/Max",

            "daterange" => "Date range",

            "check" => "Check",

            "multiselect" => "Multi select"

        );

        $this->tab_input_option = array(

            "equal" => "Equal",

            "like" => "Like",

            "less" => "Less",

            "greater" => "Greater",

            "less_or_equal" => "Less or Equal",

            "greater_or_equal" => "Greater or Equal"

        );

        $this->options_type_tab = array(

            "0" => "Value|Value",

            "1" => "Key|Value"

        );

    }

    function GetEngines($parent_id, $remotedb = false)

    {

        global $dbClass, $config;

        $sql = "SELECT * FROM `" . $config['table_prefix'] . "searchengines` WHERE parent_id='" . $parent_id . "' ORDER by tab_rank";

# RemoteDb flag added

        $reEngines = $dbClass->query($sql, $remotedb);

        if ($reEngines->RecordCOunt() > 0) {

            $ret = array();

            while (!$reEngines->EOF) {

                $ret[] = $reEngines->fields;

                $reEngines->MoveNext();

            }

            return $ret;

        } else

            return false;

    }

    function GenerateSearchEngine($param, $request)

    {

        global $config, $dbClass, $UrlClass;

        if (is_numeric($param['id'])) {

            $se_id = $param['id'];

# RemoteDb flag added

            if (!$searchEngines = $this->GetEngines($se_id, $param['masterdb'])) {

                return false;

            }

            $se_content = array();

            $se_headers = array();

            for ($counter = 0; $counter < count($searchEngines); $counter++) {

                $content = "";

                $se_info = $this->GetSearchEngineInfo($searchEngines[$counter]['id'], $param['masterdb']);

                $options_se = json_decode($se_info['options'], true);

                $se_id = $se_info['id'];

                if ($se_info !== false) {

                    $sql = "SELECT * FROM wp_searchenginescontent WHERE searchengines_id='" . $se_info['id'] . "' ORDER by field_rank_col,field_rank";

# RemoteDb flag added

                    $reFields = $dbClass->Query($sql, $param['masterdb']);
                    
                    if ($reFields->recordCount() > 0) {

                        require_once($config['wpradmin_basepath'] . 'include/forms.inc.php');

                        $formsClass = registry::register('FormsClass');

//$action = $config['baseurl']."index.php";

                        $action = $UrlClass->selfURL();

                        $content .= $formsClass->startform($action, array(

                            'method' => 'get',

                            'onSubmit' => 'readySearchForm(this);'

                        ));

                        $content .= $formsClass->create_hidden('page', $param['page']);

                        $parseClass = registry::register('parseClass');

                        $tab_fields = array();

                        $tab_fields_num = array();

                        while (!$reFields->EOF) {

                            $tab_fields[$reFields->fields['field_name']] = $reFields->fields;

                            $tab_fields_num[] = $reFields->fields;

                            $reFields->MoveNext();

                        }

                        if ($se_info['template'] != '') {

                            $template = $parseClass->GetTemplate($config['basepath'] . $config['template_dir'] . "/" . $se_info['template']);

                        } else {

//stanadart template

                            $template = "";

                            $left_col = "";

                            $right_col = "";

                            for ($i = 0; $i < count($tab_fields_num); $i++) {

                                $styles = "";

                                $side = 'left';

                                if ($tab_fields_num[$i]['field_rank_col'] == 1)

                                    $side = 'right';

                                if ($tab_fields_num[$i]['field_height'] > 0)

                                    $styles .= " height='" . $tab_fields_num[$i]['field_height'] . "'";

                                elseif ($options_se[$side . "_" . $tab_fields_num[$i]['field_type'] . "_height"] > 0)

                                    $styles .= " height='" . $options_se[$side . "_" . $tab_fields_num[$i]['field_type'] . "_height"] . "'";

                                if ($tab_fields_num[$i]['field_width'] > 0)

                                    $styles .= " width='" . $tab_fields_num[$i]['field_width'] . "'";

                                elseif ($options_se[$side . "_" . $tab_fields_num[$i]['field_type'] . "_width"] > 0)

                                    $styles .= " width='" . $options_se[$side . "_" . $tab_fields_num[$i]['field_type'] . "_width"] . "'";

                                if ($tab_fields_num[$i]['field_rank_col'] == 1)

                                    $right_col .= "<strong>{searchenginefield_caption field_name='" . $tab_fields_num[$i]['field_name'] . "'}</strong>

<div style='margin-left:10px'>{searchenginefield_generate field_name='" . $tab_fields_num[$i]['field_name'] . "' $styles}</div>";

                                else

                                    $left_col .= "<strong>{searchenginefield_caption field_name='" . $tab_fields_num[$i]['field_name'] . "'}</strong>

<div style='margin-left:10px'>{searchenginefield_generate field_name='" . $tab_fields_num[$i]['field_name'] . "' $styles}</div>";

                            }

//$options_se

                            $style_left = "";

                            if (is_numeric($options_se['left_column_width']))

                                $style_left = "style='width:" . $options_se['left_column_width'] . "px;'";

                            $style_right = "float:left;";

                            if (is_numeric($options_se['right_column_width']))

                                $style_right = "style='width:" . $options_se['right_column_width'] . "px;'";

                            $template = "<table padding='5'><tr valign='top'><td $style_left>" . $left_col . "</td><td $style_right>" . $right_col . "</td></tr></table>";

                            $template .= "<div>{searchengine submit}</div>";

                        }

                        $reg_searchengine = "#{searchenginefield_(\w+)(?:\s([^}]*))?}#";

                        $reg_param = "#(\w+)\s*=(?:'([^']*)'|\"([^\"]*)\")#";

                        if (preg_match_all($reg_searchengine, $template, $matches)) {

                            for ($i = 0; $i < count($matches[0]); $i++) {

                                $params = array();

                                if (!empty($matches[2][$i])) {

                                    if (preg_match_all($reg_param, $matches[2][$i], $matches_params)) {

                                        for ($i_p = 0; $i_p < count($matches_params[0]); $i_p++) {

                                            if ($matches_params[2][$i_p] != "")

                                                $params[$matches_params[1][$i_p]] = $matches_params[2][$i_p];

                                            else

                                                $params[$matches_params[1][$i_p]] = $matches_params[3][$i_p];

                                        }

                                    }

                                }

                                if (isset($params['field_name'])) {

                                    $field = $tab_fields[$params['field_name']];

                                    $field_value = "";

                                    if (isset($request[$field['field_name']]))

                                        $field_value = $request[$field['field_name']];

                                    if ($matches[1][$i] == 'caption')

                                        $template = str_replace($matches[0][$i], $field['field_caption'], $template);

                                    if ($matches[1][$i] == 'generate') {

                                        $mode = "explode";

                                        $styles = "";

                                        if ($field['field_type'] == 'multiselect') {

                                            if ($params['height'] > 0)

                                                $styles .= " height:" . $params['height'] . "px;";

                                        }

                                        if ($field['field_type'] != 'check') {

                                            if ($params['width'] > 0)

                                                $styles .= " width:" . $params['width'] . "px;";

                                        }

                                        $aparams = array();

                                        $aparams['id'] = "se_" . $params['field_name'];

                                        if ($styles != "")

                                            $aparams['style'] = $styles;

                                        if ($field['field_type'] == 'text' OR $field['field_type'] == 'min' OR $field['field_type'] == 'max') {

                                            $input = $formsClass->GenerateField('text', $field['field_name'], $field_value, $field['field_value'], $request, $mode, $aparams);

                                        } elseif ($field['field_type'] == 'list') {

                                            $field_value = $field['field_value'];

                                            $tab = explode("|", $field['field_value']);

                                            $ntab = array();

                                            $ntab['Any'] = ""; //adds an "Any" option

                                            for ($cnt_tab = 0; $cnt_tab < count($tab); $cnt_tab++) {

//$ntab[base64_encode($tab[$cnt_tab])] = $tab[$cnt_tab];

                                                $ntab[$tab[$cnt_tab]] = $tab[$cnt_tab];

                                            }

                                            $input = $formsClass->create_select($field['field_name'], $field_value, $aparams, $ntab);

                                        } elseif ($field['field_type'] == 'minmax') {

                                            $input = $formsClass->create_text($field['field_name'] . "_from", '', $aparams);

                                            $input .= " to " . $formsClass->create_text($field['field_name'] . "_to", '', $aparams);

                                        } elseif ($field['field_type'] == 'daterange') {

                                            $input = $formsClass->create_text($field['field_name'] . "_from", '', $aparams);

                                            $input .= " to " . $formsClass->create_text($field['field_name'] . "_to", '', $aparams);

                                        } elseif ($field['field_type'] == 'check') {

                                            $field_value = $field['field_value'];

                                            $tab = explode("|", $field['field_value']);

                                            $ntab = array();

                                            $input = "";

                                            for ($cnt_tab = 0; $cnt_tab < count($tab); $cnt_tab++) {

//$ntab[base64_encode($tab[$cnt_tab])] = $tab[$cnt_tab];

//$input .= $formsClass->create_checkbox($field['field_name']."[]",base64_encode($tab[$cnt_tab])).$tab[$cnt_tab]."<br/>";

                                                $input .= $formsClass->create_checkbox($field['field_name'] . "[]", $tab[$cnt_tab]) . $tab[$cnt_tab] . "<br/>";

                                            }

                                        } elseif ($field['field_type'] == 'multiselect') {

                                            $field_value = $field['field_value'];

                                            $tab = explode("|", $field['field_value']);

                                            $ntab = array();

                                            $ntab['Any'] = ""; //adds an "Any" option

                                            for ($cnt_tab = 0; $cnt_tab < count($tab); $cnt_tab++) {

//$ntab[base64_encode($tab[$cnt_tab])] = $tab[$cnt_tab];

                                                $ntab[$tab[$cnt_tab]] = $tab[$cnt_tab];

                                            }

                                            $input = $formsClass->create_multiselect($field['field_name'], $field_value, $aparams, $ntab);

                                        }

                                        if ($input !== false)

                                            $template = str_replace($matches[0][$i], $input, $template);

                                    }

                                }

                            }

                        }

                        $template = $parseClass->ReplaceTag("{searchengine submit}", $formsClass->create_submit('', 'Search', '', false, true), $template);

                        $content .= $template;

                        $content .= $formsClass->create_hidden('searchengine' . $se_id . '_validate', 'yes', '');

                        $content .= $formsClass->endform();

                    }

                }

                $key = "seTab_" . $se_info['id'];

                $se_content[$key] = $content;

                if ($se_info['tab_caption'] != "")

                    $se_headers[$key] = $se_info['tab_caption'];

                else

                    $se_headers[$key] = "&nbsp;";

            }

//tabs
            if (count($se_content) > 1) {

                global $presentationClass, $config;

                return $presentationClass->JqueryTabsWithData($se_content, $se_headers, array());

            } else {

                return $content;

            }

        } else

            return false;

    }

    function ChooseOption($option, $field, $value, $debug = false)

    {

        $sql = false;

        switch ($option) {

            case 'text':

                $sql = $field . " LIKE '%" . str_replace(" ", "%", $value) . "%'";

                break;

            case 'min':

                if (is_numeric($value))

                    $sql = $field . " < " . $value;

                break;

            case 'max':

                if (is_numeric($value))

                    $sql = $field . " > " . $value;

                break;

            case 'less_or_equal':

// if(is_numeric($value))

                if (!empty($value))

                    $sql = $field . " <= '" . $value . "'";

                break;

            case 'greater_or_equal':

// if(is_numeric($value))

                if (!empty($value))

                    $sql = $field . " >= '" . $value . "'";

                break;

// Added to Fix ARRAY Search 06-15-2012

            case 'check':

            case 'multiselect':

                $sql = " find_in_set('{$value}',{$field}) <> 0 OR find_in_set(' {$value}',{$field}) <> 0 ";

                break;

            default: //equal

                $sql = $field . " = '" . $value . "'";

                break;

        }

        return $sql;

    }

    function GenerateSQL($se_id, $request_vars, $se_info, $count_bool = false, $remotedb = false)

    {

//echo 'SEid: '.$se_id.'<br>';

//var_dump($request_vars);

//echo '<br>';

//var_dump($se_info);

        global $dbClass, $config;

        unset($request_vars["page"], $request_vars["searchengine2_validate"], $request_vars["successinfo"], $request_vars["errorinfo"]);

        $sql_tab = array();

        $counter = 0;

        $CRITERIA_EXISTS = count($request_vars) > 0;

        $request_fields = array();

        foreach (array_keys($request_vars) as $key) {

            $key = preg_replace('~_from|_to~i', '', $key);

            $request_fields[$key] = 1;

        }

        if ($CRITERIA_EXISTS) {

            $sql = "SELECT * FROM " . $config['table_prefix'] . "searchenginescontent WHERE searchengines_id='" . $se_id . "'";

# RemoteDb flag added

            $reFields = $dbClass->Query($sql, $remotedb);

            if ($reFields->recordCount() > 0) {

                while (!$reFields->EOF) {

                    $break = false;

                    if (array_key_exists($reFields->fields['field_name'], $request_fields)) {

                        $break = ($reFields->fields['field_type'] == 'select' AND $request_vars[$reFields->fields['field_name']] == 'select');

                    } else {

                        $break = true;

                    }

# RemoteDb flag added

                    if ($break !== true AND ($dbClass->ColumnExists($config['table_prefix'] . 'listingsdb', $reFields->fields['field_in_table'], $col_param, $remotedb) OR $reFields->fields['field_in_table'] == 'class_name'

//OR $reFields->fields['field_type']=='minmax'

                        )
                    ) {

                        $sql = false;

                        if ($reFields->fields['field_type'] == 'check' OR $reFields->fields['field_type'] == 'multiselect') {

//$tab_option = $reFields->fields['field_options'];

                            /*if($reFields->fields['field_options_type']==1)

                            {

                            $temp = explode("|",$reFields->fields['field_options']);

                            for($i=0;$i<count($temp);$i=$i+2)

                            {

                            $tab_option[$temp[$i]] = $temp[$i+1];

                            }

                            }else

                            {

                            */

                            $temp = explode("|", $reFields->fields['field_value']);

                            for ($i = 0; $i < count($temp); $i++) {

                                $tab_option[$temp[$i]] = $temp[$i];

                            }

//}

                            $sql_checkbox = array();

                            if ($reFields->fields['field_in_table'] == 'class_name') {

                                require_once($config['wpradmin_basepath'] . "include/class.inc.php");

                                $pclassClass = registry::register('pclassClass');

                                if ($classes = $pclassClass->GetClasses(true)) {

                                    if (isset($request_vars[$reFields->fields['field_name']]) && is_array($request_vars[$reFields->fields['field_name']])) {

                                        foreach ($request_vars[$reFields->fields['field_name']] as $tab_key => $tab_value) {

                                            if ($class_id = array_search($tab_value, $classes)) {

                                                $sql_t = $this->ChooseOption($reFields->fields['field_type'], 'LM.class_id', $class_id);

                                                if ($sql_t !== false)

                                                    $sql_checkbox[] = $sql_t;

                                            }

                                        }

                                    }

                                }

                            } else {

                                foreach ($tab_option as $tab_key => $tab_value) {

                                    if (isset($request_vars[$reFields->fields['field_name']]) && is_array($request_vars[$reFields->fields['field_name']])) {

//$fieldValue = base64_decode($request_vars[$reFields->fields['field_name']]);

//if(in_array(base64_encode($tab_key),$request_vars[$reFields->fields['field_name']]))

                                        if (in_array($tab_key, $request_vars[$reFields->fields['field_name']])) {

                                            $sql_t = $this->ChooseOption($reFields->fields['field_type'], 'LM.' . $reFields->fields['field_in_table'], $tab_key);

                                            if ($sql_t !== false)

                                                $sql_checkbox[] = $sql_t;

                                        }

                                    }

                                }

                            }

                            if (count($sql_checkbox) > 0) {

                                $sql = "(";

                                for ($i = 0; $i < count($sql_checkbox); $i++) {

                                    $sql .= $sql_checkbox[$i];

                                    if ($i < count($sql_checkbox) - 1)

                                        $sql .= " OR ";

                                }

                                $sql .= ")";

                            }

                        } elseif ($reFields->fields['field_type'] == 'minmax') {

                            $sql = "(";

                            $value = $request_vars[$reFields->fields['field_name'] . "_from"];

                            $sql_a = $this->ChooseOption('greater_or_equal', 'LM.' . $reFields->fields['field_in_table'], $value);

                            $value = $request_vars[$reFields->fields['field_name'] . "_to"];

                            $sql_b = $this->ChooseOption('less_or_equal', 'LM.' . $reFields->fields['field_in_table'], $value);

                            if ($sql_b !== false AND $sql_a === false) {

                                $sql = $sql_b;

                            } elseif ($sql_b === false AND $sql_a !== false) {

                                $sql = $sql_a;

                            } elseif ($sql_a === false AND $sql_b === false) {

                                $sql = false;

                            } elseif ($sql_a !== false AND $sql_b !== false)

                                $sql = "(" . $sql_a . " AND " . $sql_b . ")";

                        } elseif ($reFields->fields['field_type'] == 'daterange') {

                            global $config;

//require_once($config['wpradmin_basepath'] . "include/controlpanel.inc.php");

//$control     = registry::register('controlpanelClass');

                            $control = new controlpanelClass();

                            $date_format = $control->GetDateFormatField();

                            $formats = array(

                                "",

                                "%m/%d/%Y",

                                "%Y/%d/%m",

                                "%d/%m/%Y",

                                "%Y/%m/%d"

                            );

                            $selected_format = isset($formats[$date_format]) ? $formats[$date_format] : $formats[1];

//controlpanelfield dateformat

                            $from = $this->parseDate($request_vars[$reFields->fields['field_name'] . "_from"], $selected_format);

                            $sql_a = $this->ChooseOption('greater_or_equal', 'LM.' . $reFields->fields['field_in_table'], $from);

                            $to = $this->parseDate($request_vars[$reFields->fields['field_name'] . "_to"], $selected_format);

                            $sql_b = $this->ChooseOption('less_or_equal', 'LM.' . $reFields->fields['field_in_table'], $to);

                            if ($sql_b !== false AND $sql_a === false) {

                                $sql = $sql_b;

                            } elseif ($sql_b === false AND $sql_a !== false) {

                                $sql = $sql_a;

                            } elseif ($sql_a === false AND $sql_b === false) {

                                $sql = false;

                            } elseif ($sql_a !== false AND $sql_b !== false) {

                                $sql = "(" . $sql_a . " AND " . $sql_b . ")";

                            }

                        } else {

                            if ($reFields->fields['field_in_table'] == 'class_name') {

                                require_once($config['wpradmin_basepath'] . "include/class.inc.php");

                                $pclassClass = registry::register('pclassClass');

                                $classes = $pclassClass->GetClasses(true);

                                $class_id = array_search($request_vars[$reFields->fields['field_name']], $classes);

//var_dump($classes);

                                $sql = $this->ChooseOption($reFields->fields['field_type'], 'LM.class_id', $class_id);

                            } else {

// if($reFields->fields['field_type']=='list')

// $value = base64_decode($value);

                                $value = $request_vars[$reFields->fields['field_name']];

                                if ($value != "") {
                                    if ($reFields->fields['field_name'] == "Zip") {
                                        $sql = 'LM.Zip IN (' . $value . ')';
                                    } else {
                                        $sql = $this->ChooseOption($reFields->fields['field_type'], 'LM.' . $reFields->fields['field_in_table'], $value);
                                    }
                                }
                            }

                        }

                        if ($sql !== false) {

                            $sql_tab[$counter] = $sql;

                            $counter++;

                        }

                    }

                    $reFields->MoveNext();

                }

            }

        }

        if ($se_info['class_id'] != 0) {

            $sql_tab[] = " class_id='" . $se_info['class_id'] . "'";

        }

/////////////////////////////////////////////////////////////////////////////////

//quick set for pricerange

        if (isset($_GET['pricerange'])) {

//this is a price range

            $price_array = explode(':', $_GET['pricerange']);

            $sql_minprice = $this->ChooseOption('greater_or_equal', 'LM.price', $price_array[0]);

            $sql_maxprice = $this->ChooseOption('less_or_equal', 'LM.price', $price_array[1]);

            $sql_tab[] = "(" . $sql_minprice . " AND " . $sql_maxprice . ") ";

        }

//////////////////////////////////////////////////////////////////////

        if (count($sql_tab) > 0) {

            if ($count_bool === false) {

                $generate_sql = "SELECT * FROM " . $config['table_prefix'] . "listingsdb AS LM " . " LEFT JOIN " . $config['table_prefix'] . "agents AS LA ON LM.agent_code = LA.agent_code " . " LEFT JOIN " . $config['table_prefix'] . "offices AS LO ON LM.office_code = LO.office_code " . " LEFT JOIN " . $config['table_prefix'] . "class AS LC ON LM.class_id = LC.class_id " . "WHERE ";

            } else {

                $generate_sql = "SELECT count(listingsdb_id) as count FROM " . $config['table_prefix'] . "listingsdb AS LM WHERE ";

            }

            for ($i = 0; $i < count($sql_tab); $i++) {

                $generate_sql .= $sql_tab[$i];

                if ($i < count($sql_tab) - 1)

                    $generate_sql .= " AND ";

            }

            return $generate_sql;

        } else {

            if ($count_bool === false) {

                $generate_sql = "SELECT * FROM " . $config['table_prefix'] . "listingsdb AS LM " . " LEFT JOIN " . $config['table_prefix'] . "agents AS LA ON LM.agent_code = LA.agent_code " . " LEFT JOIN " . $config['table_prefix'] . "offices AS LO ON LM.office_code = LO.office_code " . " LEFT JOIN " . $config['table_prefix'] . "class AS LC ON LM.class_id = LC.class_id ";

            } else {

                $generate_sql = "SELECT count(1) as count FROM " . $config['table_prefix'] . "listingsdb AS LM ";

            }

            return $generate_sql;

        }

        return false;

    }

    function GetSearchEngineInfo($id, $remotedb = false)

    {

        global $dbClass, $config;

        $sql = "SELECT * FROM " . $config['table_prefix'] . "searchengines WHERE id='" . $id . "'";

# RemoteDb flag added

        $row = $dbClass->Query($sql, $remotedb);

        if ($row->recordCount() > 0)

            return $row->fields;

        return false;

    }

    function GeneratePagination($max_visible_page, $max_on_page, $count_pages, $cur_page)

    {

        global $UrlClass;

        $pages = "";

        if ($cur_page + $max_on_page > $count_pages) {

            $end_page = $count_pages;

        } else

            $end_page = $cur_page + $max_visible_page;

        if (($max_visible_page % 2) == 0) {

            $left = $max_visible_page / 2;

            $right = $left - 1;

        } else {

            $left = ($max_visible_page - 1) / 2;

            $right = $left;

        }

        $start_page = $cur_page - $left;

        if ($start_page < 1)

            $start_page = 1;

        $end_page = $start_page + $right + $left;

        if ($end_page > $count_pages) {

            $end_page = $count_pages;

            $start_page = $end_page - $left - $right;

        }

        if ($start_page < 1)

            $start_page = 1;

        for ($i = $start_page; $i <= $end_page; $i++) {

            $adress = $UrlClass->ReplaceUrlValues(array(

                'cur_page' => $i

            ), false, false, true);

            if ($i == $cur_page)

                $pages .= "<li><strong><a href='$adress'>$i</a></strong></li>";

            else

                $pages .= "<li><a href='$adress'>$i</a></li>";

        }

        return $pages;

    }

    function InsertFavoriteSearch($id, $link, $uid, $name)

    {

        global $dbClass, $config;

        $sql = "SELECT * FROM `" . $config['table_prefix'] . "searchfavorite` WHERE search_id='" . $id . "' AND user_id='" . $uid . "' AND name='" . $name . "'";

        $reCheck = $dbClass->query($sql);

        if ($reCheck->RecordCount() == 0) {

            $sql = "INSERT INTO `" . $config['table_prefix'] . "searchfavorite` SET search_id='" . $id . "', user_id='" . $uid . "', link='" . $link . "', name='" . $name . "'";

            return $dbClass->Query($sql);

        } else

            return "already";

    }

    function GenerateFavoriteLink()

    {

        global $UrlClass;

        return $UrlClass->ReplaceUrlValues(array(

            'addfavorite' => '1'

        ));

    }

    function GenerateSearchEngineResults($param, $request_vars)

    {

        global $dbClass, $config, $UrlClass;

        $get_vars = $_GET;

        $next_button_disabled = '<a href="#" class="notlinked">&gt;&gt;</a>';

        $prev_button_disabled = '<a href="#" class="notlinked">&lt;&lt;</a>';

        require_once($config['wpradmin_basepath'] . 'include/listingfields.inc.php');

        $listingFields = registry::register('ListingFields');

        $max_visible_page = $config['searchresults_visible_page'];

        if (is_numeric($param['id'])) {

            $se_id = $param['id'];

            if (isset($request_vars['searchengine' . $se_id . "_validate"])) {

# RemoteDb flag added

                $se_info = $this->GetSearchEngineInfo($se_id, $param['masterdb']);

                if ($gencountSQL = $this->GenerateSQL($se_id, $request_vars, $se_info, true, $param['masterdb'])) {

//wp_die( 'Here '. $gencountSQL);

                    $genSQL = $this->GenerateSQL($se_id, $request_vars, $se_info, false, $param['masterdb']);

                    if (isset($get_vars['sortby'])) {

                        $sortby = $listingFields->SearchField($get_vars['sortby']);

# RemoteDb flag added

                        if ($dbClass->ColumnExists($config['table_prefix'] . "listingsdb", $sortby, $column_info, $param['masterdb'])) {

                            $column_info['Type'] = preg_replace('/\(\d+\)/', "", $column_info['Type']);

                            if ($column_info['Type'] == "int") {

                                $genSQL .= " ORDER BY " . $sortby . "+0";

                            } else

                                $genSQL .= " ORDER BY " . $sortby;

                            if ($get_vars['sortorder'] == "DESC")

                                $genSQL .= " DESC ";

                        }

                    }

                    require_once($config['wpradmin_basepath'] . 'include/parse.inc.php');

                    $template = registry::register('ParseClass');

                    $content = $template->GetTemplate($config['basepath'] . $config['template_dir'] . "/" . $se_info['searchresults_template']);

                    $reg_searchresultsnav_pages = "#\{searchresultsnav_pages(?:\s+length=(\d+))?\}#is";

                    $max_on_page = $se_info['searchresults_on_page'];

                    $pat = "";

                    if (preg_match($reg_searchresultsnav_pages, $content, $match)) {

                        $pat = $match[0];

                        if (isset($match[1]))

                            $max_visible_page = $match[1];

                    }

# RemoteDb flag added

                    $number_records = $dbClass->Query($gencountSQL, $param['masterdb']);

                    $number_records = $number_records->fields['count'];

                    $total_number_records = $number_records;

//include_once('controlpanel.inc.php');

                    $max_disp_ck = new controlpanelClass();

                    $max_disp_get = $max_disp_ck->GetControlPanelOnlyData();

                    $max_listing_display = $max_disp_get['controlpanel_max_search_results'];

                    if ($max_listing_display) {

                        $number_records = $max_listing_display;

                        define('MAX_LISTING_COUNT', $max_listing_display);

                        define('TOTAL_LISTING_COUNT', $total_number_records);

                    }

                    if (isset($get_vars['cur_page']))

                        $cur_page = $get_vars['cur_page'];

                    else

                        $cur_page = 1;

                    if (!is_numeric($cur_page) OR $cur_page < 1)

                        $cur_page = 1;

                    $count_pages = ceil($number_records / $max_on_page);

                    $start_range = ($cur_page - 1) * $max_on_page + 1;

                    $end_range = $cur_page * $max_on_page;

                    if ($end_range > $number_records)

                        $end_range = $number_records;

                    $genSQL = $genSQL . " LIMIT " . ($start_range - 1) . "," . $max_on_page;

// echo $genSQL;

# RemoteDb flag added

                    $results = $dbClass->Query($genSQL, $param['masterdb']);

                    $pages = $this->GeneratePagination($max_visible_page, $max_on_page, $count_pages, $cur_page);

                    $resultsCount = $results->recordCount();

                    if ($resultsCount > 0) {

                        if (count($get_vars['listing_class']) == 1) {

                            /*$sql = "SELECT * FROM `".$config['table_prefix']."class` WHERE class_id='".$get_vars['listing_class'][0]."'";

                            $reN = $dbClass->query($sql);

                            if($reN->RecordCOunt()>0)

                            {*/

//$classname = str_replace(" ","_",trim(strtolower($reN->fields['class_name'])));

                            $classname . "class_" . $get_vars['listing_class'][0];

                            $path = pathinfo($se_info['searchresults_template']);

                            $tmp_name = $path['filename'] . "_" . $classname . "." . $path['extension'];

                            if (file_exists($config['basepath'] . $config['template_dir'] . "/" . $tmp_name)) {

                                $se_info['searchresults_template'] = $tmp_name;

                            }

//}

                        }

                        $content = $template->GetTemplate($config['basepath'] . $config['template_dir'] . "/" . $se_info['searchresults_template']);

//ReplaceTag

                        $content = $template->ReplaceTag('{searchresultsnav_searchcount}', $total_number_records, $content);

                        $content = $template->ReplaceTag('{searchresultsnav_maxcount}', $number_records, $content);

                        $content = $template->ReplaceTag("{searchrestultsnav_range}", $start_range . "-" . $end_range, $content);

                        $content = $template->ReplaceTag("{favorite_name}", $_POST['search_name'], $content);

                        if ($pat != "")

                            $content = $template->ReplaceTag($pat, $pages, $content);

                        else {

                            $content = $template->ReplaceTag("{searchresultsnav_pages}", $pages, $content);

                        }

                        if ($cur_page > 1) {

                            $adress = $UrlClass->ReplaceUrlValues(array(

                                'cur_page' => $cur_page - 1

                            ), array(

                                'addfavorite'

                            ));

                            $prev_button = "<a href='$adress'>&lt;&lt;</a>";

                            $content = $template->ReplaceTag("{searchresultsnav_prev_button}", $prev_button, $content);

                        } else

                            $content = $template->ReplaceTag("{searchresultsnav_prev_button}", $prev_button_disabled, $content);

                        if ($cur_page < $count_pages) {

                            $adress = $UrlClass->ReplaceUrlValues(array(

                                'cur_page' => $cur_page + 1

                            ), array(

                                'addfavorite'

                            ));

                            $next_button = "<a href='$adress'>&gt;&gt;</a>";

                            $content = $template->ReplaceTag("{searchresultsnav_next_button}", $next_button, $content);

                        } else

                            $content = $template->ReplaceTag("{searchresultsnav_next_button}", $next_button_disabled, $content);

//$content = $template->ParseNavigationTags($results,$se_info,$content);

                        $template_row = $template->GetTemplateBlock('searchresults', $content);

                        if (is_numeric($max_on_page) AND $max_on_page > 0) {

                            if ($resultsCount >= $max_on_page) {

                                $repeat = $max_on_page;

                            } else

                                $repeat = $resultsCount;

                        } else

                            $repeat = $resultsCount;

                        $rows = "";

                        for ($i = 0; $i < $repeat; $i++) {

// $rows .= $template->MainParse($template_row['content'],$results->fields['listingsdb_id'],true);

                            $rows .= $template->MainParse($template_row['content'], $results->fields, true);

                            if ($i % 2 == 0)

                                $odd = 0;

                            else

                                $odd = 1;

                            $rows = $template->ReplaceTag("{row_num_even_odd}", $odd, $rows);

                            $results->MoveNext();

                        }

                        $content = $template->ReplaceTemplateBlock('searchresults', $rows, $content);

                        $reg_sort = "#{searchresultsort(.*?)}#";

                        $reg_param = "#(\w+)\s*=(?:'([^']*)'|\"([^\"]*)\")#";

                        if (preg_match_all($reg_sort, $content, $matches)) {

                            for ($i = 0; $i < count($matches[0]); $i++) {

                                $params = false;

                                if (!empty($matches[1][$i])) {

                                    if (preg_match_all($reg_param, $matches[1][$i], $matches_params)) {

                                        for ($i_p = 0; $i_p < count($matches_params[0]); $i_p++) {

                                            if ($matches_params[2][$i_p] != "")

                                                $params[$matches_params[1][$i_p]] = $matches_params[2][$i_p];

                                            else

                                                $params[$matches_params[1][$i_p]] = $matches_params[3][$i_p];

                                        }

                                    }

                                }

                                if (isset($params['field_name'])) {

                                    if (!empty($get_vars['sortby']) && $get_vars['sortby'] == $params['field_name']) {

                                        if ($get_vars['sortorder'] == "DESC") {

                                            $replacement = $UrlClass->ReplaceUrlValues(array(

                                                'sortby' => $params['field_name']

                                            ), 'sortorder');

                                        } else

                                            $replacement = $UrlClass->ReplaceUrlValues(array(

                                                'sortby' => $params['field_name'],

                                                'sortorder' => 'DESC'

                                            ));

                                    } else

                                        $replacement = $UrlClass->ReplaceUrlValues(array(

                                            'sortby' => $params['field_name']

                                        ), 'sortorder');

                                } else

                                    $replacement = $UrlClass->selfURL();

                                $content = str_replace($matches[0][$i], $replacement, $content);

                            }

                        }

                        /*

                        require_once($config['basepath']."include/parse.inc.php");

                        $parseClass = registry::register('parseClass');

                        $parseClass->MainParse($content);*/

                        return $content;

                    }

                    return "<h3>Search Results</h3>No results";

                } else

                    return "<h3>Search Results</h3>No results";

            }

        }

        return false;

    }

    function GetSearchEngines()

    {

        global $dbClass, $config;

        $sql = "SELECT * FROM " . $config['table_prefix'] . "searchengines WHERE parent_id=0";

        $results = $dbClass->Query($sql);

        if ($results->recordCount() > 0) {

            $return_tab = array();

            while (!$results->EOF) {

                $return_tab[] = $results->fields;

                $results->MoveNext();

            }

            return $return_tab;

        } else

            return false;

    }

    function GetSearchEngineFields($id)

    {

        global $dbClass, $config;

        $sql = "SELECT * FROM " . $config['table_prefix'] . "searchenginescontent WHERE searchengines_id='" . $id . "' ORDER BY field_rank_col,field_rank";

# RemoteDb flag added

        $results = $dbClass->Query($sql, true);

        if ($results->recordCount() > 0) {

            $tab_results = array();

            while (!$results->EOF) {

                $tab_results[] = $results->fields;

                $results->MoveNext();

            }

            return $tab_results;

        } else

            return false;

    }

    function AddEditSearchEngineField($request, $sid = 0, $edit_id = false)

    {

        global $dbClass, $config;

        if ($edit_id !== false)

            $sql = "UPDATE " . $config['table_prefix'] . "searchenginescontent ";

        else

            $sql = "INSERT INTO " . $config['table_prefix'] . "searchenginescontent ";

        $sql .= "SET

searchengines_id = '" . $sid . "',

field_in_table = '" . $request['field_in_table'] . "',

field_name= '" . $request['field_name'] . "',

field_caption= '" . $request['field_caption'] . "',

field_type= '" . $request['field_type'] . "',

field_height= '" . $request['field_height'] . "',

field_width= '" . $request['field_width'] . "',

input_type= '" . $request['input_type'] . "',

input_option= '" . $request['input_option'] . "',

field_value= '" . $request['field_value'] . "'";

        if (isset($request['field_options']))

            $sql .= ",field_options 	 = '" . $request['field_options'] . "'";

        if (isset($request['field_options_type']))

            $sql .= ",field_options_type 	 = '" . $request['field_options_type'] . "'";

        if (isset($request['field_required']))

            $sql .= ",field_required 	 = '" . $request['field_required'] . "'";

        if (isset($request['field_rank']))

            $sql .= ",field_rank = '" . $request['field_rank'] . "'";

        if (isset($request['field_rank_col']))

            $sql .= ",field_rank_col = '" . $request['field_rank_col'] . "'";

        if ($edit_id !== false) {

            $sql .= " WHERE id='" . $edit_id . "'";

            return $dbClass->Query($sql);

        } else {

            if ($dbClass->Query($sql)) {

                return $dbClass->lastId();

            } else

                return false;

        }

    }

    function UpdateSearchEngineFields($edit_id, $request_vars, $index)

    {

        global $dbClass, $config;

        $delsql = "";

        $delsql = substr($delsql, 0, -1);

        $sql = "DELETE FROM `" . $config['table_prefix'] . "searchenginescontent` WHERE searchengines_id='" . $edit_id . "'";

        $dbClass->query($sql);

        for ($i = 0; $i < count($request_vars['field_id'][$index]); $i++) {

//if($delsql!="")

//{

//}

            $opt = explode("_", $request_vars['field_id'][$index][$i]);

            if (is_numeric($opt[0])) {

//$field_value = base64_decode($request_vars['field_value'][$index][$i]);

                $field_value = $request_vars['field_value'][$index][$i];

                $vars = array();

                $vars['field_in_table'] = $request_vars['field_in_table'][$index][$i];

                $vars['field_name'] = $request_vars['field_name'][$index][$i];

                $vars['field_caption'] = $request_vars['field_caption'][$index][$i];

                $vars['field_type'] = $request_vars['field_type'][$index][$i];

                $vars['field_height'] = $request_vars['field_height'][$index][$i];

                $vars['field_width'] = $request_vars['field_width'][$index][$i];

                $vars['input_type'] = $request_vars['input_type'][$index][$i];

                $vars['input_option'] = $request_vars['input_option'][$index][$i];

                $field_value = str_replace("\n", "|", $field_value);

                $vars['field_value'] = $field_value;

                $vars['field_rank'] = $i;

                $vars['field_rank_col'] = $opt[1];

                $lID = $this->AddEditSearchEngineField($vars, $edit_id);

//$delsql .= $lID.",";

                /*

                if($opt[0]==0)

                {

                $lID = $this->AddEditSearchEngineField($vars,$edit_id);

                $delsql .= $lID.",";

                }else

                {

                $delsql .= $opt[0].",";

                $this->AddEditSearchEngineField($vars,$edit_id,$opt[0]);

                }*/

            }

        }

        return true;

    }

    function AddSearchEngineFields($sid)

    {

        global $dbClass, $config;

        $sql = "UPDATE  `" . $config['table_prefix'] . "searchenginescontent` SET searchengines_id='" . $sid . "' WHERE searchengines_id=0";

        return $dbClass->Query($sql);

    }

    function GenerateOptions($request, $index)

    {

        $options = array();

        $opt = array(

            "_column_width",

            "_text_width",

            "_minmax_width",

            "_daterange_width",

            "_minimum_width",

            "_maximum_width",

            "_list_width",

            "_multiselect_height",

            "_multiselect_width"

        );

        for ($i = 0; $i < count($opt); $i++) {

            $name = "left" . $opt[$i];

            $options[$name] = $request[$name][$index];

            $name = "right" . $opt[$i];

            $options[$name] = $request[$name][$index];

        }

        return json_encode($options);

    }

    function UpdateSearchEngine($edit_id, $request_vars, $tab_caption = '', $parentId = false, $rank = 0, $class_id = 0)

    {

        global $dbClass, $config;

        $options = $this->GenerateOptions($request_vars, $rank);

        $sql = "UPDATE " . $config['table_prefix'] . "searchengines SET

name='" . $request_vars['name'] . "',

template='" . $request_vars['template'] . "',

searchresults_template='" . $request_vars['searchresults_template'] . "',

searchresults_on_page='" . $request_vars['searchresults_on_page'] . "',

searchengine_type='" . $request_vars['searchengine_type'] . "',

options='" . $options . "',

tab_rank='" . $rank . "',

tab_caption='" . $tab_caption . "'";

        if ($parentId !== false) {

            $sql .= ",parent_id='" . $parentId . "'";

        }

        if ($class_id !== false) {

            $sql .= ",class_id='" . $class_id . "'";

        }

        $sql .= " WHERE id='" . $edit_id . "'";

        return $dbClass->Query($sql);

    }

    function AddSearchEngine($edit_id, $request_vars, $tab_caption = '', $parentId = false, $rank = 0, $class_id = 0)

    {

        global $dbClass, $config;

        $options = $this->GenerateOptions($request_vars, $rank);

        $sql = "INSERT INTO " . $config['table_prefix'] . "searchengines SET

name='" . $request_vars['name'] . "',

template='" . $request_vars['template'] . "',

searchresults_template='" . $request_vars['searchresults_template'] . "',

searchresults_on_page='" . $request_vars['searchresults_on_page'] . "',

searchengine_type='" . $request_vars['searchengine_type'] . "',

options='" . $options . "',

tab_rank='" . $rank . "',

tab_caption='" . $tab_caption . "',

parent_id='" . $parentId . "',

class_id='" . $class_id . "'

";

        if ($dbClass->Query($sql)) {

            return $dbClass->LastID();

        }

        return false;

    }

    function DeleteSearchEngine($del_id)

    {

        global $dbClass, $config;

        $sql = "DELETE FROM " . $config['table_prefix'] . "searchenginescontent WHERE searchengines_id ='" . $del_id . "'";

        $dbClass->Query($sql);

        $sql = "SELECT id FROM " . $config['table_prefix'] . "searchengines WHERE parent_id='" . $del_id . "'";

        $re = $dbClass->query($sql);

        if ($re->RecordCount() > 0) {

            while (!$re->EOF) {

                $this->DeleteSearchEngine($re->fields['id']);

                $re->MoveNext();

            }

        }

        $sql = "DELETE FROM " . $config['table_prefix'] . "searchengines WHERE id='" . $del_id . "'";

        return $dbClass->Query($sql);

    }

    function SearchEngineBackEnd($post_vars, $get_vars)

    {

        global $config, $dbClass, $UrlClass, $presentationClass, $formsClass, $jqueryscript;

        $content = "";

        $page_name = "searchengines";

        $info = "";

        if (!isset($get_vars['action']) OR $get_vars['action'] == 'del_searchengine') {

            if ($get_vars['action'] == 'del_searchengine') {

                if (is_numeric($get_vars['del_id'])) {

                    if ($this->DeleteSearchEngine($get_vars['del_id'])) {

                        $info = $presentationClass->OperationSuccessfull("Search engine delete sucess");

                    } else

                        $info = $presentationClass->OperationFailed("Search engine delete failed");

                }

            }

            $content = $presentationClass->SecondHeader('Search Engines List');

            if ($tab_searchengines = $this->GetSearchEngines()) {

                $headers = array(

                    "ID",

                    "Searchengine name",

                    "Shortcode",

                    array(

                        'name' => "",

                        'align' => 'right'

                    )

                );

                for ($i = 0; $i < count($tab_searchengines); $i++) {

                    $presentation_data[$i][0] = $tab_searchengines[$i]['id'];

                    $presentation_data[$i][1] = $tab_searchengines[$i]['name'];

                    $presentation_data[$i][2] = "{wp-realty search id=" . $tab_searchengines[$i]['id'] . "}";

                    if ($tab_searchengines[$i]['searchengine_type'] == 1)

                        $presentation_data[$i][2] .= " OR {wp-realty search}";

                    elseif ($tab_searchengines[$i]['searchengine_type'] == 2)

                        $presentation_data[$i][2] .= " OR {wp-realty advsearch}";

                    $editurl = $config['adm_baseurl'] . "index.php?apage=" . $page_name . "&action=edit_searchengine&edit_id=" . $tab_searchengines[$i]['id'];

                    $deleteurl = $config['adm_baseurl'] . "index.php?apage=" . $page_name . "&action=del_searchengine&del_id=" . $tab_searchengines[$i]['id'];

                    $presentation_data[$i][3] = "

<a href='$editurl'>

<img src='" . $config['wpradmin_baseurl'] . "images/pencil-small.png'></a>

<a href='$deleteurl' class='delbutton' name='Delete this searchengine?' onclick='return false'><img src='" . $config['wpradmin_baseurl'] . "images/cross-button.png'></a>";

                }

                $content .= $presentationClass->StandardTableWithDataNew($presentation_data, $headers, false, array(

                    'id' => 'searchengines_datatable',

                    'cellpadding' => '10'

                ));

            } else

                $content .= "No searchengines";

        } else {

            if ($get_vars['action'] == 'edit_searchengine' AND is_numeric($get_vars['edit_id'])) {

                $edit_search_engine_id = $get_vars['edit_id'];

                $content = $this->AddEditSearchEngines($post_vars, $get_vars, $edit_search_engine_id);

            }

        }

        return $content;

    }

    function DeleteSearchEngineField($fid)

    {

        global $dbClass, $config;

        $sql = "DELETE FROM `" . $config['table_prefix'] . "searchenginescontent` WHERE id='" . $fid . "'";

        return $dbClass->Query($sql);

    }

    function AddEditSearchEngines($post_vars, $getvars, $edit_id = false)

    {

        global $config, $presentationClass, $dbClass;

        $content = "";

        $reg_tab_rank = "#(\d+)_(\d+)#";

        if (!isset($getvars['faction']) OR $getvars['faction'] == 'del_field') {

            if ($getvars['faction'] == 'del_field' AND is_numeric($getvars['fid'])) {

                if ($this->DeleteSearchEngineField($getvars['fid']))

                    $content .= $presentationClass->OperationSuccessfull('Field has been deleted');

                else

                    $content .= $presentationClass->OperationSuccessfull('Field hasn\'t been deleted');

            }

            if ($edit_id === false) {

                if (isset($post_vars['addsubmit'])) {

//$post_vars = $dbClass->DataFiltersArray($post_vars);

                    if ($post_vars['name'] != "") {

                        if ($parentID = $this->AddSearchEngine(false, $post_vars)) {

                            for ($i = 0; $i < count($post_vars['tab_rank']); $i++) {

                                if (preg_match($reg_tab_rank, $post_vars['tab_rank'][$i], $match)) {

                                    $index = $i;

                                    $rank = $match[1];

                                    if ($ID = $this->AddSearchEngine(false, $post_vars, $post_vars['tab_caption'][$index], $parentID, $rank, $post_vars['class_id'][$index])) {

                                        if ($this->UpdateSearchEngineFields($ID, $post_vars, $index)) {

                                        }

                                    }

                                }

                            }

                            header("Location: " . $config['adm_baseurl'] . "index.php?apage=searchengines&action=edit_searchengine&edit_id=" . $parentID);

                            die();

                        }

                    } else

                        $content .= $presentationClass->OperationFailed('You must fill field NAME');

                }

//$request = $this->GetSearchEngineInfo($edit_id);

            } else {

                if (isset($post_vars['editsubmit'])) {

//$post_vars = $dbClass->DataFiltersArray($post_vars);

                    if ($post_vars['name'] != "") {

                        $notDelete = array();

                        $this->UpdateSearchEngine($edit_id, $post_vars);

                        for ($i = 0; $i < count($post_vars['tab_rank']); $i++) {

                            if (preg_match($reg_tab_rank, $post_vars['tab_rank'][$i], $match)) {

                                $index = $i;

                                $rank = $match[1];

                                if ($match[2] != "0") {

                                    $this->UpdateSearchEngine($match[2], $post_vars, $post_vars['tab_caption'][$index], $edit_id, $rank, $post_vars['class_id'][$index]);

                                    $ID = $match[2];

                                } else {

                                    $ID = $this->AddSearchEngine(false, $post_vars, $post_vars['tab_caption'][$index], $edit_id, $rank, $post_vars['class_id'][$index]);

                                }

                                $notDelete[] = $ID;

                                if ($this->UpdateSearchEngineFields($ID, $post_vars, $index)) {

//$content.= $presentationClass->OperationSuccessfull('Search Engine has been updated');

                                }

                            }

                        }

                        $content .= $presentationClass->OperationSuccessfull('Search Engine has been updated');

//delete notactive tab

                        if (count($notDelete) > 0) {

                            $notDelete = implode(",", $notDelete);

                            $sql = "SELECT id FROM " . $config['table_prefix'] . "searchengines WHERE parent_id='$edit_id' AND id NOT IN (" . $notDelete . ")";

                            $re = $dbClass->query($sql);

                            if ($re->RecordCount() > 0) {

                                while (!$re->EOF) {

                                    $this->DeleteSearchEngine($re->fields['id']);

                                    $re->MoveNext();

                                }

                            }

                        }

                    } else

                        $content .= $presentationClass->OperationFailed('You must fill field NAME');

                }

//$request = $this->GetSearchEngineInfo($edit_id);

            }

            $content .= $this->ShowAddEditSearchEngines($edit_id);

        }

        return $content;

    }

    function GetFieldInfo($fid)

    {

        global $dbClass, $config;

        $sql = "SELECT * FROM `" . $config['table_prefix'] . "searchenginescontent` WHERE id='" . $fid . "'";

        return $dbClass->GetOneRow($sql);

    }

    function FieldPreview($field_type, $field_caption, $field_height, $field_width, $field_value = '')

    {

        global $formsClass, $config;

        if ($field_type == 'list') {

            if ($field_value != "")

                $options = explode("|", $field_value);

            else

                $options = false;

            $params = array(

                'class' => 'list'

            );

            if ($field_width > 0)

                $params['style'] = 'width:' . $field_width . "px";

            $preview = "<strong>" . $field_caption . ":</strong>&nbsp;&nbsp;" . $formsClass->create_select('', '', $params, $options);

            $preview .= "<a href='#' class='listedit'><img src='" . $config['wpradmin_baseurl'] . "images/pencil-small.png'></a>";

        } elseif ($field_type == 'min') {

            $params = array();

            if ($field_width > 0)

                $params['style'] = 'width:' . $field_width . "px";

            $preview = "<strong>Minimum " . $field_caption . ":</strong>&nbsp;&nbsp;" . $formsClass->create_text('', '', $params);

        } elseif ($field_type == 'max') {

            $params = array();

            if ($field_width > 0)

                $params['style'] = 'width:' . $field_width . "px";

            $preview = "<strong>Maximum " . $field_caption . ":</strong>&nbsp;&nbsp;" . $formsClass->create_text('', '', $params);

        } elseif ($field_type == 'minmax') {

            $params = array();

            if ($field_width > 0)

                $params['style'] = 'width:' . $field_width . "px";

            $preview = "<strong>" . $field_caption . ":</strong>&nbsp;&nbsp;" . $formsClass->create_text('', '', $params) . "&nbsp;&nbsp;to&nbsp;&nbsp;" . $formsClass->create_text('', '', $params);

        } elseif ($field_type == 'daterange') {

            $params = array();

            if ($field_width > 0)

                $params['style'] = 'width:' . $field_width . "px";

            $preview = "<strong>" . $field_caption . ":</strong>&nbsp;&nbsp;" . $formsClass->create_text('', '', $params) . "&nbsp;&nbsp;to&nbsp;&nbsp;" . $formsClass->create_text('', '', $params);

        } elseif ($field_type == 'check') {

            if ($field_value != "")

                $options = explode("|", $field_value);

            else

                $options = false;

            $preview = "<table><tr><td><strong>" . $field_caption . ":</strong></td><td>";

            if ($options !== false) {

                for ($i = 0; $i < count($options); $i++) {

                    $preview .= $formsClass->create_checkbox('', '') . $options[$i];

                    if ($i == 0)

                        $preview .= "<a href='#' class='listedit'><img src='" . $config['wpradmin_baseurl'] . "images/pencil-small.png'></a>";

                    $preview .= "<br/>";

                }

            } else

                $preview .= "<a href='#' class='listedit'><img src='" . $config['wpradmin_baseurl'] . "images/pencil-small.png'></a>";

            $preview .= "</td></tr><table>";

        } elseif ($field_type == 'multiselect') {

            $params = array(

                'class' => 'list'

            );

            $params['style'] = "";

            if ($field_width > 0)

                $params['style'] .= 'width:' . $field_width . "px;";

            if ($field_height > 0) {

                $params['style'] .= 'height:' . $field_height . "px;";

            }

            if ($field_value != "")

                $options = explode("|", $field_value);

            else

                $options = false;

            $preview = "<table><tr valign='top'><td><strong>" . $field_caption . ":</strong></td>";

            $preview .= "<td>" . $formsClass->create_multiselect('', '', $params, $options) . "</td>";

            $preview .= "<td><a href='#' class='listedit'><img src='" . $config['wpradmin_baseurl'] . "images/pencil-small.png'></a></td></tr></table>";

        } else {

            $params = array();

            if ($field_width > 0)

                $params['style'] = 'width:' . $field_width . "px";

            $preview = "<strong>" . $field_caption . ":</strong>&nbsp;&nbsp;" . $formsClass->create_text('', '', $params);

        }

        return $preview;

    }

    function GetFieldValues($name_field)

    {

        global $dbClass, $config;

        $sql = "SELECT " . $name_field . " FROM " . $config['table_prefix'] . "listingsdb ORDER BY " . $name_field;

        $reField = $dbClass->query($sql, true);

        if ($reField->RecordCount() > 0) {

            $rarray = array();

            while (!$reField->EOF) {

                if (!in_array($reField->fields[$name_field], $rarray) AND $reField->fields[$name_field] != "" AND $reField->fields[$name_field] != Null)

                    $rarray[] = $reField->fields[$name_field];

                $reField->MoveNext();

            }

            return $rarray;

        }

        return false;

    }

    function GenerateFieldBox($request, $num)

    {

        global $formsClass, $config;

        $field_content = "";

        if ($request['id'] == 0) {

            if ($request['field_name'] == 'class_name') {

                require_once($config['wpradmin_basepath'] . "include/class.inc.php");

                $pclassClass = registry::register('pclassClass');

                if ($classes = $pclassClass->GetClasses()) {

                    $tmp = "";

                    for ($i = 0; $i < count($classes); $i++) {

                        $tmp .= $classes[$i]['class_name'];

                        if ($i < count($classes) - 1)

                            $tmp .= "|";

                    }

                    $request['field_type'] = 'multiselect';

                    $request['field_value'] = $tmp;

                }

            } else {

                if ($fields = $this->GetFieldValues($request['field_name'])) {

                    $tmp = implode("|", $fields);

                    $request['field_type'] = 'multiselect';

                    $request['field_value'] = $tmp;

                }

            }

        }

        if ($request['id'] == 0 AND $request['field_caption'] == '')

            $request['field_caption'] = $request['field_name'];

        $field_content .= "<table cellpadding='3'>";

        $field_content .= "<tr><td width='100'><strong>Field Name:</strong></td><td>" . $formsClass->create_hidden('field_id[' . $num . '][]', $request['id'], array(

                'class' => 'fieldid'

            )) . $formsClass->create_hidden('field_in_table[' . $num . '][]', $request['field_in_table']) . $formsClass->create_text('field_name[' . $num . '][]', $request['field_name']) . "</td></tr>";

        $field_content .= "<tr><td><strong>Caption:</strong></td><td>" . $formsClass->create_hidden('field_value[' . $num . '][]', $request['field_value'], array(

                'class' => 'fieldvalue'

            )) . $formsClass->create_text('field_caption[' . $num . '][]', $request['field_caption'], array(

                'class' => 'fieldcaption'

            )) . "</td></tr>";

// $field_content .= "<tr><td><strong>Caption:</strong></td><td>".$formsClass->create_hidden('field_value['.$num.'][]',base64_encode($request['field_value']),array('class'=>'fieldvalue')).$formsClass->create_text('field_caption['.$num.'][]',$request['field_caption'],array('class'=>'fieldcaption'))."</td></tr>";

        $field_content .= "<tr><td><strong>Field type:</strong></td><td>" . $formsClass->create_select('field_type[' . $num . '][]', $request['field_type'], array(

                'class' => 'fieldtype'

            ), $this->tab_field_type) . "</td></tr>";

        if ($request['field_type'] == 'multiselect') {

            $field_content .= "<tr class='field_height'><td><strong>Height:</strong></td><td>" . $formsClass->create_text('field_height[' . $num . '][]', $request['field_height'], array(

                    'class' => 'fieldheight',

                    'size' => '4'

                )) . " px</td></tr>";

        }

        if ($request['field_type'] != 'check') {

            $field_content .= "<tr class='field_width'><td><strong>Width:</strong></td><td>" . $formsClass->create_text('field_width[' . $num . '][]', $request['field_width'], array(

                    'class' => 'fieldwidth',

                    'size' => '4'

                )) . " px</td></tr>";

        }

        $field_content .= "</table>";

        $field_preview = $this->FieldPreview($request['field_type'], $request['field_caption'], $request['field_height'], $request['field_width'], $request['field_value']);

        $field_content .= "<div class='preview'><label>Preview</label>" . $field_preview . "</div>";

        return array(

            "<a href='#' class='delbutton_se' onclick='return false' name='Delete this field?'><img src='" . $config['wpradmin_baseurl'] . "images/cross-button.png'></a>&nbsp;" . $request['field_name'],

            $field_content

        );

    }

    function GenerateToggleSettings($type, $request = array())

    {

        global $formsClass;

        $content = "";

        $content .= "<div class='box toggle_side_div wpr_form' style='display:none;padding:5px'>";

        $content .= "<h3>Configure (default) settings for this column</h3>";

        $content .= "<table cellpadding='5'>";

        $type = strtolower($type);

        $content .= "<tr><td><b>" . $type . " column :&nbsp;</b></td><td>" . $formsClass->create_text($type . "_column_width[]", $request[$type . "_column_width"], array(

                'size' => '4'

            )) . "width in px</td></tr>";

        $content .= "<tr valign='top'><td><b>Multiselect:&nbsp;</b></td><td>" . $formsClass->create_text($type . "_multiselect_width[]", $request[$type . "_multiselect_width"], array(

                'size' => '4'

            )) . " width in px

<br/>" . $formsClass->create_text($type . "_multiselect_height[]", $request[$type . "_multiselect_height"], array(

                'size' => '4'

            )) . " height in px</td></tr>";

        $content .= "<tr valign='top'><td><b>Text:&nbsp;</b></td><td>" . $formsClass->create_text($type . "_text_width[]", $request[$type . "_text_width"], array(

                'size' => '4'

            )) . " width in px</td></tr>";

        $content .= "<tr valign='top'><td><b>List:&nbsp;</b></td><td>" . $formsClass->create_text($type . "_list_width[]", $request[$type . "_list_width"], array(

                'size' => '4'

            )) . " width in px</td></tr>";

        $content .= "<tr valign='top'><td><b>Min/Max:&nbsp;</b></td><td>" . $formsClass->create_text($type . "_minmax_width[]", $request[$type . "_minmax_width"], array(

                'size' => '4'

            )) . " width in px</td></tr>";

        $content .= "<tr valign='top'><td><b>Date Range:&nbsp;</b></td><td>" . $formsClass->create_text($type . "_daterange_width[]", $request[$type . "_daterange_width"], array(

                'size' => '4'

            )) . " width in px</td></tr>";

        $content .= "<tr valign='top'><td><b>Minimum:&nbsp;</b></td><td>" . $formsClass->create_text($type . "_minimum_width[]", $request[$type . "_minimum_width"], array(

                'size' => '4'

            )) . " width in px</td></tr>";

        $content .= "<tr valign='top'><td><b>Maximum:&nbsp;</b></td><td>" . $formsClass->create_text($type . "_maximum_width[]", $request[$type . "_maximum_width"], array(

                'size' => '4'

            )) . " width in px</td></tr>";

        $content .= "</table>";

        $content .= "</div>";

        return $content;

    }

    function GenerateTabContent($num, $edit_id = false, $request = array())

    {

        global $dbClass, $presentationClass, $config, $formsClass;

        if ($edit_id !== false) {

            if ($tab_searchengine_fields = $this->GetSearchEngineFields($edit_id)) {

                for ($i = 0; $i < count($tab_searchengine_fields); $i++) {

                    $req_bool = false;

                    if ($tab_searchengine_fields[$i]['field_required'] == 1)

                        $req_bool = true;

                    $data[$tab_searchengine_fields[$i]['field_rank_col']][] = $this->GenerateFieldBox($tab_searchengine_fields[$i], $num);

                }

            }

        } else {

            $data[0] = false;

            $data[1] = false;

        }

        $block_fields = array('listingsdb_creation_date', 'listingsdb_id', 'listingsdb_is_mark_for_remove', 'listingsdb_is_photo_downloaded', 'listingsdb_last_modified', 'listingsdb_photo_last_downloaded');

# Dinesh : Wednesday, November 27, 2013

# RemoteDb flag added

        $tab_temp = $dbClass->GetColumns($config['table_prefix'] . "listingsdb", $block_fields, false, true);

        foreach ($tab_temp as $key => $value) {

            $tab_field_in_table[$value] = $value;

        }

        asort($tab_field_in_table);

        $options = json_decode($request['options'], true);

        $toggle_left = $this->GenerateToggleSettings('Left', $options);

        $toggle_right = $this->GenerateToggleSettings('Right', $options);

        $jquerytabsC .= "<div style='float:left'>Add new field: " . $formsClass->create_select('', '', array(

                'class' => 'add_new_field'

            ), array_merge(array(

                '' => 'choose',

                "class_name" => "class_name"

            ), $tab_field_in_table)) . "</div>";

        $jquerytabsC .= "<span style='float:right' ><button onclick='return false' class='editTab' style='float:none;display:inline'>Edit options</button>";

        $jquerytabsC .= "<button onclick='return false' class='deleteTab' style='float:none;display:inline'>Delete tab</button>";

        $jquerytabsC .= $formsClass->create_hidden('tab_caption[' . $cntTab . "]", $request['tab_caption'], array(

            'class' => 'tab_caption'

        ));

        $jquerytabsC .= $formsClass->create_hidden('class_id[' . $cntTab . "]", $request['class_id'], array(

            'class' => 'class_id'

        ));

        $tab_rank = $num . "_";

        if ($edit_id !== false)

            $tab_rank .= $edit_id;

        else

            $tab_rank .= "0";

        $jquerytabsC .= $formsClass->create_hidden('tab_rank[' . $cntTab . "]", $tab_rank, array(

            'class' => 'tab_rank'

        ));

        $jquerytabsC .= "</span><div style='clear:both'></div>";

        $jquerytabsC .= $presentationClass->BlockTableSortableSE($data[0], 2, "<a href='#' onclick='return false' class='side_toggle'>[+]</a> Left side", $toggle_left);

        $jquerytabsC .= $presentationClass->BlockTableSortableSE($data[1], 2, "<a href='#' onclick='return false' class='side_toggle'>[+]</a> Right side", $toggle_right);

        return $jquerytabsC;

    }

    /************************************************************\
     * This is a very large function that needs to be cleaned up.
     *
     * \************************************************************/

    function ShowAddEditSearchEngines($edit_id = false)

    {

        global $presentationClass, $formsClass, $dbClass, $config, $jqueryscript, $UrlClass;

        if ($edit_id === false) {

            $page_name = "addsearchengine";

            $header = $presentationClass->SecondHeader('Add Search Forms');

        } else {

            $page_name = "searchengines";

            $header = $presentationClass->SecondHeader('Edit Search Forms');

        }

        if ($edit_id !== false) {

            $request = $this->GetSearchEngineInfo($edit_id);

            $sql = "SELECT * FROM " . $config['table_prefix'] . "searchengines WHERE parent_id='" . $edit_id . "' ORDER by tab_rank";

            $reSearchEngines = $dbClass->query($sql);

            $cntTab = 0;

            $jquerytabHeaders = array();

            $jquerytabs = array();

            if ($reSearchEngines->RecordCount() > 0) {

//$cntTab = $reSearchEngines->RecordCount();

                while (!$reSearchEngines->EOF) {

                    if ($reSearchEngines->fields['tab_caption'] != "")

                        $jquerytabHeaders['jquerytab_' . $cntTab] = $reSearchEngines->fields['tab_caption'];

                    else

                        $jquerytabHeaders['jquerytab_' . $cntTab] = "Empty caption";

                    $jquerytabs['jquerytab_' . $cntTab] = $this->GenerateTabContent($cntTab, $reSearchEngines->fields['id'], $reSearchEngines->fields);

                    $reSearchEngines->MoveNext();

                    $cntTab++;

                }

            } else

                return false;

        } else {

            $cntTab = 0;

        }

        $backurl = $config['adm_baseurl'] . "index.php?apage=" . $page_name;

        $content .= $formsClass->startform('', array(

            'id' => 'form_se'

        ));

        $content .= "<div class='tophref'><a href='$backurl' class='top_href'><button class='skin_colour round_all'><img width='24' height='24' src='/wp-content/plugins/wp-realty/core/images/icons/small/white/bended_arrow_left.png'><span>Back</span></button></a></div><div class='clear'></div>";

        $content .= '<div class="box grid_16 round_all">

<h2 class="box_head grad_colour">Search Form Template Options:</h2>

<a class="grabber" href="#">&nbsp;</a>

<a class="toggle" href="#">&nbsp;</a>

<div class="toggle_container">

<div class="block">';

        $content .= $header;

        require_once($config['wpradmin_basepath'] . "include/template.inc.php");

        $templateClass = registry::register('templateClass');

        $tab_files = $templateClass->GetTemplateFile($config['basepath'] . $config['template_dir']);

        $tab_files = $tab_files['php'];

        asort($tab_files);

        $tab_files_default = array_merge(array(

            '' => 'default'

        ), $tab_files);

        $rows[] = array(

            "<b>Name:</b>",

            $formsClass->create_text('name', $request['name'], '')

        );

        $rows[] = array(

            "<b>Template:</b>",

            $formsClass->create_select('template', $request['template'], '', $tab_files_default)

        );

        $rows[] = array(

            "<b>Searchresults template:</b>",

            $formsClass->create_select('searchresults_template', $request['searchresults_template'], '', $tab_files)

        );

        $rows[] = array(

            "<b>Results on page:</b>",

            $formsClass->create_text('searchresults_on_page', $request['searchresults_on_page'], '')

        );

        $searchengine_type = array(

            0 => "choose",

            1 => "normal search",

            2 => "advanced search"

        );

        $rows[] = array(

            "<b>Search Form Type:</b>",

            $formsClass->create_select('searchengine_type', $request['searchengine_type'], '', $searchengine_type)

        );

        $content .= $presentationClass->BlockTable($rows, 2, array(

                'class' => 'clearfix'

            )) . "<br/>";

        if ($edit_id === false)

            $content .= $formsClass->create_hidden('action_url', $UrlClass->AddUrlValues(array(

                'page' => $page_name,

                'action' => 'add_searchengine'

            )));

        else

            $content .= $formsClass->create_hidden('action_url', $UrlClass->AddUrlValues(array(

                'page' => $page_name,

                'action' => 'edit_searchengine',

                'edit_id' => $edit_id

            )));

        if ($edit_id === false)

            $add_url = $config['adm_baseurl'] . "index.php?apage=" . $page_name . "&faction=add_field";

        else {

            $add_url = $config['adm_baseurl'] . "index.php?apage=" . $page_name . "&action=edit_searchengine&edit_id=" . $edit_id . "&faction=add_field";

        }

        $content .= '</div></div></div>';

        $content .= $presentationClass->SecondHeader("Fields List");

//$headers = array("Field in Table","Field Name","Caption","type","Input option",/*"Required",*/"Value","Options","Opt type","");

        $headers = array(

            "Field in Table",

            "Field Name",

            "Caption",

            "Type",

            "Input option",

            "Value",

            array(

                'name' => "",

                'align' => 'right'

            )

        );

        $content .= "<div align='left'><button id='addNewTab' onclick='return false'>Add new tab</button></div>";

        $add_new_field_content = $presentationClass->CreateItemSE('[title]', 'a');

        $add_new_field_content = str_replace("\r\n", "", $add_new_field_content);

        $add_new_field_content = addslashes($add_new_field_content);

//$data[1]

//$jquerytabsC = $this->GenerateTabContent(0,$edit_id,$request);

//$jquerytabsC1 = $this->GenerateTabContent(1,false);

//$jquerytabHeaders = array('jquerytab_1'=>'Home'.$pencil,'jquerytab_2'=>'test');

//$jquerytabs = array('jquerytab_1'=>$jquerytabsC,'jquerytab_2'=>$jquerytabsC1);

        $content .= $presentationClass->JqueryTabsWithDataNew($jquerytabs, $jquerytabHeaders, array(

            'id' => 'searchengineTab',

            'class' => 'box grid_16 round_all tabs wpr_form',

            'grabber' => false,

            'toggle' => false

        ));

        require_once($config['wpradmin_basepath'] . "include/class.inc.php");

        $pClass = registry::register('pclassClass');

        $pClasses = $pClass->GetClasses();

        $classes[0] = "All";

        for ($i = 0; $i < count($pClasses); $i++) {

            $classes[$pClasses[$i]['class_id']] = $pClasses[$i]['class_name'];

        }

        $class_select = $formsClass->create_select('', '', array(

            'id' => 'class_id'

        ), $classes);

        $content .= "<div id='tabsoptions'>

Name  <input type='text' value='' id='tab_caption'/><br/>

Class $class_select

</div>";

        $content .= "<div id='deletedialog'>

Delete this tab?

</div>";

        $content .= "<div id='listoptions'>

<textarea style='width:100%'></textarea>

</div>";

        $class_column = "sd_column_se";

        $url = $config['baseurl'] . "ajax.php?f=searchengine.php";

        $content .= $jqueryscript->PrintScript('

jQuery("#searchengineTab .tab_header").sortable(

{

//connectWith: "#searchengineTab .tab_header",

//items:  "li"

});

jQuery("#listoptions").dialog({autoOpen:false});

jQuery("#tabsoptions").dialog({autoOpen:false});

jQuery("#deletedialog").dialog({autoOpen:false});

jQuery(".deleteTab").live("click",function()

{

var index = $("#searchengineTab").tabs("option", "selected");

jQuery("#deletedialog").dialog({

autoOpen: false,

hide:"explode",

buttons: {

"Yes": function() {

$("#searchengineTab").tabs("remove",index);

jQuery( this ).dialog("close");

},

Cancel: function() {

jQuery( this ).dialog("close");

}

}

});

jQuery("#deletedialog").dialog("open");

});

jQuery(".editTab").live("click",function()

{

var tab_handle = $(this).closest("div").attr("id");

var tab_caption_handle = jQuery(this).nextAll(".tab_caption:first");

var class_id_handle = jQuery(this).nextAll(".class_id:first");

$("#searchengineTab ul.tab_header li").each(function()

{

if($(this).children("a").attr("href")=="#"+tab_handle)

{

tab_handle = $(this).children("a");

}

});

var tab_caption = tab_caption_handle.val();

var class_id = class_id_handle.val();

jQuery("#tabsoptions #tab_caption").val(tab_caption);

jQuery("#tabsoptions #class_id").val(class_id);

jQuery.uniform.update();

jQuery("#tabsoptions").dialog({

autoOpen: false,

hide:"explode",

buttons: {

"Save": function() {

if(jQuery("#tab_caption").val()!="")

{

tab_handle.text(jQuery("#tab_caption").val());

}

else

tab_handle.text("Empty caption");

tab_caption_handle.val(jQuery("#tab_caption").val());

class_id_handle.val(jQuery("#class_id").val());

jQuery( this ).dialog("close");

},

Cancel: function() {

jQuery( this ).dialog("close");

}

}

});

jQuery("#tabsoptions").dialog("open");

});

jQuery(".side_toggle").live("click",function()

{

$(this).parent("h2").next("div.toggle_side_div").toggle();

});

var counter = ' . $cntTab . ';

jQuery("#addNewTab").click(function()

{

var name = "jquerytab_"+counter;

jQuery.ajax({

type: "POST",

url: "' . $url . '",

data: "mode=4&num="+counter+"&name="+name,

success:function(data)

{

if(data!="")

{

//$("#searchengineTab ul.tab_header").append("<li><a href=\'#"+name+"\'>"+name+"</a>");

$("#searchengineTab").tabs("add","#"+name,"Empty caption");

$("#"+name).html(data);

counter++;

}

}

});

});

function ReloadPreview(fieldvalue,fieldcaption,fieldtype,handle)

{

if(fieldtype=="multiselect")

{

handle.prev("table").find(".field_width").show();

handle.prev("table").find(".field_height").show();

}

else if(fieldtype!="check")

{

handle.prev("table").find(".field_width").show();

handle.prev("table").find(".field_height").hide();

}else

{

handle.prev("table").find(".field_width").hide();

handle.prev("table").find(".field_height").hide();

}

fieldwidth = handle.prev("table").find(".fieldwidth").val();

fieldheight = handle.prev("table").find(".fieldheight").val();

jQuery.ajax({

type: "POST",

url: "' . $url . '",

data: "mode=2&fieldvalue="+fieldvalue+"&fieldtype="+fieldtype+"&fieldcaption="+fieldcaption+"&fieldwidth="+fieldwidth+"&fieldheight="+fieldheight,

success:function(data)

{

handle.empty().append("<label>Preview</label>").append(data);

}

});

}

function replaceAll(txt, replace, with_this) {

return txt.replace(new RegExp(replace, "g"),with_this);

}

function strpos (haystack, needle, offset) {

var i = (haystack + "").indexOf(needle, (offset || 0));

return i === -1 ? false : i;

}

jQuery(".listedit").live("click",function()

{

var handle = jQuery(this);

var handle_value = handle.closest(".preview").prev("table").find(".fieldvalue");

var value = handle_value.attr("value");

var handle_preview = handle.closest(".preview");

var fieldcaption = handle_preview.prev("table").find(".fieldcaption").attr("value");

var fieldtype = handle_preview.prev("table").find(".fieldtype").attr("value");

$.ajax({

type: "POST",

url: "' . $url . '",

data: "mode=3&value="+value,

success:function(data)

{

value = data;

jQuery("#listoptions textarea").text(value);

jQuery("#listoptions").dialog({

autoOpen: false,

hide:"explode",

buttons: {

"Save": function() {

var new_value = "";

var value = jQuery("#listoptions textarea").attr("value");

var re = /(.+)/m;

while (matches = re.exec(value))

{

value = value.replace(matches[1],"");

new_value += matches[1]+"|";

}

handle_value.attr("value",new_value);

ReloadPreview(new_value,fieldcaption,fieldtype,handle_preview);

jQuery( this ).dialog("close");

},

Cancel: function() {

jQuery( this ).dialog("close");

}

}

});

jQuery("#listoptions").dialog("open");

}

});

});

jQuery(".fieldtype").live("change",function(){

var handle = jQuery(this).closest("table").next(".preview");

var fieldcaption =jQuery(this).closest("tr").prev("tr").find(".fieldcaption").attr("value");

var fieldvalue = jQuery(this).closest("tr").prev("tr").find(".fieldvalue").attr("value");

ReloadPreview(fieldvalue,fieldcaption,jQuery(this).attr("value"),handle);

});

jQuery(".fieldcaption").live("change",function(){

var handle = jQuery(this).closest("table").next(".preview");

var fieldtype =jQuery(this).closest("table").find(".fieldtype").attr("value");

var fieldvalue = jQuery(this).closest("table").find(".fieldvalue").attr("value");

ReloadPreview(fieldvalue,jQuery(this).attr("value"),fieldtype,handle);

});

jQuery("input.fieldheight,input.fieldwidth").live("change",function(){

var handle = jQuery(this).closest("table").next(".preview");

var fieldtype =jQuery(this).closest("table").find(".fieldtype").attr("value");

var fieldvalue = jQuery(this).closest("table").find(".fieldvalue").attr("value");

var fieldcaption =jQuery(this).closest("table").find(".fieldcaption").attr("value");

ReloadPreview(fieldvalue,fieldcaption,fieldtype,handle);

});

jQuery(".delbutton_se").live("click",function()

{

var handle = jQuery(this);

jQuery("#confirm_dialog").empty().append(jQuery(this).attr("name"));

jQuery("#confirm_dialog").dialog({

autoOpen: false,

hide:"explode",

buttons: {

"Yes": function() {

handle.closest("div.box").remove();

jQuery( this ).dialog( "close" );

},

Cancel: function() {

jQuery( this ).dialog( "close" );

}

}

});

jQuery("#confirm_dialog").dialog("open");

});

jQuery(".' . $class_column . '").livequery(function()

{

jQuery(this).sortable({

connectWith: ".' . $class_column . '",

opacity: 0.4,

tolerance: "pointer",

placeholder: "place_holder",

});

});

jQuery(".add_new_field").live("change",function()

{

var val = jQuery(this).attr("value");

var activeID = jQuery("#searchengineTab .ui-state-active a").attr("href");

var num = activeID.split("_");

num = num[1];

jQuery(".add_new_field").attr("value","");

if(val!="")

{

$.ajax({

type: "POST",

url: "' . $url . '",

data: "mode=1&field="+val+"&num="+num,

success:function(data)

{

if(data!="error")

{

var activeID = jQuery("#searchengineTab .ui-state-active a").attr("href");

jQuery(activeID+" .' . $class_column . ':first").append(data);

}

}

});

}

}

);

');

//addnewfieldform

        $add_table = array();

        if ($edit_id === false) {

            $content .= $formsClass->create_button('addsubmit', 'Add search engine', array(

                'id' => 'save_se',

                'onclick' => 'return false'

            ));

            $content .= $formsClass->create_hidden('addsubmit', '1');

        } else {

            $content .= $formsClass->create_button('editsubmit', 'Edit search engine', array(

                'id' => 'save_se',

                'onclick' => 'return false'

            ));

            $content .= $formsClass->create_hidden('editsubmit', '1');

        }

        $content .= $formsClass->endform();

        $content .= $jqueryscript->PrintScript("

jQuery('#save_se').click(function()

{

if(jQuery('#form_se input[name=name]').attr('value')=='')

{

alert('You must fill field NAME');

return false;

}

var fields = new Array();

var cnt=0;

jQuery('#searchengineTab ul.tab_header li').each(function()

{

var href = $(this).children('a').attr('href');

var handle = jQuery(href).find('input.tab_rank');

var rank = handle.val().split('_');

rank = cnt+'_'+rank[1];

handle.val(rank);

cnt++;

});

jQuery('#searchengineTab div.ui-tabs-panel').each(function()

{

var cnt=0;

jQuery(this).find('." . $class_column . "').each(function()

{

fields[cnt] = '';

jQuery(this).find('input.fieldid').each(function()

{

var cs = jQuery(this).attr('value');

jQuery(this).attr('value',cs+'_'+cnt);

});

cnt++;

});

});

$('#form_se').submit();

});

");

        return $content;

    }

    function GetSearchEngineByName($name)

    {

        global $dbClass, $config;

        $sql = "SELECT * FROM `" . $config['table_prefix'] . "searchengines` WHERE name='" . $name . "'";

        $re = $dbClass->query($sql);

        if ($re->RecordCount() > 0) {

            return $re->fields['id'];

        } else

            return false;

    }

    function parseDate($date, $format)

    {

        if (!preg_match_all("/%([YmdHMp])([^%])*/", $format, $formatTokens, PREG_SET_ORDER)) {

            return false;

        }

        $datePattern = '';

        foreach ($formatTokens as $formatToken) {

            $delimiter = isset($formatToken[2]) ? preg_quote($formatToken[2], "/") : '';

            $datePattern .= "(.*)" . $delimiter;

        }

// Splits up the given $date

        if (!preg_match("/" . $datePattern . "/", $date, $dateTokens)) {

            return false;

        }

        $dateSegments = array();

        for ($i = 0; $i < count($formatTokens); $i++) {

            $dateSegments[$formatTokens[$i][1]] = $dateTokens[$i + 1];

        }

// Reformats the given $date into US English date format, suitable for strtotime()

        if ($dateSegments["Y"] && $dateSegments["m"] && $dateSegments["d"]) {

            $dateReformated = $dateSegments["Y"] . "-" . $dateSegments["m"] . "-" . $dateSegments["d"];

        } else {

            return false;

        }

        if (isset($dateSegments["H"]) && $dateSegments["H"] && isset($dateSegments["M"]) && $dateSegments["M"]) {

            $dateReformated .= " " . $dateSegments["H"] . ":" . $dateSegments["M"];

        }

// return strtotime( $dateReformated );

        return $dateReformated;

    }

}

?>