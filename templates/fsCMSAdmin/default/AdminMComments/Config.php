[parent:../AdminPanel/Index.php]

[block-content]
<?php echo fsHtml::Link($myLink.'Index', T('XMLcms_back'), false, array('class' => 'fsCMS-btn admin-btn-back')); ?>
<hr />
<form action="<?php echo fsHtml::Url($myLink.'DoConfig'); ?>" method="post">
  <p>
    <?php _T('XMLcms_comments_sorting'); ?>:
    <?php echo fsHtml::Select('sorting', array('ASC' => T('XMLcms_text_asc'), 'DESC' => T('XMLcms_text_desc')), $tag->settings->sorting); ?>
  </p>
  <p>
    <?php _T('XMLcms_allow_guests'); ?>:
    <?php echo fsHtml::Select('allow_guests', array('0' => T('XMLcms_no'), '1' => T('XMLcms_yes')), $tag->settings->allow_guests); ?>
  </p>
  <p>
    <?php _T('XMLcms_comments_minlen'); ?>:
    <?php echo fsHtml::Number('min_length', $tag->settings->min_length, array('min' => 1)); ?>
  </p>
  <p>
    <?php _T('XMLcms_comments_maxlen'); ?>:
    <?php echo fsHtml::Number('max_length', $tag->settings->max_length, array('min' => 0)); ?>
  </p>
  <p>
    <?php _T('XMLcms_comments_time_for_edit'); ?>:
    <?php echo fsHtml::Number('edit_time', $tag->settings->edit_time, array('min' => 0)); ?>
  </p>
  <p>
    <?php _T('XMLcms_text_posts_on_page'); ?>, <?php _T('XMLcms_0_infinity'); ?>:
    <?php echo fsHtml::Number('comments_on_page', $tag->settings->comments_on_page, array('min' => 0)); ?>
  </p>
  <hr />
  <p>
    <?php _T('XMLcms_blocked_ip'); ?>:<br />
    <?php echo fsHtml::Textarea('block_ip', $tag->settings->block_ip, array('style' => 'width:100%;')); ?>
  </p>
  <p>
    <?php _T('XMLcms_blocked_users'); ?>:<br />
    <?php echo fsHtml::Textarea('block_users', $tag->settings->block_users, array('style' => 'width:100%;')); ?>
  </p>
  <hr /> 
  <input class='fsCMS-btn admin-btn-save' type='submit' value='<?php _T('XMLcms_save'); ?>' />   
</form>
[endblock-content]