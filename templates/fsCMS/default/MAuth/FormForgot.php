<div>
    <b><?php echo $tag->message; ?></b>
    <div class="margin-top-15">Email</div>
    <div><input type="email" name="email" /></div>
    <div class="margin-top-15">
        <?php _T('XMLcms_captcha'); ?> <br />
        <img src="<?php echo fsHtml::Url(URL_ROOT.'MCaptcha/Create'); ?>" width="100" height="25" alt="Captcha" title="Captcha" />    
    </div>
    <div><input type="text" name="captcha" /></div>
    <input class="margin-top-15 btn btn-default" type="submit" value="<?php _T('XMLcms_send'); ?>" />
</div>                                  