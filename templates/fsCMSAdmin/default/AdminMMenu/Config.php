[parent:../AdminPanel/Index.php]

[block-content]
<?php echo fsHtml::Link($myLink.'Index', T('XMLcms_back'), false, array('class' => 'fsCMS-btn admin-btn-back')); ?>
<hr />
<form action="<?php echo fsHtml::Url($myLink.'DoConfig'); ?>" method="post">
  <p class='title'>
    <?php _T('XMLcms_text_default_template'); ?>:
    <select class='select-small' name='tpl'>    
    <?php foreach ($tag->templates as $template) {
      $selected = $template == $tag->current_template ? 'selected' : '';
    ?>
      <option value='<?php echo $template; ?>'><?php echo $template; ?></option>
    <?php } ?>
    </select>
  </p>
  <hr />
  <input class='fsCMS-btn admin-btn-save' type='submit' value='<?php _T('XMLcms_save'); ?>' />     
</form>      
[endblock-content]