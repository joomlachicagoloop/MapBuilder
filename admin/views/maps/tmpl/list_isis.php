<?php
	defined('_JEXEC') or die('Restricted access');
	JHtml::_('bootstrap.tooltip');
	JHtml::_('behavior.multiselect');
	JHtml::_('formbehavior.chosen', 'select');
	$user = JFactory::getUser();
	$user_id = $user->get('id');
	$sortFields = array();
	$sortFields['map_name'] = JText::_('COM_MAPBUILDER_LIST_MAP_NAME_LABEL');
	$sortFields['published'] = JText::_('COM_MAPBUILDER_LIST_PUBLISHED_LABEL');
	$sortFields['ordering'] = JText::_('COM_MAPBUILDER_LIST_ORDERING_LABEL');
	$sortFields['s.access'] = JText::_('COM_MAPBUILDER_LIST_ACCESS_LABEL');
	$sortFields['map_id'] = JText::_('COM_MAPBUILDER_LIST_ID_LABEL');
	$saveOrder = $this->filter->filter_order == 'ordering';
	if ($saveOrder)
	{
		$saveOrderingUrl = 'index.php?option=com_mapbuilder&task=maps.saveOrderAjax&tmpl=component';
		JHtml::_('sortablelist.sortable', 'data-table', 'adminForm', strtolower($this->filter->filter_order_Dir), $saveOrderingUrl);
	}
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
                <label for="directionTable" class="element-invisible"><?php echo JText::_('COM_MAPBUILDER_LIST_ORDERING_LABEL');?></label>
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
                <input type="text" name="filter_search" id="filter-search_" class="input-large" placeholder="<?php echo JText::_('COM_MAPBUILDER_FILTER_MAPS_SEARCH_LABEL'); ?>" value="<?php echo $this->filter->filter_search; ?>" />
            </div>
            <div class="btn-group pull-left">
                <input type="button" class="btn" name="submit_button" id="submit-button_" value="Go" onclick="document.forms.adminForm.task.value='filter';document.forms.adminForm.submit();"/>
                <input type="button" class="btn" name="reset_button" id="reset-button_" value="Reset" onclick="document.forms.adminForm.filter_search.value='';document.forms.adminForm.task.value='filter';document.forms.adminForm.submit();"/>
            </div>
        </div>
        <input type="hidden" name="option" value="com_mapbuilder" />
        <input type="hidden" name="task" value="maps.filter" />
        <input type="hidden" name="chosen" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <input type="hidden" name="filter_order" value="<?php echo $this->filter->filter_order; ?>" />
        <input type="hidden" name="filter_order_Dir" value="<?php echo $this->filter->filter_order_Dir; ?>" />
        <?php echo JHtml::_('form.token')."\n"; ?>
        <table class="table table-striped" id="data-table">
            <thead>
                <tr>
                    <th width="1%" class="nowrap center hidden-phone">
                        <?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'ordering', $this->filter->filter_order_Dir, $this->filter->filter_order, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
                    </th>
                    <th width="5">
                        <input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this)" />
                    </th>
                    <th width="5%" class="nowrap">
                        <?php echo JHtml::_('grid.sort', 'COM_MAPBUILDER_LIST_PUBLISHED_LABEL', 'published', $this->filter->filter_order_Dir, $this->filter->filter_order, 'maps.filter'); ?>
                    </th>
                    <th class="title nowrap">
                        <?php echo JHtml::_('grid.sort', 'COM_MAPBUILDER_LIST_MAP_NAME_LABEL', 'map_name', $this->filter->filter_order_Dir, $this->filter->filter_order, 'maps.filter'); ?>
                    </th>
                    <th class="nowrap">
                        <?php echo JHtml::_('grid.sort', 'COM_MAPBUILDER_LIST_ACCESS_LABEL', 's.access', $this->filter->filter_order_Dir, $this->filter->filter_order, 'maps.filter'); ?>
                    </th>
                    <th>
                        <?php echo JText::_('COM_MAPBUILDER_LIST_DESCRIPTION_LABEL'); ?>
                    </th>
                    <th width="1%">
                        <?php echo JHtml::_('grid.sort', 'COM_MAPBUILDER_LIST_ID_LABEL', 'map_id', $this->filter->filter_order_Dir, $this->filter->filter_order, 'maps.filter'); ?>
                    </th>
                </tr>
            </thead>
            <tbody>
            <?php
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
                <tr class="row<?php echo $k; ?>" sortable-group-id="">
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
                        <?php echo JHtml::_('jgrid.published', $row->published, $i, 'maps.', $canChange, 'cb'); ?>
                    </td>
                    <td  class="nowrap">
                        <?php
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
                        <?php echo $row->access; ?>
                    </td>
                    <td>
                        <?php $words = explode(" ", strip_tags($row->map_description)); echo implode(" ", array_splice($words, 0, 55)); ?>
                    </td>
                    <td>
                        <?php echo $row->map_id; ?>
                    </td>
                </tr>
                <?php
                $k = 1 - $k;
            }
            ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="7">
                        <?php echo $this->page->getListFooter(); ?>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</form>
