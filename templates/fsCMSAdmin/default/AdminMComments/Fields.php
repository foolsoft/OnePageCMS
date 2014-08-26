[parent:../AdminPanel/Index.php]

[block-content]
<?php
$textDelete = T('XMLcms_delete');
$textEdit = T('XMLcms_edit'); 
$yes = T('XMLcms_yes');
$no = T('XMLcms_no');
?>
<div>
  <?php
  echo fsHtml::Link($myLink.'Index', T('XMLcms_back'), false, array('class' => 'fsCMS-btn admin-btn-back float-left'));  
  echo fsHtml::Link($myLink.'FieldsAdd', T('XMLcms_add'), false, array('class' => 'fsCMS-btn admin-btn-add float-left'));
  ?>
  <div class="clr"></div>
</div>
<hr />
<table class="list-table">
  <tr>
    <th><?php _T('XMLcms_text_title'); ?></th>
    <th><?php _T('XMLcms_text_required'); ?></th>
    <th><?php _T('XMLcms_html_attr_name'); ?></th>
    <th><?php _T('XMLcms_text_action'); ?></th>
  </tr>
  <?php foreach ($tag->fields as $field) { ?>
  <tr>
    <td><?php echo $field['title']; ?></td>
    <td><?php echo $field['required'] == '1' ? $yes : $no; ?></td>
    <td>additional[<?php echo $field['name']; ?>]</td>
    <td>
      <div class='admin-action-td'>
        <a href='<?php echo $myLink; ?>FieldsEdit/key/<?php echo $field['name']; ?>/'
           title='<?php echo $textEdit; ?>'
           class='admin-btn-small admin-btn-edit'
        ></a>   
        <a href='<?php echo $myLink; ?>Delete/key/<?php echo $field['name']; ?>/table/comment_fields/referer/Fields/'
           title='<?php echo $textDelete; ?>'
           class='admin-btn-small admin-btn-delete'
        ></a>   
        <div class='clr'></div>
      </div>
    </td>
  </tr>
  <?php } ?>
</table>  
[endblock-content]