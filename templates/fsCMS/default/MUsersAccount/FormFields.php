<?php foreach ($fields as $fieldName => $field) { ?>
<div class="row margin-top-15">
    <div class="title-field col-lg-2">
        <?php _T($field['title']) ;?>
    </div>
    <div class="col-lg-10">
<?php        
        $datepicker = $field['expression'] == '\d{2}\.\d{2}\.\d{4}';
        if(strpos($field['expression'], '|') !== false
            && strpos($field['expression'], '[') === false
            && strpos($field['expression'], ']') === false) {
                echo fsHtml::Select('user_field['.$field['id'].']', explode('|', $field['expression']), $field['value'], array('class' => 'width-100'));
        } else { ?>
        <input data-regexp="<?php echo $field['expression']; ?>" class="width-100 <?php echo $datepicker ? 'datepicker-ru' : ''; ?>" value='<?php echo $field['value']; ?>' type='text' name='user_field[<?php echo $field['id']; ?>]' />
        <?php } ?>
    </div>
</div>
<?php } ?>
<div class="margin-top-15">
    <input class="btn btn-default" type="submit" value="<?php _T('XMLcms_save'); ?>" />
</div>