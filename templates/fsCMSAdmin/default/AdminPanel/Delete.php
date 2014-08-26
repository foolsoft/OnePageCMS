[parent:Index.php]

[block-content]
<div class='title delete-sure'><?php _T('XMLcms_text_sure'); ?></div> 
<div class='delete-buttons'>
  <?php echo fsHtml::Link($tag->urlYes, T('XMLcms_yes'), false, array('suffix' => false, 'class' => 'fsCMS-btn admin-btn-yes')); ?>
  <?php echo fsHtml::Link($tag->urlNo, T('XMLcms_no'), false, array('suffix' => false, 'class' => 'fsCMS-btn admin-btn-no')); ?>
  <div class='clr'></div>
</div> 
[endblock-content]