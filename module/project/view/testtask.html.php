<?php
/**
 * The browse view file of testtask module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     testtask
 * @version     $Id: browse.html.php 1914 2011-06-24 10:11:25Z yidong@cnezsoft.com $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php js::set('confirmDelete', $lang->testtask->confirmDelete)?>
<?php if($project->hasProduct):?>
<style>.tfoot {margin-left: 205px}</style>
<?php endif;?>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php if(!empty($tasks) and $project->hasProduct):?>
    <div class="pull-left table-group-btns">
      <button type="button" class="btn btn-link group-collapse-all"><?php echo $lang->testtask->collapseAll;?> <i class="icon-fold-all muted"></i></button>
      <button type="button" class="btn btn-link group-expand-all"><?php echo $lang->testtask->expandAll;?> <i class="icon-unfold-all muted"></i></button>
    </div>
    <?php endif;?>
    <?php $total = 0;?>
    <?php foreach($tasks as $productTasks) $total += count($productTasks);?>
    <a href='' class='btn btn-link btn-active-text'>
      <span class='text'><?php echo $lang->testtask->browse;?></span>
      <span class="label label-light label-badge"><?php echo $total;?></span>
    </a>
  </div>
  <div class="btn-toolbar pull-right">
    <?php if(common::canModify('project', $project)):?>
    <?php common::printLink('testtask', 'create', "product=0&executionID=0&build=0&projectID=$projectID", "<i class='icon icon-plus'></i> " . $lang->testtask->create, '', "class='btn btn-primary'");?>
    <?php endif;?>
  </div>
</div>
<div id="mainContent">
  <?php if(empty($tasks)):?>
  <div class="table-empty-tip">
    <p>
      <span class="text-muted"><?php echo $lang->testtask->noTesttask;?></span>
      <?php if(common::canModify('project', $project) and common::hasPriv('testtask', 'create')):?>
      <?php echo html::a($this->createLink('testtask', 'create', "product=0&executionID=0&build=0&projectID=$projectID"), "<i class='icon icon-plus'></i> " . $lang->testtask->create, '', "class='btn btn-info' data-app={$this->app->tab}");?>
      <?php endif;?>
    </p>
  </div>
  <?php else:?>
  <form class="main-table table-testtask" data-ride="table" data-group="true" method="post" target='hiddenwin' id='testtaskForm'>
    <table class="table table-grouped has-sort-head" id='taskList'>
      <thead>
        <?php $vars = "projectID=$projectID&orderBy=%s&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}&pageID={$pager->pageID}";?>
        <?php $canTestReport = ($canBeChanged and common::hasPriv('testreport', 'browse'));?>
        <tr class='<?php if($total and $project->hasProduct) echo 'divider'; ?>'>
          <th class='c-side text-center <?php if(!$project->hasProduct) echo 'hide';?>'><?php common::printOrderLink('product', $orderBy, $vars, $lang->testtask->product);?></th>
          <th class="c-id">
            <?php common::printOrderLink('id', $orderBy, $vars, $lang->idAB);?>
          </th>
          <th><?php common::printOrderLink('name', $orderBy, $vars, $lang->testtask->name);?></th>
          <th><?php common::printOrderLink('build', $orderBy, $vars, $lang->testtask->build);?></th>
          <th class='c-user'><?php common::printOrderLink('owner', $orderBy, $vars, $lang->testtask->owner);?></th>
          <th class='c-date'><?php common::printOrderLink('begin', $orderBy, $vars, $lang->testtask->begin);?></th>
          <th class='c-date'><?php common::printOrderLink('end', $orderBy, $vars, $lang->testtask->end);?></th>
          <th class='c-status'><?php common::printOrderLink('status', $orderBy, $vars, $lang->statusAB);?></th>
          <th class='c-actions-5 text-center'><?php echo $lang->actions;?></th>
        </tr>
      </thead>
      <tbody>
        <?php
        $waitCount    = 0;
        $testingCount = 0;
        $blockedCount = 0;
        $doneCount    = 0;
        ?>
        <?php foreach($tasks as $product => $productTasks):?>
        <?php $productName = zget($products, $product, '');?>
        <?php foreach($productTasks as $task):?>
        <?php if($task->status == 'wait')    $waitCount ++;?>
        <?php if($task->status == 'doing')   $testingCount ++;?>
        <?php if($task->status == 'blocked') $blockedCount ++;?>
        <?php if($task->status == 'done')    $doneCount ++;?>
        <tr data-id='<?php echo $product;?>' <?php if($task == reset($productTasks)) echo "class='divider-top'";?>>
          <?php if($task == reset($productTasks)):?>
          <td rowspan='<?php echo count($productTasks);?>' class='c-side text-left group-toggle <?php if(!$project->hasProduct) echo 'hide';?>'>
            <a class='text-primary' title='<?php echo $productName;?>'><i class='icon icon-caret-down'></i> <?php echo $productName;?></a>
            <div class='small'><span class='text-muted'><?php echo $lang->testtask->allTasks;?></span> <?php echo count($productTasks);?></div>
          </td>
          <?php endif;?>
          <td class="c-id">
            <?php printf('%03d', $task->id);?>
          </td>
          <td class='text-left' title="<?php echo $task->name?>"><?php echo html::a($this->createLink('testtask', 'cases', "taskID=$task->id"), $task->name, '', "data-app='project'");?></td>
          <td title="<?php echo $task->buildName?>">
            <?php
            if($task->build == 'trunk' || empty($task->buildName))
            {
                echo $lang->trunk;
            }
            else
            {
                $linkModule = $project->multiple ? 'build' : 'projectbuild';
                echo html::a($this->createLink($linkModule, 'view', "buildID=$task->build"), $task->buildName);
            }
            ?>
          </td>
          <td><?php echo zget($users, $task->owner);?></td>
          <td><?php echo $task->begin?></td>
          <td><?php echo $task->end?></td>
          <?php $status = $this->processStatus('testtask', $task);?>
          <td title='<?php echo $status;?>'>
            <span class='status-testtask status-<?php echo $task->status?>'><?php echo $status;?></span>
          </td>
          <td class='c-actions'>
            <?php
            if($canBeChanged)
            {
                common::printIcon('testtask', 'cases',    "taskID=$task->id", $task, 'list', 'sitemap');
                common::printIcon('testtask', 'linkCase', "taskID=$task->id", $task, 'list', 'link');
                common::printIcon('project', 'testreport', "projectID=$task->project&objectType=project&extra=$task->id", '', 'list', 'summary', '', '', false, "data-app='project'", $this->lang->testreport->common);
                common::printIcon('testtask', 'edit',   "taskID=$task->id", $task, 'list');
                common::printIcon('testtask', 'delete', "taskID=$task->id", $task, 'list', 'trash', 'hiddenwin');
            }
            ?>
          </td>
        </tr>
        <?php endforeach;?>
        <tr data-id='<?php echo $product;?>' class='group-toggle group-summary divider hidden'>
          <td class='c-side text-left'>
            <a title='<?php echo $productName;?>'><i class='icon-caret-right text-muted'></i> <?php echo $productName;?></a>
          </td>
          <td colspan='8' class='text-left'>
            <div class='small with-padding'>
              <span class='text-muted'><?php echo $lang->testtask->allTasks;?></span> <?php echo count($productTasks);?>
            </div>
          </td>
        </tr>
        <?php endforeach;?>
      </tbody>
    </table>
    <div class="table-footer">
      <div class="table-statistic"><?php echo sprintf($lang->testtask->allSummary, $total, $waitCount, $testingCount, $blockedCount, $doneCount);?></div>
      <?php $pager->show('right', 'pagerjs');?>
    </div>
  </form>
  <?php endif;?>
</div>
<?php include '../../common/view/footer.html.php';?>
