<?php
	defined('_JEXEC') or die('Restricted access');
	$uri	= JURI::getInstance();
	$base	= $uri->root();
	JHtml::_('behavior.keepalive');
	$document = JFactory::getDocument();
	$document->addScript("http://maps.google.com/maps/api/js?sensor=false&amp;key={$api_key}");
	$document->addScript("components".DS."com_maps".DS."javascript".DS."markers.js", "text/javascript", true);
?>

<script type="text/javascript">
//<![CDATA[
	Joomla.submitbutton = function(sometask){
		var someForm = document.forms.adminForm;
		var re_empty = /(\w+)/;
		var re_slug = /^([\w-]+)$/;
		var re_blank = /^(\W*)$/;
		var re_coord = /\d+$/;
		if(sometask != 'cancel'){
			if(!re_empty.test($('marker_name').value)){
				$('marker_name').focus();
				alert("Marker Name is a required field");
				return false;
			}
			if(!re_slug.test($('marker_alias').value)){
				if(re_blank.test($('marker_alias').value)){
					$('marker_alias').value = $('marker_name').value.replace(/\W/g, '-').toLowerCase();
				}else{
					$('marker_alias').focus();
					alert('The Alias is required for proper operation. It cannot be left blank. It must contain only letters, numbers, underscores, or dashes');
					return false;
				}
			}
			if(!re_coord.test($('marker_lat').value)){
				$('marker_lat').focus();
				alert("Latitude must not be blank. Click the map to drop a marker and drag to adjust coordinates.");
				return false;
			}
		}
		<?php echo $this->form->getField('marker_description')->save(); ?>
		someForm.task.value = sometask;
		someForm.submit();
	}
//]]>
</script>
<form action="index.php" method="post" name="adminForm" enctype="multipart/form-data">
	<input type="hidden" name="option" value="com_maps" />
	<input type="hidden" name="controller" value="markers" />
	<input type="hidden" name="view" value="markers" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="hidemainmenu" value="0" />
	<input type="hidden" name="marker_id" value="<? echo $this->data->marker_id; ?>" />
	<input type="hidden" name="ordering" value="<? echo $this->data->ordering; ?>" />
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
				<div class="clr"></div>
				<?php echo $this->form->getLabel('marker_description'); ?>
				<div class="clr"></div>
				<?php echo $this->form->getInput('marker_description'); ?>
			</fieldset>
			<fieldset>
				<legend><?php echo JText::_('COM_MAPS_FORM_LEGEND_PREVIEW'); ?></legend>
				<div id="map-preview_"></div>
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
				<?php foreach($this->form->getFieldset('params') as $field){
					echo $field->input;
				} ?>
			</fieldset>
		</div>
	</div>
</form>
