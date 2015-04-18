[parent:../AdminPanel/Index.php]

[block-content]
<?php echo fsHtml::Link($myLink.'Add', T('XMLcms_text_add_page'), false, array('class' => 'fsCMS-btn admin-btn-add')); ?>
<hr />
<form action="" method="get">
    <?php _T('XMLcms_text_search'); ?>: 
    <input type="text" name="title" value="<?php echo $tag->search; ?>" />
    <input type="submit" value="<?php _T('XMLcms_apply'); ?>" />
</form>
<hr />
<table class="list-table">
  <tr>
    <th>№</th>
    <th><?php _T('XMLcms_text_title'); ?></th>
    <th><?php _T('XMLcms_text_action'); ?></th>
  </tr>
  <?php foreach ($tag->pages as $page) { ?>
  <tr class='admin-row-active-<?php echo $page['active']; ?>'>
    <td><?php echo $page['id']; ?></td>
    <td>
      <?php echo fsHtml::Link(URL_ROOT.'page/'.$page['alt'], $page['title'], T('XMLcms_text_open'), array('target' => '_blank')); ?>
    </td>
    <td>
      <div class='admin-action-td'>
      <?php if ($page['active'] == 0) { ?>
        <a href='<?php echo $myLink.'Activate/key/'.$page['id'].'/'; ?>'
           title='<?php _T('XMLcms_activate'); ?>'
           class='admin-btn-small admin-btn-activate'></a>   
      <?php } ?>
        <a href='<?php echo $myLink; ?>Edit/key/<?php echo $page['id']; ?>/'
           title='<?php _T('XMLcms_edit'); ?>'
           class='admin-btn-small admin-btn-edit'></a>   
        <?php if ($page['id'] > 0) { ?>
        <a href='<?php echo $myLink; ?>Delete/key/<?php echo $page['id']; ?>/'
           title='<?php _T('XMLcms_delete'); ?>'
           class='admin-btn-small admin-btn-delete'></a>   
        <?php } ?>
        <div class='clr'></div>
      </div>
    </td>
  </tr>
  <?php } ?>
</table> 
<?php if($tag->pagesNavigation != '') {
    echo '<hr />'.$tag->pagesNavigation;
} ?>
[endblock-content]