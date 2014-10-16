[parent:../AdminPanel/Index.php]

[block-content]
<?php
$textDelete = T('XMLcms_delete');
$textEdit = T('XMLcms_edit');
?>
<div>  
  <?php
  echo fsHtml::Link($myLink.'Index', T('XMLcms_back'), false, array('class' => 'fsCMS-btn admin-btn-back float-left')); 
  echo fsHtml::Link($myLink.'AddGroup', T('XMLcms_text_add_group'), false, array('class' => 'fsCMS-btn admin-btn-add float-left')); 
  ?>
  <div class='clr'></div>
</div>
<hr />
<table class="list-table">
  <tr>
    <th><?php _T('XMLcms_text_name'); ?></th>
    <th><?php _T('XMLcms_text_action'); ?></th>
  </tr>
  <?php foreach ($tag->types as $type) { ?>
  <tr class='admin-row-active-<?php echo $user['active']; ?>'>
    <td><?php echo $type['name']; ?></td>
    <td>
      <div class='admin-action-td'>
        <a href='<?php echo $myLink; ?>EditGroup/key/<?php echo $type['id']; ?>/'
           title='<?php echo $textEdit; ?>'
           class='admin-btn-small admin-btn-edit'></a>   
        <?php if($type['id'] > 1) { ?>
        <a href='<?php echo $myLink; ?>Delete/key/<?php echo $type['id']; ?>/table/types_users/referer/Groups/'
           title='<?php echo $textDelete; ?>'
           class='admin-btn-small admin-btn-delete'></a>   
        <?php } ?>
        <div class='clr'></div>
      </div>
    </td>
  </tr>
  <?php } ?>
</table>  
[endblock-content]