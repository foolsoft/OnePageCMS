[parent:../AdminPanel/Index.php]

[block-content]
<?php echo fsHtml::Link($myLink.'Index', T('XMLcms_back'), false, array('class' => 'fsCMS-btn admin-btn-back')); ?>
<hr />
<form action='<?php echo $myLink; ?>DoEdit/key/<?php echo $tag->menu->name; ?>/' method='post'>
  <p class='title'>
    <?php _T('XMLcms_text_title'); ?>:
    <input value='<?php echo $tag->menu->title; ?>' class='input-100' type='text' name='title' />
  </p>
  <p class='title'>
    <?php _T('XMLcms_text_marker'); ?>:
    <input value='<?php echo $tag->menu->name; ?>' class='input-100' id='name' type='text' name='name' onkeyup='fsCMS.Chpu(this.value, this.id);' />
  </p>
  <p class='title'>
    <?php _T('XMLcms_text_template'); ?>:
    <select class='select-small' name='tpl'>    
    <?php
    foreach ($tag->templates as $template) {
      $selected = $template == $tag->menu->tpl ? 'selected' : '';
    ?>
      <option <?php echo $selected; ?> value='<?php echo $template; ?>'><?php echo $template; ?></option>
    <?php  
    } 
    ?>
     </select>
  </p>
  <hr />
  <input class='fsCMS-btn admin-btn-save' type='submit' value='<?php _T('XMLcms_save'); ?>' />     
</form>      
[endblock-content]