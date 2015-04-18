[parent:../AdminPanel/Index.php]

[block-content]
<?php
$textDelete = T('XMLcms_delete');
$textEdit = T('XMLcms_edit');
?>
<div>  
  <?php
  echo fsHtml::Link($myLink.'Index', T('XMLcms_back'), false, array('class' => 'fsCMS-btn admin-btn-back float-left'));
  echo fsHtml::Link($myLink.'AddCategory', T('XMLcms_text_add_category'), false, array('class' => 'fsCMS-btn admin-btn-add float-left'));
  ?> 
  <div class='clr'></div>
</div>
<hr />
<table class="list-table">
  <tr>
    <th>№</th>
    <th><?php _T('XMLcms_text_name'); ?></th>
    <th><?php _T('XMLcms_text_action'); ?></th>
  </tr>
  <?php 
  foreach ($tag->categories as $category) {
    if ($category['id'] == ALL_TYPES) {
      continue;
    }
    $name = FunctionsPosts::GetFullCategoryName($tag->categories, $category);  
  ?>
  <tr>
    <td><?php echo $category['id']; ?></td>
    <td>
      <?php echo fsHtml::Link(URL_ROOT.'posts/'.$category['alt'], $name, $name, array('target' => '_blank')); ?>
    </td>
    <td>
      <div class='admin-action-td'>
        <a href='<?php echo $myLink; ?>EditCategory/key/<?php echo $category['id']; ?>/'
           title='<?php echo $textEdit; ?>'
           class='admin-btn-small admin-btn-edit'></a>   
        <a href='<?php echo $myLink; ?>Delete/referer/Categories/table/posts_category/key/<?php echo $category['id']; ?>/'
           title='<?php echo $textDelete; ?>'
           class='admin-btn-small admin-btn-delete'></a>   
        <div class='clr'></div>
      </div>
    </td>
  </tr>
  <?php } ?>
</table>  
[endblock-content]