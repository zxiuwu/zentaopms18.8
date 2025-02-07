<?php
/**
 * The caselib of assetlib module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2021 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Liyuchun <liyuchun@easycorp.ltd>
 * @package     assetlib
 * @version     $Id: caselib.html.php 4903 2021-06-24 08:54:59Z $
 * @link        https://www.zentao.net
 */
?>
<?php include $app->getModuleRoot() . "common/view/header.html.php"?>
<?php if($canSort) include $app->getModuleRoot() . "common/view/sortable.html.php"?>
<div id="mainMenu" class="clearfix">
  <div class="btn-toobar pull-left">
    <?php common::sortFeatureMenu();?>
    <?php foreach($lang->assetlib->featureBar['caselib'] as $featureType => $label):?>
    <?php $active = $type == $featureType ? 'btn-active-text' : '';?>
    <?php $label = "<span class='text'>$label</span>";?>
    <?php echo html::a(inlink('caselib', "type=$featureType"), $label, '', "class='btn btn-link $active'");?>
    <?php endforeach;?>
  </div>
</div>
<div id="mainContent" class="main-table">
  <?php if(empty($libs)):?>
  <div class="table-empty-tip">
    <p><span class="text-muted"><?php echo $lang->noData;?></span></p>
  </div>
  <?php else:?>
  <table class="table has-sort-head" id='libList'>
    <?php $vars = "type=$type&orderBy=%s&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"; ?>
    <thead>
      <tr>
        <th class='text-left c-id'><?php common::printOrderLink('id', $orderBy, $vars, $lang->assetlib->id);?></th>
        <th class='c-order' data-flex='false' title='<?php echo $this->lang->assetlib->sort?>'><?php echo $this->lang->assetlib->sort?></th>
        <th><?php common::printOrderLink('name', $orderBy, $vars, $lang->assetlib->name);?></th>
        <th class='c-desc'><?php echo $lang->assetlib->desc;?></th>
        <th class='c-addedBy'><?php common::printOrderLink('addedBy', $orderBy, $vars, $lang->assetlib->createdBy);?></th>
      </tr>
    </thead>
    <tbody class='sortable'>
      <?php $this->loadModel('file');?>
      <?php foreach($libs as $lib):?>
      <?php $lib = $this->file->replaceImgURL($lib, 'desc');?>
      <tr data-id='<?php echo $lib->id;?>'>
        <td><?php echo sprintf('%03d',$lib->id);?></td>
        <td class='sort-handler c-sort' title='' style=''><i class='icon-move'></td>
        <td class='c-name' title="<?php echo $lib->name;?>"><?php echo html::a($this->createLink('caselib', 'browse', "libID=$lib->id"), $lib->name);?></td>
        <td  class='text-left content'>
          <?php $desc = trim(strip_tags(str_replace(array('</p>', '<br />', '<br>', '<br/>'), "\n", str_replace(array("\n    ", "\r"), '', $lib->desc)), '<img>'));?>
          <div title='<?php echo $desc;?>'><?php echo nl2br($desc);?></div>
        </td>
        <td class='nobr c-openedBy' title='<?php echo zget($users, $lib->addedBy);?>'><?php echo zget($users, $lib->addedBy);?></td>
      </tr>
      <?php endforeach;?>
    </tbody>
  </table>
  <div class='table-footer'>
    <?php $pager->show('right', 'pagerjs');?>
  </div>
  <?php endif;?>
</div>
<?php include $app->getModuleRoot() . "common/view/footer.html.php"?>
