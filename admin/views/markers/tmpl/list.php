<?php
	defined('_JEXEC') or die('Restricted access');
	$user	=& JFactory::getUser();
	$uri	=& JURI::getInstance();
	$base	= $uri->root();
	JHtml::_('behavior.tooltip');
?>

<script type="text/javascript">
//<![CDATA[
	$each($$('span.icon-32-delete'), function(someElement, someIndex){
		someElement.getParent().onclick = function(){
			if(document.adminForm.boxchecked.value == 0){
				alert('Please make a selection from the list to delete');
				return false;
			}
			var confirmation = confirm('Are you sure you want to delete the selected markers? This action cannot be undone!');
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
				<?php echo JHtml::_('select.genericlist', $this->maps, "filter_map", "", "maps_id", "maps_name", $this->filter->filter_map, "filter_map"); ?>
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
					<? echo JHTML::_('grid.sort', JText::_('Map Name'), 'maps_name', $this->filter->filter_order_Dir, $this->filter->filter_order, 'filter'); ?>
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
			$checked	= JHTML::_('grid.id', $i, $row->marker_id );
			$link		= JRoute::_( 'index.php?option=com_maps&controller=markers&view=markers&task=edit&cid[]='. $row->marker_id.'&'.JUtility::getToken().'=1');
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
					if(JTable::isCheckedOut($user->get('id'), $row->checked_out)){
						echo JText::_( $row->marker_name);
					}else{
						echo "<a href=\"{$link}\">" . $row->marker_name . "</a>";
					}
					?>
				</td>
				<td>
					<? echo ($row->maps_name) ? $row->maps_name : "none"; ?>
				</td>
				<td align="center">
					<?php echo JHtml::_('jgrid.published', $row->published, $i, '', true, 'cb'); ?>
				</td>
				<td class="order">
					<span><? echo $this->page->orderUpIcon( $i, ($i > 0), 'orderup', 'Move Up'); ?></span>
					<span><? echo $this->page->orderDownIcon( $i, count($this->items), true, 'orderdown', 'Move Down'); ?></span>
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