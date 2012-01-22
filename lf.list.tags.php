<?php

/**
 * [BEGIN_COT_EXT]
 * Hooks=page.list.tags, page.tags, index.tags
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
require_once(cot_langfile('lf'));
require_once cot_incfile('forms');
global $lf_fields;

foreach ($lf_fields as $row)
{
	$uname = strtoupper($row['field_name']);
	switch ($row['field_type'])
	{
		case 'inputint':
		case 'currency':
		case 'double':
			$t->assign(array(
				'LIST_FILTERS_'.$uname => cot_inputbox('text', 'lf['.$row['field_name'].'][is]', $lf[$row['field_name']]['is']),
				'LIST_FILTERS_'.$uname.'_MORE' => cot_inputbox('text', 'lf['.$row['field_name'].'][more]', $lf[$row['field_name']]['more']),
				'LIST_FILTERS_'.$uname.'_LESS' => cot_inputbox('text', 'lf['.$row['field_name'].'][less]', $lf[$row['field_name']]['less']),
				'LIST_FILTERS_'.$uname.'_ISSET' => cot_checkbox($lf[$row['field_name']]['isset'], 'lf['.$row['field_name'].'][isset]'),
			));
			break;
		case 'datetime':
			$t->assign(array(
				'LIST_FILTERS_'.$uname => cot_selectbox_date($lf[$row['field_name']]['is'], 'long', 'lf['.$row['field_name'].'][is]'),
				'LIST_FILTERS_'.$uname.'_MORE' => cot_selectbox_date($lf[$row['field_name']]['more'], 'long', 'lf['.$row['field_name'].'][more]'),
				'LIST_FILTERS_'.$uname.'_LESS' => cot_selectbox_date($lf[$row['field_name']]['less'], 'long', 'lf['.$row['field_name'].'][less]'),
				'LIST_FILTERS_'.$uname.'_SHORT' => cot_selectbox_date($lf[$row['field_name']]['is'], 'long', 'lf['.$row['field_name'].'][is]'),
				'LIST_FILTERS_'.$uname.'_MORESHORT' => cot_selectbox_date($lf[$row['field_name']]['more'], 'short', 'lf['.$row['field_name'].'][more]'),
				'LIST_FILTERS_'.$uname.'_LESSSHORT' => cot_selectbox_date($lf[$row['field_name']]['less'], 'short', 'lf['.$row['field_name'].'][less]'),
				'LIST_FILTERS_'.$uname.'_ISSET' => cot_checkbox($lf[$row['field_name']]['isset'], 'lf['.$row['field_name'].'][isset]'),
			));
			break;
		case 'file':
		case 'input':
			$t->assign(array(
				'LIST_FILTERS_'.$uname => cot_inputbox('text', 'lf['.$row['field_name'].'][is]', $lf[$row['field_name']]['is']),
				'LIST_FILTERS_'.$uname.'_LIKE' => cot_inputbox('text', 'lf['.$row['field_name'].'][like]', $lf[$row['field_name']]['like']),
				'LIST_FILTERS_'.$uname.'_ISSET' => cot_checkbox($lf[$row['field_name']]['isset'], 'lf['.$row['field_name'].'][isset]'),
			));
			break;
		case 'checkbox':
			$R['checkbox_res'] = $R['input_checkbox'];
			$R['input_checkbox'] = '<label><input type="checkbox" name="{$name}" value="{$value}"{$checked}{$attrs} /> {$title}</label>';
			$cfg_params_titles = (isset($L['lf_'.$row['field_name'].'_params']) && is_array($L['lf_'.$row['field_name'].'_params'])) ? $L['lf_'.$row['field_name'].'_params'] : $L['lf_checkbox'];
			$t->assign(array(
				'LIST_FILTERS_'.$uname => cot_selectbox((isset($lf[$row['field_name']])) ? $lf[$row['field_name']] : 2, 'lf['.$row['field_name'].']', range(0, 2), $cfg_params_titles, false),
				'LIST_FILTERS_'.$uname.'_CHECK' => cot_checkbox((isset($lf[$row['field_name']])) ? $lf[$row['field_name']] : 0, 'lf['.$row['field_name'].']', isset($L['page_'.$row['field_name'].'_title']) ? $L['page_'.$row['field_name'].'_title'] : $row['field_description'])
			));
			break;
		case 'select':
		case 'radio':
		case 'country':
			$opt_array = explode(',', $row['field_variants']);
			$options = array();
			$options_titles = array();

			$options[] = 'lf_nomatter';
			$options_titles[] = $L['lf_nomatter'];
			if ($row['field_type'] != 'country')
			{
				if (count($opt_array) != 0)
				{
					foreach ($opt_array as $var)
					{
						$options_titles[] = (!empty($L['page_'.$row['field_name'].'_'.$var])) ? $L['page_'.$row['field_name'].'_'.$var] : $var;
						$options[] = $var;
					}
				}
			}
			else
			{
				if (!$cot_countries)
					include_once cot_langfile('countries', 'core');
				$options[] = array_keys($cot_countries);
				$options_titles[] = array_values($cot_countries);
			}
			//options gen
			$lfmiltiadd = '';
			$lf[$row['field_name']] = (is_string($lf[$row['field_name']]) && !empty($lf[$row['field_name']])) ? array($lf[$row['field_name']]) : $lf[$row['field_name']];
			$lf[$row['field_name']] = (is_array($lf[$row['field_name']])) ? $lf[$row['field_name']] : array('lf_nomatter');
			foreach ($lf[$row['field_name']] as $lfval)
			{
				$lfmiltiadd .= '<div class="option'.$row['field_name'].'">
'.cot_selectbox($lfval, 'lf['.$row['field_name'].'][]', $options, $options_titles, false, '').'<button name="deloption" type="button" class="deloption'.$row['field_name'].'" title="'.$L['Delete'].'" style="display:none;"><img src="'.$cfg['plugins_dir'].'/lf/img/minus.png" alt="'.$L['Delete'].'" /></button>
</div>';
			}
			$lfselval = ((is_array($lf[$row['field_name']]) && count($lf[$row['field_name']]) > 0)) ? $lf[$row['field_name']][0] : 'lf_nomatter';
			//end options gen
			$t->assign(array(
				'LIST_FILTERS_'.$uname => cot_selectbox($lfselval, 'lf['.$row['field_name'].'][]', $options, $options_titles, false),
				'LIST_FILTERS_'.$uname.'_MULTI' => cot_selectbox((isset($lf[$row['field_name']])) ? $lf[$row['field_name']] : 'lf_nomatter', 'lf['.$row['field_name'].'][]', $options, $options_titles, false, ' multiple="multiple"'),
				'LIST_FILTERS_'.$uname.'_RADIO' => cot_radiobox($lfselval, 'lf['.$row['field_name'].'][]', $options, $options_titles, false),
				'LIST_FILTERS_'.$uname.'_MULTIADD' => $lfmiltiadd.'<button id="addoption'.$row['field_name'].'" name="addoption" type="button" title="'.$L['Add'].'" style="display:none;"><img src="'.$cfg['plugins_dir'].'/lf/img/plus.png" alt="'.$L['Add'].'" /></button>
<script type="text/javascript">
$(".deloption'.$row['field_name'].'").live("click",function () {
	$(this).parent().children("select").attr("value", "lf_nomatter");
	if ($(".option'.$row['field_name'].'").length > 1)
	{
		$(this).parent().remove();
	}
	return false;
});

$(document).ready(function(){
	$("#addoption'.$row['field_name'].'").click(function () {
	$(".option'.$row['field_name'].'").last().clone().insertAfter($(".option'.$row['field_name'].'").last()).show().children("select").attr("value","lf_nomatter");
	return false;
	});
	$("#addoption'.$row['field_name'].'").show();
	$(".deloption'.$row['field_name'].'").show();
});
</script>',
			));
			break;
		case 'textarea':
		default:
			$t->assign(array(
				'LIST_FILTERS_'.$uname => cot_inputbox('text', 'lf['.$row['field_name'].'][is]', $lf[$row['field_name']]['is']),
				'LIST_FILTERS_'.$uname.'_LIKE' => cot_inputbox('text', 'lf['.$row['field_name'].'][like]', $lf[$row['field_name']]['like']),
				'LIST_FILTERS_'.$uname.'_ISSET' => cot_checkbox($lf[$row['field_name']]['isset'], 'lf['.$row['field_name'].'][isset]'),
			));
			break;
	}

	$t->assign('LIST_FILTERS_'.$uname.'_TITLE', isset($L['page_'.$row['field_name'].'_title']) ? $L['page_'.$row['field_name'].'_title'] : $row['field_description']);
}

$lfparams = $_GET;
unset($lfparams['lf']);
foreach ($lfparams as $key => $val)
{
	$lfhidden .= cot_inputbox('hidden', $key, $val);
}

$t->assign(array(
	'LIST_FILTERS_HIDDEN' => $lfhidden,
	'LIST_FILTERS_CAT' => cot_selectbox((!isset($lf['lfcat'])) ? 'all' : $lf['lfcat'], 'lf[lfcat][]', array_keys($lf_pages_cat_list), array_values($lf_pages_cat_list), false, ' multiple="multiple" style="width:50%"'),
	'LIST_FILTERS_SEARCH' => cot_inputbox('text', 'lf[search]', $lf['search']),
	'LIST_FILTERS_URL' => cot_url('page', "c=$c&s=$s&w=$w&o=$o&p=$p")
));
$t->parse('MAIN.LIST_FILTERS');
?>