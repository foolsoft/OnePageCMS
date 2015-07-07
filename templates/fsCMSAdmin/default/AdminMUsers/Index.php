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
  echo fsHtml::Link($myLink.'Add', T('XMLcms_text_add_user'), false, array('class' => 'fsCMS-btn admin-btn-add float-left')); 
  echo fsHtml::Link($myLink.'Fields', T('XMLcms_text_user_fields'), false, array('class' => 'fsCMS-btn float-left'));
  echo fsHtml::Link($myLink.'Groups', T('XMLcms_groups'), false, array('class' => 'fsCMS-btn float-left'));
  ?>
  <div class='clr'></div>
</div>
<hr />
<form method="post">
    <input type="text" name="search" value="<?php echo $tag->search; ?>" />
    <input type="submit"  value="<?php _T('XMLcms_text_search'); ?> " />
</form>
<hr />
<table class="list-table">
  <tr>
    <th>Id</th>
    <th><?php _T('XMLcms_text_login'); ?></th>
    <th><?php _T('XMLcms_text_type'); ?></th>
    <th><?php _T('XMLcms_text_action'); ?></th>
  </tr>
  <?php foreach ($tag->users as $user) { ?>
  <tr class='admin-row-active-<?php echo $user['active']; ?>'>
    <td><?php echo $user['id']; ?></td>
    <td><?php echo $user['login']; ?></td>
    <td><?php echo $user['link_type']; ?></td>
    <td>
      <div class='admin-action-td'>
      <?php if ($user['active'] == 0) { ?>
        <a href='<?php echo $myLink.'Activate/key/'.$user['id'].'/'; ?>'
           title='<?php echo $textActivate; ?>' class='admin-btn-small admin-btn-activate'></a>   
      <?php } ?>
        <a href='<?php echo $myLink; ?>Edit/key/<?php echo $user['id']; ?>/'
           title='<?php echo $textEdit; ?>' class='admin-btn-small admin-btn-edit'></a>   
      <?php
      if ($user['id'] > 0 && $user['login'] != fsConfig::GetInstance('main_admin')) {
      ?>
        <a href='<?php echo $myLink; ?>Delete/key/<?php echo $user['id']; ?>/'
           title='<?php echo $textDelete; ?>' class='admin-btn-small admin-btn-delete'></a>   
      <?php } ?>
      <div class='clr'></div>
      </div>
    </td>
  </tr>
  <?php } ?>
</table>  
[endblock-content]