<?php

/**
 * List Filters (lf) Plugin for Cotonti CMF 0.9.x
 *
 * @version 3.0
 * @author esclkm
 * @copyright (c) 2008-2012 esclkm
 */
defined('COT_CODE') or die('Wrong URL');

global $lf_fields;
//$field_types = array('input', 'inputint', 'currency', 'double', 'textarea', 'select', 'checkbox', 'radio', 'datetime', 'country', 'file');
$lf_fields = array(
	'id' => array('field_name' => 'id', 'field_type' => 'input', 'field_variants' => '', 'field_description' => ''),
	'key' => array('field_name' => 'key', 'field_type' => 'input', 'field_variants' => '', 'field_description' => ''),
	'title' => array('field_name' => 'title', 'field_type' => 'input', 'field_variants' => '', 'field_description' => $L['Title']),
	'desc' => array('field_name' => 'desc', 'field_type' => 'input', 'field_variants' => '', 'field_description' => $L['Description']),
	'text' => array('field_name' => 'text', 'field_type' => 'textarea', 'field_variants' => '', 'field_description' => $L['Text']),
	'author' => array('field_name' => 'author', 'field_type' => 'input', 'field_variants' => '', 'field_description' => $L['Author']),
	'ownerid' => array('field_name' => 'id', 'field_type' => 'input', 'field_variants' => '', 'field_description' => ''),
	'url' => array('field_name' => 'url', 'field_type' => 'input', 'field_variants' => '', 'field_description' => ''),
	'size' => array('field_name' => 'size', 'field_type' => 'input', 'field_variants' => '', 'field_description' => ''),
	'count' => array('field_name' => 'count', 'field_type' => 'input', 'field_variants' => '', 'field_description' => $L['Views']),
	'rating' => array('field_name' => 'rating', 'field_type' => 'input', 'field_variants' => '', 'field_description' => ''),
	'comcount' => array('field_name' => 'comcount', 'field_type' => 'input', 'field_variants' => '', 'field_description' => ''),
	'filecount' => array('field_name' => 'filecount', 'field_type' => 'input', 'field_variants' => '', 'field_description' => ''),
	'alias' => array('field_name' => 'alias', 'field_type' => 'input', 'field_variants' => '', 'field_description' => $L['Alias']),
	'date' => array('field_name' => 'date', 'field_type' => 'datetime', 'field_variants' => '', 'field_description' => $L['Date']),
	'begin' => array('field_name' => 'begin', 'field_type' => 'datetime', 'field_variants' => '', 'field_description' => $L['Begin']),
	'expire' => array('field_name' => 'expire', 'field_type' => 'datetime', 'field_variants' => '', 'field_description' => $L['Expire']),
);

if (count($cot_extrafields[$db_pages]) > 0)
{
	$lf_fields = $lf_fields + $cot_extrafields[$db_pages];
}

function generate_sql_query($sql_field, $sql_prt, $type)
{
	$sql_field = 'page_'.$sql_field;
	if (!empty($sql_prt))
	{
		switch ($type)
		{
			case 'isset':
				$sql_tstr = $sql_field." <> '' OR ".$sql_field." <> '0'";
				break;
			case 'like':
				$words = explode(' ', $sql_prt);
				$sql_prt = '%'.implode('%', $words).'%';
				$sql_tstr = $sql_field." LIKE '".$sql_prt."'";
				break;
			case 'more':
				$sql_prt = cot_import($sql_prt, 'D', 'NUM');
				$sql_tstr = ($sql_prt !='') ? $sql_field." >= ".(double)$sql_prt : "";
				break;
			case 'less':
				$sql_prt = cot_import($sql_prt, 'D', 'NUM');
				$sql_tstr = ($sql_prt !='') ? $sql_field." <= ".(double)$sql_prt : "";
				break;
			case 'array':
				if (is_array($sql_prt))
				{
					foreach ($sql_prt as $k => $v)
					{
						$v = cot_import($v, 'D', 'TXT');
						if (!empty($v))
						{
							$sql_tprt[] = $v;
						}
					}
					$sql_tstr = $sql_field." IN ('".implode("', '", $sql_tprt)."')";
				}
				else
				{
					$sql_tstr = $sql_field."='".$sql_prt."'";
				}
				break;
			case 'is':
			default:
				$sql_tstr = $sql_field."='".$sql_prt."'";
				break;
		}
	}
	return($sql_tstr);
}

?>