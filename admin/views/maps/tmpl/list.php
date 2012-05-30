<?php
	defined('_JEXEC') or die('Restricted access');
	$user	=& JFactory::getUser();
	$uri	=& JURI::getInstance();
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
	<input type="hidden" name="option" value="com_maps" />
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
					<? echo JHTML::_('grid.sort', JText::_('Map Name'), 'maps_name', $this->filter->filter_order_Dir, $this->filter->filter_order, 'filter'); ?>
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
			$checked	= JHTML::_('grid.id', $i, $row->maps_id );
			$link		= JRoute::_( 'index.php?option=com_maps&task=edit&cid[]='. $row->maps_id.'&'.JUtility::getToken().'=1');
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
					if(JTable::isCheckedOut($user->get('id'), $row->checked_out)){
						echo JText::_( $row->maps_name);
					}else{
						echo "<a href=\"{$link}\">" . htmlspecialchars($row->maps_name, ENT_QUOTES) . "</a>";
					}
					?>
				</td>
				<td align="center">
					<?php echo JHtml::_('jgrid.published', $row->published, $i, '', true, 'cb'); ?>
				</td>
				<td class="order">
					<span><? echo $this->page->orderUpIcon( $i, ($i > 0), 'orderup', 'Move Up'); ?></span>
					<span><? echo $this->page->orderDownIcon( $i, count($this->items), true, 'orderdown', 'Move Down'); ?></span>
					<input type="text" name="order[]" size="5" value="<? echo $row->ordering; ?>" class="text_area" style="text-align: center" />
				</td>
				<td align="center">
					<? echo $row->access; ?>
				</td>
				<td>
					<? echo $row->maps_description; ?>
				</td>
				<td>
					<? echo $row->maps_id; ?>
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