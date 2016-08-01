<?php if(count($fields) > 0) { foreach ($fields as $fieldName => $field) { ?>
<div class="row margin-top-15">
    <div class="title-field col-lg-3">
        <?php _T($field['title']) ;?>
    </div>
    <div class="col-lg-9">
    <?php echo fsFields::Create($fields, $fieldName, isset($info[$field['name']]) ? $info[$field['name']]['value'] : '', array('class' => 'width-100', 'data-regexp' => $field['expression']), 'user_field');
    ?>
    </div>
</div>
<?php } ?>
<div class="margin-top-15">
    <input class="btn btn-default" type="submit" value="<?php _T('XMLcms_save'); ?>" />
</div>
<?php } ?>