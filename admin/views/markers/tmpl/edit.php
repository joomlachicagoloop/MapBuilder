<?php
	defined('_JEXEC') or die('Restricted access');
	jimport('joomla.html.pane');
	$pane	=& JPane::getInstance('sliders');
	$uri	=& JURI::getInstance();
	$base	= $uri->root();
?>

<script type="text/javascript">
//<![CDATA[
	function submitbutton(sometask){
		var someForm = document.forms.adminForm;
		var re_empty = /(\w+)/;
		var re_slug = /^([\w-]+)$/;
		var re_blank = /^(\W*)$/;
		var re_coord = /\d+$/;
		if(sometask != 'cancel'){
			if(!re_empty.test($('marker-name_').value)){
				$('marker-name_').focus();
				alert("Marker Name is a required field");
				return false;
			}
			if(!re_slug.test($('marker-alias_').value)){
				if(re_blank.test($('marker-alias_').value)){
					$('marker-alias_').value = $('marker-name_').value.replace(/\W/g, '-').toLowerCase();
				}else{
					$('marker-alias_').focus();
					alert('The Alias is required for proper operation. It cannot be left blank. It must contain only letters, numbers, underscores, or dashes');
					return false;
				}
			}
			if(!re_coord.test($('optionsmarker_lat').getValue())){
				$('optionsmarker_lat').focus();
				alert("Latitude must not be blank. Click the map to drop a marker and drag to adjust coordinates.");
				return false;
			}
		}
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
		<table class="admintable">
			<tr>
				<td valign="top">
					<fieldset class="adminform">
						<legend>Map Marker</legend>
						<table class="admintable">
							<tr>
								<td class="key">
									<label for="marker-name_">Marker Name</label>
								</td>
								<td>
									<input type="text" class="inputbox" name="marker_name" id="marker-name_" value="<? echo $this->data->marker_name; ?>" size="64" maxlength="64" />
								</td>
							</tr>
							<tr>
								<td class="key">
									<label for="marker-alias_">Alias</label>
								</td>
								<td>
									<input type="text" class="inputbox" name="marker_alias" id="marker-alias_" value="<? echo $this->data->marker_alias; ?>" size="64" maxlength="96" />
								</td>
							</tr>
							<tr>
								<td class="key">
									<label for="marker-description_">Description</label>
								</td>
								<td>
									<textarea name="marker_description" id="marker-description_" cols="55" rows="10"><? echo $this->data->marker_description; ?></textarea>
								</td>
							</tr>
						</table>
					</fieldset>
					<fieldset class="adminform">
						<legend>Map Preview</legend>
						<div id="map-preview_"></div>
					</fieldset>
				</td>
				<td valign="top">
<?
	echo $pane->startPane('params-pane');
	echo $pane->startPanel(JText::_('Marker Options'), 'marker-options_');
	echo $this->params->render('options');
	echo $pane->endPanel();
	echo $pane->endPane();
?>
				</td>
			</tr>
		</table>
	</div>
</form>
