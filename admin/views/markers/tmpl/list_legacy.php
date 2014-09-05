<?php
	defined('_JEXEC') or die('Restricted access');
	$user	= JFactory::getUser();
	$uri	= JURI::getInstance();
	$base	= $uri->root();
	JHtml::_('behavior.tooltip');
?>

<form action="index.php" method="post" name="adminForm">
	<input type="hidden" name="option" value="com_mapbuilder" />
	<input type="hidden" name="controller" value="markers" />
	<input type="hidden" name="view" value="markers" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="chosen" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="hidemainmenu" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->filter->filter_order; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filter->filter_order_Dir; ?>" />
	<? echo JHTML::_('form.token')."\n"; ?>
	<table>
		<tr>
			<td width="100%">
				<? echo JText::_('Filter'); ?>
				<input type="text" name="filter_search" id="filter-search_" value="<? echo $this->filter->filter_search; ?>" />
				<?php echo JHtml::_('select.genericlist', $this->maps, "filter_map", "", "map_id", "map_name", $this->filter->filter_map, "filter_map"); ?>
				<input type="button" name="submit_button" id="submit-button_" value="Go" onclick="document.forms.adminForm.task.value='filter';document.forms.adminForm.submit();"/>
				<input type="button" name="reset_button" id="reset-button_" value="Reset" onclick="document.forms.adminForm.filter_search.value='';document.forms.adminForm.task.value='filter';document.forms.adminForm.submit();"/>
			</td>
		</tr>
	</table>
	<table class="adminlist">
		<thead>
			<tr>
				<th width="5">
					<? echo JText::_('Num'); ?>
				</th>
				<th width="5">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<? echo count( $this->items ); ?>);" />
				</th>
				<th class="title" width="20%">
					<? echo JHTML::_('grid.sort', JText::_('Marker Name'), 'marker_name', $this->filter->filter_order_Dir, $this->filter->filter_order, 'filter'); ?>
				</th>
				<th width="20%">
					<? echo JHTML::_('grid.sort', JText::_('Map Name'), 'map_name', $this->filter->filter_order_Dir, $this->filter->filter_order, 'filter'); ?>
				</th>
				<th width="5%" nowrap="nowrap">
					<? echo JHTML::_('grid.sort', JText::_('Published'), 'm.published', $this->filter->filter_order_Dir, $this->filter->filter_order, 'filter'); ?>
				</th>
				<th width="10%" nowrap="nowrap">
					<? echo JHTML::_('grid.sort', JText::_('Order'), 'ordering', $this->filter->filter_order_Dir, $this->filter->filter_order, 'filter');?>
					<? echo JHTML::_('grid.order', $this->items); ?>
				</th>
				<th nowrap="nowrap">
					<? echo JHTML::_('grid.sort', JText::_('Access'), 'm.access', $this->filter->filter_order_Dir, $this->filter->filter_order, 'filter'); ?>
				</th>
				<th>
					<? echo JText::_('Description'); ?>
				</th>
				<th width="1%">
					<? echo JText::_('ID'); ?>
				</th>
			</tr>
		</thead>
		<tbody>
		<?
		$k = 0;
		for($i=0; $i < count($this->items); $i++){
			$row		= $this->items[$i];
			$checked	= JHtml::_('grid.id', $i, $row->marker_id);
			$link		= JRoute::_('index.php?option=com_mapbuilder&task=markers.edit&marker_id='. $row->marker_id.'&'.JSession::getFormToken().'=1');
			$canCreate  = $user->authorise('core.create',     'com_mapbuilder');
			$canEdit    = $user->authorise('core.edit',       'com_mapbuilder');
			$canCheckin = $user->authorise('core.manage',     'com_checkin') || $row->checked_out == $user_id || $row->checked_out == 0;
			$canEditOwn = $user->authorise('core.edit.own',   'com_mapbuilder');
			$canChange  = $user->authorise('core.edit.state', 'com_mapbuilder') && $canCheckin;
			?>
			<tr class="row<? echo $k; ?>">
				<td>
					<? echo $this->page->getRowOffset($i); ?>
				</td>
				<td align="center">
					<? echo $checked; ?>
				</td>
				<td>
					<?
					if($row->checked_out){
						echo JHtml::_('jgrid.checkedout', $i, $row->editor, $row->checked_out_time, 'markers.', $canCheckin);
						echo "<span class=\"title\">".JText::_( $row->marker_name)."</span>";
					}else{
						if($canEdit || $canEditOwn){
							echo "<a href=\"{$link}\">" . htmlspecialchars($row->marker_name, ENT_QUOTES) . "</a>";
						}else{
							echo "<span class=\"title\">".JText::_( $row->marker_name)."</span>";
						}
					}
					?>
				</td>
				<td>
					<? echo ($row->map_name) ? $row->map_name : "none"; ?>
				</td>
				<td align="center">
					<?php echo JHtml::_('jgrid.published', $row->published, $i, 'markers.', true, 'cb'); ?>
				</td>
				<td class="order">
					<span><? echo $this->page->orderUpIcon( $i, ($i > 0), 'markers.orderup', 'Move Up'); ?></span>
					<span><? echo $this->page->orderDownIcon( $i, count($this->items), true, 'markers.orderdown', 'Move Down'); ?></span>
					<input type="text" name="order[]" size="5" value="<? echo $row->ordervalue; ?>" class="text_area" style="text-align: center" />
				</td>
				<td>
					<? echo $row->access; ?>
				</td>
				<td>
					<? echo JText::_( strip_tags($row->marker_description) ); ?>
				</td>
				<td>
					<? echo $row->marker_id; ?>
				</td>
			</tr>
			<?
			$k = 1 - $k;
		}
		?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="9">
					<? echo $this->page->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
	</table>
</form>
