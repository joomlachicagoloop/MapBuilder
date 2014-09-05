<?php
	defined('_JEXEC') or die('Restricted access');
	$document = JFactory::getDocument();
	$document->addScript("http://maps.google.com/maps/api/js?sensor=false");
	$document->addScript("components".DS."com_mapbuilder".DS."javascript".DS."markers.js", "text/javascript", true);
	JHtml::_('behavior.keepalive');
	JHtml::_('bootstrap.tooltip');
	JHtml::_('behavior.formvalidation');
?>

<script type="text/javascript">
//<![CDATA[
	(function() {
		var $, UIView;
		$ = jQuery;
		UIView = (function() {
			function UIView() {
				// CONSTRUCTOR METHOD
				document.formvalidator.setHandler('uint', function(value){
					re_uint = /^\d+$/;
					return re_uint.test(value);
				});
				document.formvalidator.setHandler('string', function(value){
					re_string = /^([\w\d\s-_\.,&'#\u00E0-\u00FC]+)?$/;
					return re_string.test(value);
				});
				document.formvalidator.setHandler('cmd', function(value){
					re_cmd = /^([\w-_]+)$/;
					return re_cmd.test(value);
				});
				Joomla.submitbutton = function (sometask){
					var someForm = document.forms.adminForm;
					if(sometask != 'markers.cancel'){
						if(!document.formvalidator.isValid(someForm)){
							return false;
						}
					}
					<?php echo $this->form->getField('marker_description')->save(); ?>
					someForm.task.value = sometask;
					someForm.submit();
				}
			}
		
			return UIView;
		})();
	
		$(function() {
			return new UIView();
		});
	}).call(this);

//]]>
</script>
<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
	<input type="hidden" name="option" value="com_mapbuilder" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="chosen" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="hidemainmenu" value="0" />
	<input type="hidden" name="marker_id" value="<? echo $this->form->getValue('marker_id'); ?>" />
	<? echo JHTML::_('form.token')."\n"; ?>
	<div id="editcell">
		<div class="span9 pull-left">
			<fieldset class="adminform">
				<legend><?php echo JText::_('COM_MAPBUILDER_FORM_LEGEND_BASIC'); ?></legend>
				<?php foreach($this->form->getFieldset('base') as $field){ ?>
					<div class="control-group">
						<?php echo $field->label; ?>
						<div class="controls"><?php echo $field->input; ?></div>
					</div>
				<?php } ?>
				<div class="clr"></div>
				<?php echo $this->form->getLabel('marker_description'); ?>
				<div class="clr"></div>
				<?php echo $this->form->getInput('marker_description'); ?>
			</fieldset>
			<fieldset>
				<legend><?php echo JText::_('COM_MAPBUILDER_FORM_LEGEND_PREVIEW'); ?></legend>
				<div id="map-preview_"></div>
			</fieldset>
		</div>
		<div class="span3 pull-left">
			<fieldset class="adminform">
				<legend><?php echo JText::_('COM_MAPBUILDER_FORM_LEGEND_OPTIONS'); ?></legend>
				<?php foreach($this->form->getFieldset('options') as $field){ ?>
					<div class="control-group">
						<?php echo $field->label; ?>
						<div class="controls"><?php echo $field->input; ?></div>
					</div>
				<?php } ?>
				<?php foreach($this->form->getFieldset('params') as $field){ ?>
					<div class="control-group">
						<?php echo $field->label; ?>
						<div class="controls"><?php echo $field->input; ?></div>
					</div>
				<?php } ?>
			</fieldset>
		</div>
	</div>
</form>
