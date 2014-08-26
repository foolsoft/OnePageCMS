<div style='float:left;padding:10px;'>
  <a href='http://onepagecms.net' style='text-decoration:none;color:#000;' target='_blank' title='OnePageCMS'>
    <b>OnePageCMS</b>
  </a>
</div>
<div class='admin-panel-up'>
  <?php echo fsHtml::Link(URL_ROOT.'MAuth/DoLogoutAdmin', T('XMLcms_logout')); ?> |
  <?php echo fsHtml::Link(URL_ROOT, T('XMLcms_opensite'), false, array('target' => '_blank', 'suffix' => false)); ?> |
  <span id='a_clear_cache'><?php echo $tag->linkClearCache; ?></span> 
</div>