<?php
	// NO DIRECT ACCESS
	defined( '_JEXEC' ) or die( 'Restricted access' );
	// LOAD THE MOOTOOLS FRAMEWORK
	JHtml::_('behavior.framework');
	// SET DOCUMENT HEAD FOR PAGE
	$document = JFactory::getDocument();
	$document->addScript("//www.google.com/jsapi");
	$document->addScript("/media/mapbuilder/javascript/maps.js", "text/javascript", true);
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
