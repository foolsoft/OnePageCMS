[parent:../AdminPanel/Index.php]

[block-content]
<?php echo fsHtml::Link($myLink.'Index', T('XMLcms_back'), false, array('class' => 'fsCMS-btn admin-btn-back')); ?>
<hr />
<form action='<?php echo fsHtml::Url($myLink.'DoConfig'); ?>' method='post'>
  <p class='title'>
   <?php _T('XMLcms_text_posts_on_page'); ?>:
   <input onkeyup='fsCMS.IsNumeric(this, 1, true, true);'
          class='input-small'
          type='text'
          name='page_count'
          value='<?php echo $tag->settings->page_count; ?>'
   />
  </p>
  <hr />
  <input class='fsCMS-btn admin-btn-save' type='submit' value='<?php _T('XMLcms_save'); ?>' />     
</form>      
[endblock-content]