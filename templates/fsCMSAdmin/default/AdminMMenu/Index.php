[parent:../AdminPanel/Index.php]

[block-content]
<?php
$textDelete = T('XMLcms_delete');
$textEdit = T('XMLcms_edit');
echo fsHtml::Link($myLink.'Config', T('XMLcms_settings'), false, array('class' => 'fsCMS-btn admin-btn-config float-left'));
echo fsHtml::Link($myLink.'Add', T('XMLcms_text_create_menu'), false, array('class' => 'fsCMS-btn admin-btn-add float-left'));
echo fsHtml::Link($myLink.'EditItems', T('XMLcms_text_menu_editor'), false, array('class' => 'fsCMS-btn float-left'));
?>
<hr />
<?php foreach ($tag->menus as $menu) { ?>
<h3 style="margin-bottom:10px;">
  <div class='float-left'>
    <?php echo $menu['title']; ?>
  </div>
  <a href='<?php echo $myLink; ?>Edit/key/<?php echo $menu['name']; ?>/'
     title='<?php echo $textEdit; ?>'
     class='admin-btn-small admin-btn-edit'
  >
  </a>
  <a href='<?php echo $myLink; ?>Delete/key/<?php echo $menu['name']; ?>/'
     title='<?php echo $textDelete; ?>'
     class='admin-btn-small admin-btn-delete'
  >
  </a>
  <div class='clr'>
</h3>     
<?php
  echo fsMenuGenerator::Get('',
                          'menu_items',
                          'parent',
                          'sample-menu',
                          'id',
                          'href',
                          'title',
                          array('position'),
                          '`menu_name` = "'.$menu['name'].'"'
      );
?>
<div class='clr'></div>
<div class='small vspace'>
  <?php _T('XMLcms_text_template_code'); ?>: {% MMenu/Menu | name=<?php echo $menu['name']; ?> %}
</div>
<?php } ?>
[endblock-content]