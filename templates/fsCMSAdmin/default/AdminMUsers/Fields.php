[parent:../AdminPanel/Index.php]

[block-content]
<?php
$textDelete = T('XMLcms_delete');
$textEdit = T('XMLcms_edit');
?>
<div>  
  <?php
  echo fsHtml::Link($myLink.'Index', T('XMLcms_back'), false, array('class' => 'fsCMS-btn admin-btn-back float-left'));
  echo fsHtml::Link($myLink.'AddField', T('XMLcms_text_add_field'), false, array('class' => 'fsCMS-btn admin-btn-add float-left'));
  ?>
  <div class='clr'></div>
</div>
<hr />
<table class="list-table">
  <tr>
    <th><?php _T('XMLcms_text_title'); ?></th>
    <th><?php _T('XMLcms_text_name'); ?></th>
    <th><?php _T('XMLcms_text_action'); ?></th>
  </tr>
  <?php foreach ($tag->fields as $field) { ?>
  <tr class='admin-row-active-1'>
    <td><?php echo $field['title']; ?></td>
    <td>user_field[<?php echo $field['name']; ?>]</td>
    <td>
      <div class='admin-action-td'>
        <a href='<?php echo $myLink; ?>EditField/table/user_fields/key/<?php echo $field['id']; ?>/'
           title='<?php echo $textEdit; ?>'
           class='admin-btn-small admin-btn-edit'
        >
        </a>   
        <a href='<?php echo $myLink; ?>Delete/referer/Fields/table/user_fields/key/<?php echo $field['id']; ?>/'
           title='<?php echo $textDelete; ?>'
           class='admin-btn-small admin-btn-delete'
        >
        </a>   
      <div class='clr'></div>
      </div>
    </td>
  </tr>
  <?php } ?>
</table>  
[endblock-content]