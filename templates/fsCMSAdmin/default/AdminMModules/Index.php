[parent:../AdminPanel/Index.php]

[block-content]
<?php echo fsHtml::Link($myLink.'Add', T('XMLcms_text_add_module'), false, array('class' => 'fsCMS-btn admin-btn-add')); ?>
<hr />
<table class="list-table">
  <tr>
    <th><?php _T('XMLcms_text_itname'); ?></th>
    <th><?php _T('XMLcms_text_action'); ?></th>
  </tr>
  <?php foreach ($tag->modules as $module) { 
    $name = T($module['text']);
  ?>
  <tr class='admin-row-active-1'>
    <td>
      <?php echo $module['in_panel'] == 0 ? $name : fsHtml::Link(URL_ROOT.$module['name'], $name, T('XMLcms_edit')); ?>
    </td>
    <td>
      <div class='admin-action-td'>
        <?php echo fsHtml::Link($myLink.'Delete?key='.$module['name'], '', T('XMLcms_delete'), array('class' => 'admin-btn-small admin-btn-delete')); ?>
      <div class='clr'></div>
      </div>
    </td>
  </tr>
  <?php } ?>
</table>  
[endblock-content]