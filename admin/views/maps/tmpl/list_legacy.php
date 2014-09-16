<?php
	defined('_JEXEC') or die('Restricted access');
	$user	= JFactory::getUser();
	$user_id= $user->get('id');
	$uri	= JURI::getInstance();
	$base	= $uri->root();
?>

<script type="text/javascript">
//<![CDATA[
	$each($$('span.icon-32-delete'), function(someElement, someIndex){
		someElement.getParent().onclick = function(e){
			var event = new Event(e);
			event.stop();
			if(document.adminForm.boxchecked.value == 0){
				alert('Please make a selection from the list to delete');
				return false;
			}
			var confirmation = confirm('Are you sure you want to delete the selected maps? This action cannot be undone!');
			if(confirmation){
				submitbutton('remove');
			}
			return false;
		}
	});
//]]>
</script>
<form action="index.php" method="post" name="adminForm">
	<input type="hidden" name="option" value="com_mapbuilder" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="chosen" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="hidemainmenu" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->filter->filter_order; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filter->filter_order_Dir; ?>" />
	<? echo JHTML::_('form.token')."\n"; ?>
	<table class="adminlist">
		<thead>
			<tr>
				<th width="5">
					<? echo JText::_('Num'); ?>
				</th>
				<th width="5">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<? echo count( $this->items ); ?>);" />
				</th>
				<th class="title">
					<? echo JHTML::_('grid.sort', JText::_('Map Name'), 'map_name', $this->filter->filter_order_Dir, $this->filter->filter_order, 'filter'); ?>
				</th>
				<th width="5%" nowrap="nowrap">
					<? echo JHTML::_('grid.sort', JText::_('Published'), 'published', $this->filter->filter_order_Dir, $this->filter->filter_order, 'filter'); ?>
				</th>
				<th width="10%" nowrap="nowrap">
					<? echo JHTML::_('grid.sort', JText::_('Order'), 'ordering', $this->filter->filter_order_Dir, $this->filter->filter_order, 'filter');?>
					<? echo JHTML::_('grid.order', $this->items); ?>
				</th>
				<th nowrap="nowrap">
					<? echo JHTML::_('grid.sort', JText::_('Access'), 'access', $this->filter->filter_order_Dir, $this->filter->filter_order, 'filter'); ?>
				</th>
				<th>
					<? echo JText::_('Map Description'); ?>
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
			$checked	= JHtml::_('grid.id', $i, $row->map_id);
			$link		= JRoute::_('index.php?option=com_mapbuilder&task=maps.edit&map_id='. $row->map_id.'&'.JSession::getFormToken().'=1');
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
				<td width="150">
					<?
					if($row->checked_out){
						echo JHtml::_('jgrid.checkedout', $i, $row->editor, $row->checked_out_time, 'maps.', $canCheckin);
						echo "<span class=\"title\">".JText::_( $row->map_name)."</span>";
					}else{
						if($canEdit || $canEditOwn){
							echo "<a href=\"{$link}\">" . htmlspecialchars($row->map_name, ENT_QUOTES) . "</a>";
						}else{
							echo "<span class=\"title\">".JText::_( $row->map_name)."</span>";
						}
					}
					?>
				</td>
				<td align="center">
					<?php echo JHtml::_('jgrid.published', $row->published, $i, 'maps.', true, 'cb'); ?>
				</td>
				<td class="order">
					<span><? echo $this->page->orderUpIcon( $i, ($i > 0), 'maps.orderup', 'Move Up'); ?></span>
					<span><? echo $this->page->orderDownIcon( $i, count($this->items), true, 'maps.orderdown', 'Move Down'); ?></span>
					<input type="text" name="order[]" size="5" value="<? echo $row->ordering; ?>" class="text_area" style="text-align: center" />
				</td>
				<td align="center">
					<? echo $row->access; ?>
				</td>
				<td>
					<? echo $row->map_description; ?>
				</td>
				<td>
					<? echo $row->map_id; ?>
				</td>
			</tr>
			<?
			$k = 1 - $k;
		}
		?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="8">
					<? echo $this->page->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
	</table>
</form>
