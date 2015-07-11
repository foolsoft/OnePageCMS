<div class="login center">
    <?php echo $tag->message; ?>
    <div class="margin-top-15"><?php _T('XMLcms_text_login'); ?></div>
    <div><input type='text' name='login'></div>
    <div><?php _T('XMLcms_text_password'); ?></div>
    <div><input type='password' name='password'></div>
    <div class="margin-top-15"><input class="btn btn-primary" type='submit' value='<?php _T('XMLcms_text_enter'); ?>' /></div>
    <div class="margin-top-15"><?php echo fsHtml::Link(URL_ROOT.'user/registration', T('XMLcms_text_registration')); ?></div>
    <div class="margin-top-15"><?php echo fsHtml::Link(URL_ROOT.'user/forgot', T('XMLcms_text_fogot_password')); ?>?</div>
</div>