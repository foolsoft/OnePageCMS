<?php
$textDelete = T('XMLcms_delete');
$textEdit = T('XMLcms_edit');
$textActivate = T('XMLcms_activate');
?>
<table class="list-table">
  <tr>
    <th width="20">Id</th>
    <th><?php _T('XMLcms_text_title'); ?></th>
    <th><?php _T('XMLcms_text_category'); ?></th>
    <th><?php _T('XMLcms_text_date'); ?></th>
    <th><?php _T('XMLcms_text_action'); ?></th>
  </tr>
  <?php foreach ($tag->posts as $post) { ?>
  <tr class='admin-row-active-<?php echo $post['active']; ?>'>
    <td><?php echo $post['id']; ?></td>
    <td>
        <?php echo fsHtml::Link(URL_ROOT.'post/'.$post['alt'], $post['title'], T('XMLcms_text_open'), array('target' => '_blank')); ?>
    </td>
    <td>
      <?php
      $name = isset($post['category_name']) ? $post['category_name'] : '-';
      echo $name != '-' 
        ? fsHtml::Link(URL_ROOT.'posts/'.$post['category_alt'], $post['category_name'], $name, array('target' => '_blank'))
        : $name;
      ?>
    </td>
    <td><?php echo $post['date']; ?></td>
    <td>
      <div class='admin-action-td'>
      <?php
      if ($post['active'] == 0) {
      ?>
        <a href='<?php echo $myLink.'Activate/key/'.$post['id'].'/'; ?>'
           title='<?php echo $textActivate; ?>'
           class='admin-btn-small admin-btn-activate'
        >
        </a>   
      <?php
      }
      ?>
        <a href='<?php echo $myLink; ?>EditPost/key/<?php echo $post['id']; ?>/'
           title='<?php echo $textEdit; ?>'
           class='admin-btn-small admin-btn-edit'
        >
        </a>   
        <a href='<?php echo $myLink; ?>Delete/key/<?php echo $post['id']; ?>/'
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
<?php
if ($tag->pages != '') { 
  echo '<hr />'.$tag->pages; 
}
?> 