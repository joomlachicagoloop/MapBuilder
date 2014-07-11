<?php
	defined('_JEXEC') or die('Restricted access');
	JHtml::_('bootstrap.tooltip');
	JHtml::_('behavior.multiselect');
	JHtml::_('formbehavior.chosen', 'select');
	$user = JFactory::getUser();
	$user_id = $user->get('id');
	$sortFields = array();
	$sortFields['marker_name'] = JText::_('COM_MAPS_LIST_MARKER_NAME_LABEL');
	$sortFields['maps_name'] = JText::_('COM_MAPS_LIST_MAP_NAME_LABEL');
	$sortFields['marker.published'] = JText::_('COM_MAPS_LIST_PUBLISHED_LABEL');
	$sortFields['marker.ordering'] = JText::_('COM_MAPS_LIST_ORDERING_LABEL');
	$sortFields['marker.access'] = JText::_('COM_MAPS_LIST_ACCESS_LABEL');
	$sortFields['marker_id'] = JText::_('COM_MAPS_LIST_ID_LABEL');
	$saveOrder = $this->filter->filter_order == 'marker.ordering';
	if ($saveOrder)
	{
		$saveOrderingUrl = 'index.php?option=com_maps&task=markers.saveOrderAjax&tmpl=component';
		JHtml::_('sortablelist.sortable', 'data-table', 'adminForm', strtolower($this->filter->filter_order_Dir), $saveOrderingUrl);
	}
	array_shift($this->maps);
	JHtmlSidebar::addFilter(JText::_('COM_MAPS_FILTER_MAP_LABEL'), 'filter_map', JHtml::_('select.options', $this->maps, 'maps_id', 'maps_name', $this->filter->filter_map));
    $this->sidebar = JHtmlSidebar::render();
?>
<script type="text/javascript">
//<![CDATA[
	Joomla.orderTable = function()
	{
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != '<?php echo $this->filter->filter_order; ?>')
		{
			dirn = 'asc';
		}
		else
		{
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, '');
	}
