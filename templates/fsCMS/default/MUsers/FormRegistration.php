<div class="login center">
    <?php echo $tag->message; ?>
    <div class="margin-top-15"><?php _T('XMLcms_text_login'); ?></div>
    <div><input required type='text' name='login' /></div>
    <div><?php _T('XMLcms_text_password'); ?></div>
    <div><input required type='password' name='password' /></div>
    <div><?php _T('XMLcms_text_repassword'); ?></div>
    <div><input required type='password' name='repassword' /></div>
    <div class="margin-top-15"><input class="btn btn-primary" type='submit' value='<?php _T('XMLcms_text_registration'); ?>' /></div>
    <div class="margin-top-15">
        <a title="<?php _T('XMLcms_back'); ?>" href='<?php echo $referer; ?>'><?php _T('XMLcms_back'); ?></a>
    </div>
</div>