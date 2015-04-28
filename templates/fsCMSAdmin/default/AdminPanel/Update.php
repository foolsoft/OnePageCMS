[parent:Index.php]

[block-content]
<p class="title">
    <?php _T('XMLcms_recommend_before_update'); ?>:
    <ul>
        <li><?php echo $tag->linkDownloadBackUp; ?></li>
        <li><?php echo fsFunctions::StringFormat(T('XMLcms_save_db'), array(fsConfig::GetInstance('db_prefix'), DBsettings::$base)); ?></li>
        <li><?php _T('XMLcms_recommend_less_user'); ?></li>
    </ul>
</p>                                               
<p class="title">
    <?php _T('XMLcms_important'); ?>!
    <br />
    <?php _T('XMLcms_important_update'); ?>
    <br />
    <br />
    <a href="<?php echo fsHtml::Url($myLink.'DoUpdate'); ?>" title="<?php _T('XMLcms_start_update'); ?>"><?php _T('XMLcms_start_update'); ?> - BETA</a>
</p>
[endblock-content]