//]]>
</script>
<form action="index.php" method="post" name="adminForm" id="adminForm">
<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif;?>
        <div id="filter-bar" class="btn-toolbar">
            <div class="btn-group pull-right">
                <?php echo $this->page->getLimitBox(); ?>
            </div>
            <div class="btn-group pull-right hidden-phone">
                <label for="directionTable" class="element-invisible"><?php echo JText::_('COM_MAPS_LIST_ORDERING_LABEL');?></label>
                <select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
                    <option value=""><?php echo JText::_('JFIELD_ORDERING_DESC');?></option>
                    <option value="asc" <?php if ($this->filter->filter_order_Dir == 'asc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING');?></option>
                    <option value="desc" <?php if ($this->filter->filter_order_Dir == 'desc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING');?></option>
                </select>
            </div>
            <div class="btn-group pull-right">
                <label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY');?></label>
                <select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
                    <option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option>
                    <?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $this->filter->filter_order);?>
                </select>
            </div>
            <div class="btn-group pull-left">
                <input type="text" name="filter_search" id="filter-search_" class="input-large" placeholder="<?php echo JText::_('COM_MAPS_FILTER_MARKER_SEARCH_LABEL'); ?>" value="<?php echo $this->filter->filter_search; ?>" />
            </div>
            <div class="btn-group pull-left">
                <input type="button" class="btn" name="submit_button" id="submit-button_" value="Go" onclick="document.forms.adminForm.task.value='filter';document.forms.adminForm.submit();"/>
                <input type="button" class="btn" name="reset_button" id="reset-button_" value="Reset" onclick="document.forms.adminForm.filter_search.value='';document.forms.adminForm.task.value='filter';document.forms.adminForm.submit();"/>
            </div>
        </div>
        <input type="hidden" name="option" value="com_maps" />
        <input type="hidden" name="task" value="markers.filter" />
        <input type="hidden" name="chosen" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <input type="hidden" name="hidemainmenu" value="0" />
        <input type="hidden" name="filter_order" value="<?php echo $this->filter->filter_order; ?>" />
        <input type="hidden" name="filter_order_Dir" value="<?php echo $this->filter->filter_order_Dir; ?>" />
        <? echo JHTML::_('form.token')."\n"; ?>
        <table class="table table-striped" id="data-table">
            <thead>
                <tr>
                    <th width="1%" class="nowrap center hidden-phone">
                        <?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'marker.ordering', $this->filter->filter_order_Dir, $this->filter->filter_order, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
                    </th>
                    <th width="5">
                        <input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this)" />
                    </th>
                    <th width="5%" class="nowrap">
                        <?php echo JHtml::_('grid.sort', 'COM_MAPS_LIST_PUBLISHED_LABEL', 'marker.published', $this->filter->filter_order_Dir, $this->filter->filter_order, 'markers.filter'); ?>
                    </th>
                    <th class="title nowrap">
                        <?php echo JHtml::_('grid.sort', 'COM_MAPS_LIST_MARKER_NAME_LABEL', 'marker_name', $this->filter->filter_order_Dir, $this->filter->filter_order, 'markers.filter'); ?>
                    </th>
                    <th class="nowrap">
                        <?php echo JHtml::_('grid.sort', 'COM_MAPS_LIST_MAP_NAME_LABEL', 'maps_name', $this->filter->filter_order_Dir, $this->filter->filter_order, 'markers.filter'); ?>
                    </th>
                    <th class="nowrap">
                        <?php echo JHtml::_('grid.sort', 'COM_MAPS_LIST_ACCESS_LABEL', 'marker.access', $this->filter->filter_order_Dir, $this->filter->filter_order, 'markers.filter'); ?>
                    </th>
                    <th>
                        <?php echo JText::_('COM_MAPS_LIST_DESCRIPTION_LABEL'); ?>
                    </th>
                    <th width="1%">
                        <?php echo JHtml::_('grid.sort', 'COM_MAPS_LIST_ID_LABEL', 'marker_id', $this->filter->filter_order_Dir, $this->filter->filter_order, 'markers.filter'); ?>
                    </th>
                </tr>
            </thead>
            <tbody>
            <?php
            $k = 0;
            for($i=0; $i < count($this->items); $i++){
                $row		= $this->items[$i];
                $checked	= JHtml::_('grid.id', $i, $row->maps_id);
                $link		= JRoute::_('index.php?option=com_maps&task=markers.edit&marker_id='. $row->marker_id.'&'.JSession::getFormToken().'=1');
                $canCreate  = $user->authorise('core.create',     'com_maps');
                $canEdit    = $user->authorise('core.edit',       'com_maps');
                $canCheckin = $user->authorise('core.manage',     'com_checkin') || $row->checked_out == $user_id || $row->checked_out == 0;
                $canEditOwn = $user->authorise('core.edit.own',   'com_maps');
                $canChange  = $user->authorise('core.edit.state', 'com_maps') && $canCheckin;
                ?>
                <tr class="row<?php echo $k; ?>" sortable-group-id="<?php echo $row->maps_id; ?>">
                    <td class="order nowrap center hidden-phone">
                        <?php
                        $iconClass = '';
                        if (!$canChange)
                        {
                            $iconClass = ' inactive';
                        }
                        elseif (!$this->filter->filter_order)
                        {
                            $iconClass = ' inactive tip-top hasTooltip" title="' . JHtml::tooltipText('JORDERINGDISABLED');
                        }
                        ?>
                        <span class="sortable-handler<?php echo $iconClass ?>">
                            <i class="icon-menu"></i>
                        </span>
                        <?php if ($canChange && $saveOrder) : ?>
                            <input type="text" style="display:none" name="order[]" size="5" value="<?php echo $row->ordering;?>" class="width-20 text-area-order " />
                        <?php endif; ?>
                    </td>
                    <td align="center">
                        <?php echo $checked; ?>
                    </td>
                    <td align="center">
                        <?php echo JHtml::_('jgrid.published', $row->published, $i, 'markers.', $canChange, 'cb'); ?>
                    </td>
                    <td  class="nowrap">
                        <?php
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
                    <td class="nowrap">
                        <?php echo $row->marker_name; ?>
                    </td>
                    <td align="center">
                        <?php echo $row->access; ?>
                    </td>
                    <td>
                        <?php $words = explode(" ", strip_tags($row->marker_description)); echo implode(" ", array_splice($words, 0, 55)); ?>
                    </td>
                    <td>
                        <?php echo $row->marker_id; ?>
                    </td>
                </tr>
                <?php
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
    </div>
</form>
