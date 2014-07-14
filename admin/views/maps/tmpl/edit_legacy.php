<?php
	defined('_JEXEC') or die('Restricted access');
	$document = JFactory::getDocument();
	$document->addScript("http://maps.google.com/maps/api/js?sensor=false");
	$document->addScript("components/com_maps/javascript/maps.js", "text/javascript", true);
	JHtml::_('behavior.modal');
	JHtml::_('behavior.tooltip');
	JHtml::_('behavior.formvalidation');
	JHtml::_('behavior.keepalive');
	$uri	= JURI::getInstance();
	$base	= $uri->root();
	$style	= "";
	if($width = $this->form->getValue('map_width', 'params', 0)){
		$style .= "width: {$width}px;";
	}
	if($height = $this->form->getValue('map_height', 'params', 0)){
		$style .= "height: {$height}px;";
	}
?>

<script type="text/javascript">
//<![CDATA[
	Joomla.submitbutton = function(sometask){
		var someForm = document.forms.adminForm;
		var re_empty = /(\w+)/;
		var re_slug = /^([\w-]+)$/;
		var re_blank = /^(\W*)$/;
		var re_float = /\d+$/;
		if(sometask != 'maps.cancel'){
			if(!re_empty.test($('jform_maps_name').value)){
				$('jform_maps_name').focus();
				alert("Map Title is a required field");
				return false;
			}
			if(!re_slug.test($('jform_maps_alias').value)){
				if(re_blank.test($('jform_maps_alias').value)){
					$('jform_maps_alias').value = $('jform_maps_name').value.replace(/\W/g, '-').toLowerCase();
				}else{
					$('jform_maps_alias').focus();
					alert('The Alias is required for proper operation. It cannot be left blank. It must contain only letters, numbers, underscores, or dashes');
					return false;
				}
			}
			if(!re_float.test($('jform_params_map_width').value)){
				$('jform_params_map_width').focus();
				alert("Map Width is a required field");
				return false;
			}
			if(!re_float.test($('jform_params_map_height').value)){
				$('jform_params_map_height').focus();
				alert("Map Height is a required field");
				return false;
			}
			if(!re_float.test($('jform_params_center_lat').value)){
				$('jform_params_center_lat').focus();
				alert("Center Latitude is a required field");
				return false;
			}
			if(!re_float.test($('jform_params_center_lng').value)){
				$('jform_params_center_lng').focus();
				alert("Center Longitude is a required field");
				return false;
			}
			if(!re_float.test($('jform_params_zoom').value)){
				$('jform_params_zoom').focus();
				alert("Zoom Level is a required field");
				return false;
			}
		}
		
		someForm.task.value = sometask;
		someForm.submit();
	}
//]]>
</script>
<form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
	<input type="hidden" name="option" value="com_maps" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="hidemainmenu" value="0" />
	<input type="hidden" name="maps_id" value="<? echo $this->form->getValue('maps_id'); ?>" />
	<? echo JHTML::_('form.token')."\n"; ?>
	<div id="editcell">
		<div class="width-60 fltlft">
			<fieldset class="adminform">
				<legend><?php echo JText::_('COM_MAPS_FORM_LEGEND_BASIC'); ?></legend>
				<dl>
				<?php foreach($this->form->getFieldset('base') as $field){ ?>
					<dt><?php echo $field->label; ?></dt>
					<dd><?php echo $field->input; ?></dd>
				<?php } ?>
				</dl>
			</fieldset>
			<fieldset>
				<legend><?php echo JText::_('COM_MAPS_FORM_LEGEND_PREVIEW'); ?></legend>
				<div id="map-preview_" style="<?php echo $style; ?>"></div>
			</fieldset>
		</div>
		<div class="width-40 fltlft">
			<fieldset class="adminform">
				<legend><?php echo JText::_('COM_MAPS_FORM_LEGEND_OPTIONS'); ?></legend>
				<dl>
				<?php foreach($this->form->getFieldset('options') as $field){ ?>
					<dt><?php echo $field->label; ?></dt>
					<dd><?php echo $field->input; ?></dd>
				<?php } ?>
				</dl>
			</fieldset>
			<fieldset class="adminform">
				<legend><?php echo JText::_('COM_MAPS_FORM_LEGEND_PARAMS'); ?></legend>
				<dl>
				<?php foreach($this->form->getFieldset('params') as $field){ ?>
					<dt><?php echo $field->label; ?></dt>
					<dd><?php echo $field->input; ?></dd>
				<?php } ?>
				</dl>
			</fieldset>
		</div>
	</div>
</form>
