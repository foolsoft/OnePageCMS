[parent:../AdminPanel/Index.php]

[block-content]
<?php echo fsHtml::Link($myLink.'Add', T('XMLcms_text_add_page'), false, array('class' => 'fsCMS-btn admin-btn-add')); ?>
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
           class='admin-btn-small admin-btn-activate'
        ></a>   
      <?php } ?>
        <a href='<?php echo $myLink; ?>Edit/key/<?php echo $page['id']; ?>/'
           title='<?php _T('XMLcms_edit'); ?>'
           class='admin-btn-small admin-btn-edit'
        ></a>   
        <a href='<?php echo $myLink; ?>Delete/key/<?php echo $page['id']; ?>/'
           title='<?php _T('XMLcms_delete'); ?>'
           class='admin-btn-small admin-btn-delete'
        ></a>   
        <div class='clr'></div>
      </div>
    </td>
  </tr>
  <?php } ?>
</table>  
[endblock-content]