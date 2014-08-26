[parent:../AdminPanel/Index.php]

[block-content]
<?php
$textDelete = T('XMLcms_delete');
$textEdit = T('XMLcms_edit'); 
$textActivate = T('XMLcms_activate');
?>
<div>
  <?php 
  echo fsHtml::Link($myLink.'Config', T('XMLcms_settings'), false, array('class' => 'fsCMS-btn admin-btn-config float-left'));
  echo fsHtml::Link($myLink.'Fields', T('XMLcms_text_additional_fields'), false, array('class' => 'fsCMS-btn float-left'));
  ?>
  <div class="clr"></div>
</div>
<hr />
<form action="<?php echo fsHtml::Url($myLink.'MultiAction'); ?>" method="post">
<table class="list-table">
  <tr>
    <th><?php _T('XMLcms_group'); ?></th>
    <th><?php _T('XMLcms_text_date'); ?></th>
    <th>IP</th>
    <th><?php _T('XMLcms_text_author'); ?></th>
    <th><?php _T('XMLcms_text_content'); ?></th>
    <th><?php _T('XMLcms_text_additional'); ?></th>
    <th><?php _T('XMLcms_text_action'); ?></th>
  </tr>
  <?php foreach ($tag->comments as $comment) { ?>
  <tr class='admin-row-active-<?php echo $comment['active']; ?>'>
    <td><?php echo $comment['group']; ?></td>
    <td><?php echo $comment['date']; ?></td>
    <td><?php echo $comment['ip']; ?></td>
    <td><?php echo $comment['author']; ?></td>
    <td><?php echo $comment['text']; ?></td>
    <td><?php echo $comment['additional'] == '' ? '-' : $comment['additional']; ?></td>
    <td>
      <div class='admin-action-td' <?php if ($comment['active'] == 0) { ?> style="width:100px;"<?php } ?>> 
        <?php if ($comment['active'] == 0) { ?>
        <a href='<?php echo $myLink.'Activate/key/'.$comment['id'].'/'; ?>'
           title='<?php echo $textActivate; ?>'
           class='admin-btn-small admin-btn-activate'
        ></a>   
        <?php } ?>
        <a href='<?php echo $myLink; ?>Edit/key/<?php echo $comment['id']; ?>/'
           title='<?php echo $textEdit; ?>'
           class='admin-btn-small admin-btn-edit fancybox fancybox.ajax'
        ></a>   
        <a href='<?php echo $myLink; ?>Delete/key/<?php echo $comment['id']; ?>/'
           title='<?php echo $textDelete; ?>'
           class='admin-btn-small admin-btn-delete'
        ></a> 
        <input type="checkbox" name="keys[]" value="<?php echo $comment['id']; ?>" />  
        <div class='clr'></div>
      </div>
    </td>
  </tr>
  <?php } ?>
</table>  
<hr />
<?php _T('XMLcms_with_selected'); ?>:
<select name="type">
  <option value="activate"><?php echo $textActivate; ?></option>
  <option value="delete"><?php echo $textDelete; ?></option>
</select>
<input type="submit" value="<?php _T('XMLcms_apply'); ?>" />
</form>
<?php if($tag->pages != '') {
  echo '<hr />'.$tag->pages;
} ?>
[endblock-content]