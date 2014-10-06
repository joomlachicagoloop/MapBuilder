<?php
/**
 * MapBuilder Installer Script
 *
 * @package		MapBuilder
 * @subpackage	Components
 * @license     GNU/GPLv3
 */

// NO DIRECT ACCESS
defined( '_JEXEC' ) or die( 'Restricted access' );

class com_mapbuilderInstallerScript
{
	protected $install_status	= null;
	protected $release			= null;

	/*
	 * PREFLIGHT
	 */
	public function preflight( $type, $parent ) {

		$jversion = new JVersion();

		// INSTALLING COMPONENT MANIFEST FILE VERSION
		$this->release = $parent->get( "manifest" )->version;

		// MANIFEST FILE MINIMUM JOOMLA VERSION
		$this->minimum_joomla_release = $parent->get( "manifest" )->attributes()->version;   

		// ABORT IF THE CURRENT JOOMLA RELEASE IS OLDER
		if( version_compare( $jversion->getShortVersion(), $this->minimum_joomla_release, 'lt' ) ) {
			JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_MAPBUILDER_MSG_ERROR_JVERSION', $this->minimum_joomla_release), 'error');
			return false;
		}
 
		// ABORT IF THE COMPONENT BEING INSTALLED IS NOT NEWER THAN THE CURRENTLY INSTALLED VERSION
		if ( $type == 'update' ) {
			$oldRelease = $this->getManifestParam('version');
			$rel = $oldRelease . ' to ' . $this->release;
			if ( version_compare( $this->release, $oldRelease, 'le' ) ) {
				JFactory::getApplication()->enqueueMessage(JText::_('COM_MAPBUILDER_MSG_ERROR_SCHEMA'), 'error');
				return false;
			}
		}
		else { $rel = $this->release; }
		
		// ABORT IF THE MEDIA DIRECTORY IS NOT WRITEABLE
		if(!is_writable(JPATH_ROOT."/media")){
			JFactory::getApplication()->enqueueMessage(JText::_('COM_MAPBUILDER_INSTALL_ERROR_PERMISSIONS'), 'error');
			return false;
		}

		return '<p>' . JText::_('COM_MAPBUILDER_MSG_SUCCESS_PREFLIGHT') . '</p>';
	}

	/*
	 * INSTALL
	 */
	public function install($parent){
		$this->install_status = "installed";
		return '<p>' . JText::sprintf('COM_MAPBUILDER_INSTALL_SUCCESS', $this->release) . '</p>';
	}
 
	/*
	 * UPDATE
	 */
	public function update( $parent ) {
		if(version_compare($this->release, "1.0.0", 'le')){
		    if( file_exists(JPATH_ROOT."components/com_mapbuilder/javascript") ){
		        $this->deleteDir(JPATH_ROOT."components/com_mapbuilder/javascript");
		    }
		}
		$this->install_status = "updated";
		return '<p>' . JText::sprintf('COM_MAPBUILDER_MSG_SUCCESS_UPDATE', $this->release) . '</p>';
	}
 
	/*
	 * POSTFLIGHT
	 */
	public function postflight( $type, $parent ) {
		switch($this->install_status){
		case "installed":
			$params = $this->getComponentParams();
			$menu = JMenu::getInstance('site');
			$item = $menu->getDefault();
			$params->set('params.default_itemid', $item->id);
            $params->set('params.js_framework', 'mootools');
            $this->setComponentParams($params);
			$this->setComponentParams($params);
			break;
		case "updated":
			$params = $this->getComponentParams();
			if(!$params->get('params.js_framework')){
			    $params->set('params.js_framework', 'mootools');
			    $this->setComponentParams($params);
			}
			break;
		}
		echo '<p>' . JText::_('COM_MAPBUILDER_MSG_SUCCESS_POSTFLIGHT') . '</p>';
	}

	/*
	 * UNINSTALL
	 */
	public function uninstall($parent){
		if(!isset($this->release)) $this->release = '1.0';
		echo '<p>' . JText::sprintf('COM_MAPBUILDER_MSG_SUCCESS_UNINSTALL', $this->release) . '</p>';
	}
 
	/*
	 * DELETE RESOURCES
	 */
	protected function deleteDir($some_dir){
		if(is_dir($some_dir)){
			$handle = opendir($some_dir);
			while(false !== ($file = readdir($handle))){
				if($file != "." && $file != ".."){
					if(is_dir($some_dir . "/" . $file)){
						$this->deleteDir($some_dir . "/" . $file);
					}else{
						unlink($some_dir . "/" . $file);
					}
				}
			}
			closedir($handle);
			rmdir($some_dir);
		}
	}
 
	/*
	 * GET A VARIABLE FROM THE MANIFEST FILE (ACTUALLY, FROM THE MANIFEST CACHE).
	 */
	protected function getManifestParam( $name ) {
		$db = JFactory::getDbo();
		$db->setQuery('SELECT manifest_cache FROM #__extensions WHERE name = "com_mapbuilder"');
		$manifest = json_decode( $db->loadResult(), true );
		return $manifest[ $name ];
	}

	/*
	 * SETS PARAMETER VALUES IN THE COMPONENT'S ROW OF THE EXTENSION TABLE
	 */
	protected function setManifestParams($param_array) {
		if ( count($param_array) > 0 ) {
			// read the existing component value(s)
			$db = JFactory::getDbo();
			$db->setQuery('SELECT params FROM #__extensions WHERE name = "com_mapbuilder"');
			$params = json_decode( $db->loadResult(), true );
			// add the new variable(s) to the existing one(s)
			foreach ( $param_array as $name => $value ) {
				$params[ (string) $name ] = (string) $value;
			}
			// store the combined new and existing values back as a JSON string
			$paramsString = json_encode( $params );
			$db->setQuery('UPDATE #__extensions SET params = ' .
				$db->quote( $paramsString ) .
				' WHERE name = "com_mapbuilder"' );
				$db->query();
		}
	}

	/*
	 * GET A REGISTRY OBJECT OF COMPONENT PARAMS FROM EXTENSIONS TABLE
	 */
	protected function getComponentParams() {
		$dbo	= JFactory::getDbo();
		$sql	= $dbo->getQuery(true);
		// CREATE THE SQL QUERY
		$sql->select("params");
		$sql->from("#__extensions");
		$sql->where("element = 'com_mapbuilder'");
		$dbo->setQuery($sql);
		// LOAD RESULT AS JREGISTRY OBJECT
		$params = new JRegistry();
		$params->loadString($dbo->loadResult());
		// RETURN OBJECT
		return $params;
	}

	/*
	 * SET THE JSON PARAM STRING FOR COMPONENT IN EXTENSIONS TABLE
	 */
	protected function setComponentParams($registry) {
		$dbo	= JFactory::getDbo();
		$sql	= $dbo->getQuery(true);
		// CONVERT JREGISTRY OBJECT TO JSON STRING
		$json = $registry->toString('JSON');
		// CREATE THE SQL QUERY
		$sql->clear();
		$sql->update("#__extensions");
		$sql->set('params = \''.$json.'\'');
		$sql->where("element = 'com_mapbuilder'");
		$dbo->setQuery($sql);
		$dbo->query();
	}
}
