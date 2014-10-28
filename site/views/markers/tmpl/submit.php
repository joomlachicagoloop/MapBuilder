<?php
	// NO DIRECT ACCESS
	defined( '_JEXEC' ) or die( 'Restricted access' );
	JHtml::_('behavior.formvalidation');
	// SET DOCUMENT HEAD FOR PAGE
	$document = JFactory::getDocument();
	$document->addScript("//www.google.com/jsapi");
	$options = JComponentHelper::getParams('com_mapbuilder');
	switch($options->get('params.js_framework')){
	case 'mootools':
		// LOAD THE MOOTOOLS FRAMEWORK
		JHtml::_('behavior.framework');
		$document->addScript("media/mapbuilder/javascript/markers.js", "text/javascript", true);
		break;
	case 'jquery':
		// LOAD THE jQUERY FRAMEWORK
		if(class_exists('JHtmlBootstrap')){
		    JHtml::_('bootstrap.framework');
		}else{
		    $document->addScript("media/mapbuilder/javascript/jquery.min.js");
		}
		$document->addScript("media/mapbuilder/javascript/markers-jquery.js", "text/javascript", true);
		break;
	}
	$style = "#map-id-".$this->map->map_id." {";
	if($width = $this->params->get('map_width', 0)){
		$style .= "width: 100%;";
	}
	if($height = $this->params->get('map_height', 0)){
		$style .= "height: {$height}px;";
	}
	$style .= "}";
	$document->addStyleDeclaration($style);
	$document->addStyleSheet("/media/mapbuilder/css/submit-bootstrap-2.css");
?>
<form action="/index.php" method="post" id="mapbuilder-submit-form" class="form-validate">
	<input type="hidden" name="option" value="com_mapbuilder" />
	<input type="hidden" name="layout" value="submit" />
	<input type="hidden" name="marker_id" value="" />
	<input type="hidden" name="map_id" value="<?php echo $this->map->map_id; ?>" />
	<input type="hidden" name="task" value="markers.save" />
	<?php
	foreach($this->form->getFieldset('hidden') as $field){
		echo $field->renderField();
	}
	?>
	<?php echo JHTML::_('form.token')."\n"; ?>
	<div class="row-fluid">
		<div class="span6">
			<div class="control-group">
				<div class="control-label">
					<label for="location_search"><?php echo JText::_('COM_MAPBUILDER_SEARCH_INPUT_LABEL'); ?></label>
				</div>
				<div class="controls">
					<div class="input-append">
						<input type="text" name="location_search" id="location_search" placeholder="<?php echo JText::_('COM_MAPBUILDER_SEARCH_INPUT_PLACEHOLDER'); ?>" value="" />
						<button class="btn" type="button" id="geolocation"><i class="icon-location"></i></button>
					</div>
				</div>
			</div>		
			<?php
			foreach($this->form->getFieldset('base') as $field){
				echo $field->renderField();
			}
			?>
			<div class="control-group">
				<div class="controls">
					<button type="submit" class="btn btn-info validate">Submit</button>
					<button class="btn btn-success" id="mapbuilder-tracking">Start Tracking</button>
					<img id="mapbuilder-spinner" class="hidden" src="/media/mapbuilder/images/spinner.gif" alt="Loading..." title="" />
				</div>
			</div>
		</div>
		<div class="span6">
			<div class="mapbuilder" id="map-id-<?php echo $this->map->map_id; ?>" data-id="<?php echo $this->map->map_id; ?>" data-zoom="<?php echo $this->params->get('zoom'); ?>" data-lat="<?php echo $this->params->get('center_lat'); ?>" data-lng="<?php echo $this->params->get('center_lng'); ?>"></div>
		</div>
	</div>
</form>
