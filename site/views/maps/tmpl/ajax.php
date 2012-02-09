<?php
	// NO DIRECT ACCESS
	defined( '_JEXEC' ) or die( 'Restricted access' );
	global $mainframe;
	header("Content-type: text/xml");
	echo "<root>".trim($this->markers)."</root>";
	$mainframe->close();