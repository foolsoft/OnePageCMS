<div>
    <?php echo $tag->message; ?>
    <br /><br />
    Email: <input type="email" name="email" />
    <br /><br />
    <img src="<?php echo fsHtml::Url(URL_ROOT.'MCaptcha/Create'); ?>" width="100" height="25" alt="Captcha" title="Captcha" />    
    <?php _T('XMLcms_captcha'); ?>: <input type="text" name="captcha" />
    <input type="submit" value="<?php _T('XMLcms_send'); ?>" />
</div>                                  