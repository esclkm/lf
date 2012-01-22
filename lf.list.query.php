<?php

/**
 * [BEGIN_COT_EXT]
 * Hooks=page.list.query
 * [END_COT_EXT]
 */

/**
 * List Filters (lf) Plugin for Cotonti CMF 0.9.x
 *
 * @version 3.0
 * @author esclkm
 * @copyright (c) 2008-2012 esclkm
 */
defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('lf', 'plug');
global $lf_fields;
$lf_pages_cat_list['all'] = $L['All'];
$lfcatlist = cot_structure_children('page', $c);

foreach ($lfcatlist as $lfcat)
{
	$lf_pages_cat_list[$lfcat] = $structure['page'][$lfcat]['title'];
}

if (is_array($_GET['lf']))
{
	$lf = $_GET['lf'];
	if (isset($lf['lfcat']))
	{
		$lf_sql_cat = ($lf['lfcat'][0] != 'all' && count($lf['lfcat']) > 0) ?
			"page_cat IN ('".$db->prep(implode("','", $lf['lfcat']))."')" : "page_cat IN ('".implode("','", $lfcatlist)."')";
	}
	if (isset($lf['search']) && !empty($lf['search']))
	{
		$words = explode(' ', $lf['search']);
		$searchsql_prt = '%'.implode('%', $words).'%';
		$lf_sql_str[] = "(page_title LIKE '".$searchsql_prt."' OR page_desc LIKE '".$searchsql_prt."' OR page_text LIKE '".$searchsql_prt."')";
	}

	foreach ($lf_fields as $row)
	{
		$uname = $row['field_name'];
		switch ($row['field_type'])
		{
			case 'checkbox':
				if (isset($lf[$uname]) && $lf[$uname] != 2)
				{
					$lf_sql_str[] = generate_sql_query($uname, $lf[$uname], 'is');
				}
				else
				{
					$lf[$uname] == 2;
				}
				break;
			case 'select':
			case 'radio':
			case 'country':
				if (isset($lf[$uname]) && !empty($lf[$uname]) && ($lf[$uname] != 'lf_nomatter') && $lf[$uname][0] != 'lf_nomatter')
				{
					$lf[$uname] = array_unique($lf[$uname]);
					if (is_array($lf[$uname]) && count($lf[$uname]) == 1)
					{
						$lf[$uname] = $lf[$uname][0];
					}
					$lf_sql_str[] = generate_sql_query($uname, $lf[$uname], 'array');
				}
				else
				{
					$lf[$uname] == 'lf_nomatter';
				}
				break;
			case 'datetime':
				if (isset($lf[$uname]['isset']) && $lf[$uname]['isset'] != '')
				{
					$lf_sql_str[] = generate_sql_query($uname, $lf[$uname]['isset'], 'isset');
				}
				if (isset($lf[$uname]['is']) && is_array($lf[$uname]['is']))
				{
					$lf[$uname]['is'] = cot_import_date($lf[$uname]['is'], true, false, 'D');
					$lf_sql_str[] = generate_sql_query($uname, $lf[$uname]['is'], 'is');
				}
				if (isset($lf[$uname]['more']) && is_array($lf[$uname]['more']))
				{
					$lf[$uname]['more'] = cot_import_date($lf[$uname]['more'], true, false, 'D');
					$lf_sql_str[] = generate_sql_query($uname, $lf[$uname]['more'], 'more');
				}
				if (isset($lf[$uname]['less']) && is_array($lf[$uname]['less']))
				{
					$lf[$uname]['less'] = cot_import_date($lf[$uname]['less'], true, false, 'D');
					$lf_sql_str[] = generate_sql_query($uname, $lf[$uname]['less'], 'less');
				}
				break;
			case 'inputint':
			case 'currency':
			case 'double':
				if (isset($lf[$uname]['isset']) && $lf[$uname]['isset'] != '')
				{
					$lf_sql_str[] = generate_sql_query($uname, $lf[$uname]['isset'], 'isset');
				}
				if (isset($lf[$uname]['is']) && $lf[$uname]['is'] != '')
				{
					$lf_sql_str[] = generate_sql_query($uname, $lf[$uname]['is'], 'is');
				}
				if (isset($lf[$uname]['more']) && $lf[$uname]['more'] != '')
				{
					$lf_sql_str[] = generate_sql_query($uname, $lf[$uname]['more'], 'more');
				}
				if (isset($lf[$uname]['less']) && $lf[$uname]['less'] != '')
				{
					$lf_sql_str[] = generate_sql_query($uname, $lf[$uname]['less'], 'less');
				}
				break;
			case 'input':
			case 'textarea':
			case 'file':
			default:
				if (isset($lf[$uname]['isset']) && $lf[$uname]['isset'] != '')
				{
					$lf_sql_str[] = generate_sql_query($uname, $lf[$uname]['isset'], 'isset');
				}
				if (isset($lf[$uname]['is']) && $lf[$uname]['is'] != '')
				{
					$lf_sql_str[] = generate_sql_query($uname, $lf[$uname]['is'], 'is');
				}
				if (isset($lf[$uname]['like']) && $lf[$uname]['like'] != '')
				{
					$lf_sql_str[] = generate_sql_query($uname, $lf[$uname]['like'], 'like');
				}
				break;
		}
	}

	$lf_url_path = array();
	foreach ($lf as $k => $v)
	{
		if (is_array($v))
		{
			foreach ($v as $sk => $sv)
			{
				if (is_array($sv))
				{
					foreach ($sv as $ssk => $ssv)
					{
						$lf_url_path['lf['.$k.']['.$sk.']['.$ssk.']'] = $ssv;
					}
				}
				else
				{
					$lf_url_path['lf['.$k.']['.$sk.']'] = $sv;
				}
				
			}
		}
		else
		{
			$lf_url_path['lf['.$k.']'] = $v;
		}
	}

	if (!empty($lf_sql_str))
	{
		$lf_sql_str = array_diff($lf_sql_str, array(''));
		$where['cat'] = (!empty($lf_sql_cat)) ? $lf_sql_cat : $where['cat'];
		if (count($lf_sql_str) > 0)
		{
			$where['lf'] = implode(" AND ", $lf_sql_str);
		}
		$list_url_path += $lf_url_path;
	}

 
}


?>