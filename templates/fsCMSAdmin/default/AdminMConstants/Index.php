[parent:../AdminPanel/Index.php]

[block-content]
<?php
$textDelete = T('XMLcms_delete');
$textEdit = T('XMLcms_edit'); 
echo fsHtml::Link($myLink.'Add', T('XMLcms_text_add_conts'), false, array('class' => 'fsCMS-btn admin-btn-add'));
?>
<hr />
<table class="list-table">
  <tr>
    <th><?php _T('XMLcms_text_name'); ?></th>
    <th><?php _T('XMLcms_text_value'); ?></th>
    <th><?php _T('XMLcms_text_action'); ?></th>
  </tr>
  <?php foreach ($tag->consts as $name => $value) {
    $value = substr(str_replace("<", "&lt;", $value), 0, 50);
  ?>
  <tr>
    <td><?php echo $name; ?></td>
    <td><?php echo $value; ?></td>
    <td>
      <div class='admin-action-td'>
        <a href='<?php echo $myLink; ?>Edit/key/<?php echo $name; ?>/'
           title='<?php echo $textEdit; ?>'
           class='admin-btn-small admin-btn-edit'></a>   
        <a href='<?php echo $myLink; ?>Delete/key/<?php echo $name; ?>/'
           title='<?php echo $textDelete; ?>'
           class='admin-btn-small admin-btn-delete'></a>   
        <div class='clr'></div>
      </div>
    </td>
  </tr>
  <?php } ?>
</table>  
[endblock-content]