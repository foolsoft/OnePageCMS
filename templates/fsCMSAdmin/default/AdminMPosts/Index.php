[parent:../AdminPanel/Index.php]

[block-content]
<div>  
  <?php
    echo fsHtml::Link($myLink.'Config', T('XMLcms_settings'), false, array('class' => 'fsCMS-btn admin-btn-config float-left'));
    echo fsHtml::Link($myLink.'AddPost', T('XMLcms_text_add_post'), false, array('class' => 'fsCMS-btn admin-btn-add float-left'));
    echo fsHtml::Link($myLink.'AddCategory', T('XMLcms_text_add_category'), false, array('class' => 'fsCMS-btn admin-btn-add float-left'));
    echo fsHtml::Link($myLink.'Categories', T('XMLcms_text_categories'), false, array('class' => 'fsCMS-btn float-left'));  
  ?>
  <div class='clr'></div>
</div>
<hr />
<?php _T('XMLcms_text_category'); ?>:
<select name="category" onchange="LoadPostsTable(this.value);">
<?php foreach ($tag->categories as $id => $category) {
  $selcted = ALL_TYPES == $id ? 'selected' : ''; 
?>
  <option <?php echo $selcted; ?> value="<?php echo $id; ?>">
    <?php echo FunctionsPosts::GetFullCategoryName($tag->categories, $category); ?>
  </option>
<?php } ?>
  <option value="-1"><?php _T('XMLcms_text_nocategory'); ?></option>
</select>
<hr />
<div id="post-table">
  <?php echo $tag->table; ?>
</div>
[endblock-content]