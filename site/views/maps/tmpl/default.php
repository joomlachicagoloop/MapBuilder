<?php
	// NO DIRECT ACCESS
	defined( '_JEXEC' ) or die( 'Restricted access' );
	// SET DOCUMENT HEAD FOR PAGE
	$document = JFactory::getDocument();
	$document->addScript("components/com_maps/javascript/maps.js", "text/javascript", true);
	if(trim($this->map->meta_keywords)){
		$document->setMetaData('keywords', $this->map->meta_keywords);
	}
	if(trim($this->map->meta_description)){
		$document->setMetaData('description', $this->map->meta_description);
	}
	$style = "";
	if($width = $this->params->get('map_width', 0)){
		$style .= "width: {$width}px;";
	}
	if($height = $this->params->get('map_height', 0)){
		$style .= "height: {$height}px;";
	}
	$uri		= JURI::getInstance();
	$base		= $uri->root();
	if($this->params->get('show_title')){
?>

<h1><? echo $this->map->maps_name; ?></h1>

<?	} ?>

<div class="google-map_ id<? echo $this->map->maps_id; ?> zoom<? echo $this->params->get('zoom'); ?> lat<? echo $this->params->get('center_lat'); ?> lng<? echo $this->params->get('center_lng'); ?>" style="<? echo $style; ?>"></div>

<?	if($this->params->get('show_description')){ ?>
<p><? echo $this->map->maps_description; ?></p>
<?	}
