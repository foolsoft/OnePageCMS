[parent:../AdminPanel/Index.php]

[block-content]
<?php echo fsHtml::Link($myLink.'Fields', T('XMLcms_back'), false, array('class' => 'fsCMS-btn admin-btn-back')); ?>
<hr />
<form action="<?php echo $myLink; ?>DoAddField/referer/Fields/table/user_fields/" method='post'>
  <p>
    <?php _T('XMLcms_text_title'); ?>:<br />
    <input onkeyup="fsCMS.Chpu(this.value, 'name');" class='input-100' maxlength='50' type='text' name='title' />
  </p>
  <p class='title'>
    <?php _T('XMLcms_text_name'); ?>:<br />
    <input onkeyup="fsCMS.Chpu(this.value, this.id);" id='name' class='input-100' maxlength='50' type='text' name='name' />
  </p>
  <p>
    Regexp: ([0-9a-z\-\/]+, One|Two|Three, Blue||Red||Green)
    <input class='input-100' maxlength='255' type='text' name='expression' />
  </p>
  <p class='title'>
    <?php _T('XMLcms_text_type'); ?>:
    <?php echo fsHtml::Select('type', $tag->types, 'input'); ?>
    <span class="space"></span>
    <?php _T('XMLcms_text_order'); ?>:
    <input type="number" value="0" name="position" />
  </p>
  <p>
    <?php _T('XMLcms_text_duty'); ?>:
    <input type="checkbox" name="duty" />
  </p>
  <hr /> 
  <input class='fsCMS-btn admin-btn-save' type='submit' value='<?php _T('XMLcms_add'); ?>' />   
</form>
[endblock-content]