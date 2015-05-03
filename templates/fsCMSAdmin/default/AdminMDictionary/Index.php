[parent:../AdminPanel/Index.php]

[block-content]
<div class="sticky-0 dictionary-head">
  <div class="float-left">
    <form class="confirm" method="post" action="<?php echo fsHtml::Url($myLink.'DeleteLanguage'); ?>">
      <?php _T('XMLcms_dictionary'); ?>:
      <?php echo fsHtml::Select('lang', $tag->dictionaries, $tag->selected); ?>  
      <input type="submit" value="<?php _T('XMLcms_delete'); ?>" />
    </form>
  </div>
  <div class="float-left">
    <form method="post" action="<?php echo fsHtml::Url($myLink.'CreateLanguage'); ?>">
      <?php echo fsHtml::Editor('from', '', array('size' => '3', 'placeholder' => 'en', 'maxlength' => 3)); ?> -
      <?php echo fsHtml::Editor('to', '', array('size' => '3', 'placeholder' => 'ru', 'maxlength' => 3)); ?>
      <input type="submit" value="<?php _T('XMLcms_add'); ?>" />
      <?php echo fsHtml::Button(T('XMLcms_panel_languages'), "window.location=URL_ROOT + 'AdminMDictionary/Languages' + URL_SUFFIX;"); ?>
    </form>
  </div>
  <div class="float-right">
    <?php echo fsHtml::Button(T('XMLcms_add_word'), "DictionaryAddWord();"); ?>
  </div>
  <div class="clr"></div>
</div>
<div style="margin-top:10px;">
  <form action="<?php echo fsHtml::Url($myLink.'Save'); ?>" method="post">
    <div id="dictionary-redactor"></div>
    <hr /> 
    <input class='fsCMS-btn admin-btn-save' type='submit' value='<?php _T('XMLcms_save'); ?>' />   
  </form>
</div>
<hr />
<?php _T('XMLcms_dictionary_how_use'); ?>
[endblock-content]