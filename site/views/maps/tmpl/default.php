<?php
	// NO DIRECT ACCESS
	defined( '_JEXEC' ) or die( 'Restricted access' );
	// SET DOCUMENT HEAD FOR PAGE
	$document = JFactory::getDocument();
	$document->addScript("//www.google.com/jsapi");
	$options = JComponentHelper::getParams('com_mapbuilder');
	switch($options->get('params.js_framework')){
	case 'mootools':
		// LOAD THE MOOTOOLS FRAMEWORK
		JHtml::_('behavior.framework');
		$document->addScript("media/mapbuilder/javascript/maps.js", "text/javascript", true);
		break;
	case 'jquery':
		// LOAD THE jQUERY FRAMEWORK
		if(class_exists('JHtmlBootstrap')){
		    JHtml::_('bootstrap.framework');
		}else{
		    $document->addScript("media/mapbuilder/javascript/jquery.min.js");
		}
		$document->addScript("media/mapbuilder/javascript/maps-jquery.js", "text/javascript", true);
		break;
	}
	if(trim($this->map->meta_keywords)){
		$document->setMetaData('keywords', $this->map->meta_keywords);
	}
	if(trim($this->map->meta_description)){
		$document->setMetaData('description', $this->map->meta_description);
	}
	$style = "#map-id-".$this->map->map_id." {";
	if($width = $this->params->get('map_width', 0)){
		$style .= "width: {$width}px;";
	}
	if($height = $this->params->get('map_height', 0)){
		$style .= "height: {$height}px;";
	}
	$style .= "}";
	$document->addStyleDeclaration($style);
	if($this->params->get('show_title')){
?>

<h1><? echo $this->map->map_name; ?></h1>

<?	} ?>
<div class="mapbuilder" id="map-id-<? echo $this->map->map_id; ?>" data-id="<? echo $this->map->map_id; ?>" data-zoom="<? echo $this->params->get('zoom'); ?>" data-lat="<? echo $this->params->get('center_lat'); ?>" data-lng="<? echo $this->params->get('center_lng'); ?>"></div>

<?	if($this->params->get('show_description')){ ?>
<p><? echo $this->map->map_description; ?></p>
<?	}
