<?php
	defined('_JEXEC') or die('Restricted access');
	jimport('joomla.html.pane');
	$pane	=& JPane::getInstance('sliders');
	$uri	=& JURI::getInstance();
	$base	= $uri->root();
	$style	= "";
	if($width = $this->params->get('map_width', 0)){
		$style .= "width: {$width}px;";
	}
	if($height = $this->params->get('map_height', 0)){
		$style .= "height: {$height}px;";
	}
?>

<script type="text/javascript">
//<![CDATA[
	function submitbutton(sometask){
		var someForm = document.forms.adminForm;
		var re_empty = /(\w+)/;
		var re_slug = /^([\w-]+)$/;
		var re_blank = /^(\W*)$/;
		var re_float = /\d+$/;
		if(sometask != 'cancel'){
			if(!re_empty.test($('maps-name_').value)){
				$('maps-name_').focus();
				alert("Map Title is a required field");
				return false;
			}
			if(!re_slug.test($('maps-alias_').value)){
				if(re_blank.test($('maps-alias_').value)){
					$('maps-alias_').value = $('maps-name_').value.replace(/\W/g, '-').toLowerCase();
				}else{
					$('maps-alias_').focus();
					alert('The Alias is required for proper operation. It cannot be left blank. It must contain only letters, numbers, underscores, or dashes');
					return false;
				}
			}
			if(!re_float.test($('paramsmap_width').value)){
				$('paramsmap_width').focus();
				alert("Map Width is a required field");
				return false;
			}
			if(!re_float.test($('paramsmap_height').value)){
				$('paramsmap_height').focus();
				alert("Map Height is a required field");
				return false;
			}
			if(!re_float.test($('paramscenter_lat').value)){
				$('paramscenter_lat').focus();
				alert("Center Latitude is a required field");
				return false;
			}
			if(!re_float.test($('paramscenter_lng').value)){
				$('paramscenter_lng').focus();
				alert("Center Longitude is a required field");
				return false;
			}
			if(!re_float.test($('paramszoom').value)){
				$('paramszoom').focus();
				alert("Zoom Level is a required field");
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
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="hidemainmenu" value="0" />
	<input type="hidden" name="maps_id" value="<? echo $this->data->maps_id; ?>" />
	<input type="hidden" name="ordering" value="<? echo $this->data->ordering; ?>" />
	<? echo JHTML::_('form.token')."\n"; ?>
	<div id="editcell">
		<table class="admintable">
			<tr>
				<td valign="top">
					<fieldset class="adminform">
						<legend>Map Information</legend>
						<table class="admintable">
							<tr>
								<td class="key">
									<label for="maps-name_">Map Title</label>
								</td>
								<td>
									<input type="text" class="inputbox" name="maps_name" id="maps-name_" value="<? echo $this->data->maps_name; ?>" size="64" maxlength="96" />
								</td>
							</tr>
							<tr>
								<td class="key">
									<label for="maps-alias_">Alias</label>
								</td>
								<td>
									<input type="text" class="inputbox" name="maps_alias" id="maps-alias_" value="<? echo $this->data->maps_alias; ?>" size="64" maxlength="64" />
								</td>
							</tr>
							<tr>
								<td class="key">
									<label for="maps-description">Description</label>
								</td>
								<td>
									<textarea class="inputbox" name="maps_description" id="maps-description_" cols="45" rows="8"><? echo $this->data->maps_description; ?></textarea>
								</td>
							</tr>
						</table>
					</fieldset>
					<fieldset class="adminform">
						<legend>Map Preview</legend>
						<div id="map-preview_" style="<?php echo $style; ?>"></div>
					</fieldset>
				</td>
				<td valign="top">
<?
	echo $pane->startPane('params-pane');
	echo $pane->startPanel(JText::_('Map Options'), 'maps-options_');
	echo $this->params->render('options');
	echo $pane->endPanel();
	echo $pane->startPanel(JText::_('Map Settings'), 'social-options_');
	echo $this->params->render('params', 'params');
	echo $pane->endPanel();
	echo $pane->startPanel(JText::_('Meta Data'), 'meta-data_');
	echo $this->params->render('meta', 'metadata');
	echo $pane->endPanel();
	echo $pane->endPane();
?>
				</td>
			</tr>
		</table>
	</div>
</form>
