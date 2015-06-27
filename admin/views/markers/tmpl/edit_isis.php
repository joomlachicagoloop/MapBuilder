<?php
	defined('_JEXEC') or die('Restricted access');
	$document = JFactory::getDocument();
	$document->addScript("http://maps.google.com/maps/api/js?sensor=false");
	$document->addScript("components/com_mapbuilder/javascript/markers.js", "text/javascript", true);
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
					var re_blank = /^(\W*)$/;
					if(sometask != 'markers.cancel'){
						if(re_blank.test($('#jform_marker_alias').val())){
							$('#jform_marker_alias').val($('#jform_marker_name').val().replace(/\W/g, '-').toLowerCase());
						}
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
	<input type="hidden" name="marker_id" value="<?php echo $this->form->getValue('marker_id'); ?>" />
	<?php echo $this->form->renderField('map_width', 'params'); ?>
	<?php echo $this->form->renderField('map_height', 'params'); ?>
	<?php echo $this->form->renderField('center_lat', 'params'); ?>
	<?php echo $this->form->renderField('center_lng', 'params'); ?>
	<?php echo $this->form->renderField('zoom', 'params'); ?>
	
	
	
	
	<?php echo JHTML::_('form.token')."\n"; ?>
	
	<div class="form-linline form-inline-header">
	    <?php echo $this->form->renderField('marker_name'); ?>
	    <?php echo $this->form->renderField('marker_alias'); ?>
	</div>
	<div id="editcell" class="form-horizontal">
	    <?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'marker')); ?>
	    
	    <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'marker', JText::_('COM_MAPBUILDER_FORM_LEGEND_SETTINGS', true)); ?>
        <div class="row-fluid">
            <div class="span5">
                <?php echo $this->form->renderField('map_id'); ?>
	            <?php echo $this->form->renderField('marker_lat'); ?>
	            <?php echo $this->form->renderField('marker_lng'); ?>
            </div>
            <div class="span7">
                <div id="map-preview_"></div>
            </div>
        </div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>
		
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'options', JText::_('COM_MAPBUILDER_FORM_LEGEND_MARKER', true)); ?>
		<div class="row-fluid">
		    <div class="span5">
                <?php echo $this->form->renderField('published'); ?>
                <?php echo $this->form->renderField('access'); ?>
                <?php echo $this->form->renderField('icon_id'); ?>
                <?php echo $this->form->renderField('icon_color'); ?>
		    </div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>
		
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'pagecontent', JText::_('COM_MAPBUILDER_FORM_LEGEND_CONTENT', true)); ?>
		<div class="row-fluid">
		    <div class="span12">
		        <?php echo $this->form->getInput('marker_description'); ?>

		    </div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>
		
		<?php echo JHtml::_('bootstrap.endTabSet'); ?>
	</div>
</form>
