[parent:Index.php]

[block-content]
<?php echo fsHtml::Link($myLink.'Config', T('XMLcms_settings'), false, array('class' => 'fsCMS-btn admin-btn-config')); ?>
<hr />
<h3>OnePageCMS</h3>
<p><b><?php echo T('XMLcms_version').':</b> '.$tag->cmsVersion; ?></p>
<p><b><?php echo T('XMLcms_developer').':</b> '.$tag->developerHomePage; ?></p>
<h3><?php echo T('XMLcms_info'); ?></h3>
<p><b><?php echo T('XMLcms_cache_table').':</b> '.$tag->cmsCacheTableStatus; ?></p>
<p><b><?php echo T('XMLcms_load_from_cache').':</b> '.$tag->cmsLoadTableCacheStatus; ?></p>
<p>
  <b><?php _T('XMLcms_site_state'); ?>:</b>
  <span id='a_site_lock'>
    <?php echo $tag->linkLock; ?>
  </span>
  <span class='space'>|</span>
  <?php echo $tag->linkTemplateManager; ?>
</p>
[endblock-content]