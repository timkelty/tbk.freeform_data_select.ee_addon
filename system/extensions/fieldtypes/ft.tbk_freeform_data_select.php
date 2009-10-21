<?php

if ( ! defined('EXT')) exit('Invalid file request');

/**
 * Freeform Data Select
 */
class Tbk_freeform_data_select extends Fieldframe_Fieldtype {

  /**
   * Fieldtype Info
   * @var array
   */
	var $info = array(
		'name'     => 'Freeform Data Select',
		'version'  => '1.0',
		'no_lang'  => TRUE
	);
  var $default_field_settings = array(
  	'input_type' => 'select',
  	'data_type' => 'fields'
  );

  var $default_cell_settings = array(
  	'input_type' => 'select',
  	'data_type' => 'fields'
  );
  var $input_types = array(
		'select'       => 'Drop-down List',
		'multi'        => 'Multi-select List',
	);
	var $data_types = array(
		'fields'       => 'Freeform Fields',
		'templates'    => 'Freeform Templates',
	);
	
  /**
   * Display Field Settings
   * 
   * @param  array  $field_settings  The field's settings
   * @return array  Settings HTML (cell1, cell2, rows)
   */
  function display_field_settings($field_settings)
  {
		return array('cell2' => $this->display_cell_settings($field_settings));
  }

  /**
   * Display Cell Settings
   * 
   * @param  array  $cell_settings  The cell's settings
   * @return array  Settings HTML
   */
  function display_cell_settings($cell_settings)
  {
    global $DSP;
  	// initialize Fieldframe_SettingsDisplay
  	$SD = new Fieldframe_SettingsDisplay();

  	return  '<label>'
            . $DSP->qdiv('itemWrapper defaultBold', 'Data Type')
            . $SD->select('data_type', $cell_settings['data_type'], $this->data_types)
        	  . '</label>'
      	    .  '<label>'
        	  . $DSP->qdiv('itemWrapperTop defaultBold', 'Input Type')
        	  . $SD->select('input_type', $cell_settings['input_type'], $this->input_types)
        	  . '</label>'
        	  . '<div style="height: 5px;"></div>';
  }

  /**
   * Display Field
   * 
   * @param  string  $field_name      The field's name
   * @param  mixed   $field_data      The field's current value
   * @param  array   $field_settings  The field's settings
   * @return string  The field's HTML
   */
  function display_field($field_name, $field_data, $field_settings)
  {
    global $DSP, $DB, $FF;
    $field_name = $field_name . ($field_settings['input_type'] == 'multi' ? '[]' : '');
    
    $r = ($FF->row['field_required'] == 'y') ? '' : $DSP->input_select_option('', '--');
    
    if ($field_settings['data_type'] == 'templates') {
      $templates_q = $DB->query("SELECT ft.template_name, ft.template_label
                                 FROM   exp_freeform_templates ft");

      if ($templates_q->num_rows)
      {
        $multi_row_count = 0;
        foreach($templates_q->result as $template)
        {
          if (is_array($field_data)) {
            $selected = in_array($template['template_name'], $field_data) ? 'y' : '';  
          }
          else {
            $selected = ($template['template_name'] == $field_data) ? 'y' : '';  
          }
          $r .= $DSP->input_select_option($template['template_name'], $template['template_label'], $selected);
          $multi_row_count++;
        }
        $r = $DSP->input_select_header($field_name, ($field_settings['input_type'] == 'multi' ? 'y' : ''), ($multi_row_count < 15 ? $multi_row_count : 15), 'auto')
        . $r
        . $DSP->input_select_footer();
      }
      else {
       $r .= $DSP->qdiv('highlight_alt', 'No Freeform templates exist.');
      }
    }
    else {
      $fields_q = $DB->query("SELECT ff.name, ff.label
                              FROM   exp_freeform_fields ff
                              ORDER BY ff.field_order");

      if ($fields_q->num_rows)      
      {
        $multi_row_count = 0;
        foreach($fields_q->result as $field)
        {
          if (is_array($field_data)) {
            $selected = in_array($field['name'], $field_data) ? 'y' : '';  
          }
          else {
            $selected = ($field['name'] == $field_data) ? 'y' : '';  
          }
          $r .= $DSP->input_select_option($field['name'], $field['label'], $selected);
          $multi_row_count++;
        }

        $r = $DSP->input_select_header($field_name, ($field_settings['input_type'] == 'multi' ? 'y' : ''), ($multi_row_count < 15 ? $multi_row_count : 15), 'auto')
            . $r
            . $DSP->input_select_footer();
      }
      else {
       $r .= $DSP->qdiv('highlight_alt', 'No Freeform fields exist.');
      }
    }
    return $r;
	}

	/**
	 * Display Cell
	 * 
	 * @param  string  $cell_name      The cell's name
	 * @param  mixed   $cell_data      The cell's current value
	 * @param  array   $cell_settings  The cell's settings
	 * @return string  The cell's HTML
	 */
	function display_cell($cell_name, $cell_data, $cell_settings)
	{
		return $this->display_field($cell_name, $cell_data, $cell_settings);
	}
	
	/**
	 * Save Field
	 * 
	 * @param  mixed  $field_data      The field's current value
	 * @param  array  $field_settings  The field's settings
	 * @return mixed	Modified or same $field_data
	 */
  function save_field($field_data, $field_settings)
  {
   // remove empty data
   if (is_array($field_data)) {
    $field_data = array_filter($field_data);
   }
      
   return $field_data;
  }
  /**
	 * Save Cell
	 *
	 * @param  mixed   $cell_data      The cell's current value
	 * @param  array   $cell_settings  The cell's settings
	 * @return mixed   Modified $cell_data
	 */
	function save_cell($cell_data, $cell_settings)
	{
		return $this->save_field($cell_data, $cell_settings);
	}
	
}