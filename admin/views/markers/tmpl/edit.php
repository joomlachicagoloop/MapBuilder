<?php
defined('_JEXEC') or die('Restricted access');
if(JFactory::getApplication()->getTemplate() == "isis"){
	echo $this->loadTemplate('isis');
}else{
	echo $this->loadTemplate('legacy');
}
