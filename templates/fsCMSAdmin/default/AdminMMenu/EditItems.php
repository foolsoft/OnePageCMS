[parent:../AdminPanel/Index.php]

[block-content]
<?php
$selectedTitle = '';
$selectedLink = '';
$selectedOrder = '0';
$selectedParent = '0';
?>
<?php echo fsHtml::Link($myLink.'Index', T('XMLcms_back'), false, array('class' => 'fsCMS-btn admin-btn-back')); ?>
<hr />
<form action='<?php echo $myLink; ?>DoItems/table/menu_items/' method='post'>
  <p class='title'>
    <?php _T('XMLcms_text_menu'); ?>:<br />
    <?php if($tag->key > 0) {
        echo '<input id="menu_name" type="hidden" value="'.$tag->menu.'" name="menu_name" />';
    } ?>
    <select name='menu_name'
            class='input-100'
            <?php echo $tag->key == 0 ? 'onchange="LoadItemsInMenu(this.value);"' : 'disabled'; ?>
    >
    <?php foreach ($tag->menus as $menu) { 
      $selected = '';
      if ($tag->last != '') {
        $selected = $menu['name'] == $tag->last ? 'selected' : '';
      } else {
        $selected = $menu['name'] == $tag->menu ? 'selected' : '';
      }
    ?>
      <option <?php echo $selected; ?> value='<?php echo $menu['name']; ?>'><?php echo $menu['title']; ?></option>
    <?php } ?>
    </select>
  </p>
  <p class='title'>
    <?php _T('XMLcms_text_select_menu_for_redact'); ?>:<br />
    <select id='redact_name'
            name='redact_name'
            class='input-100'
            onchange="window.location='<?php echo $myLink; ?>EditItems/key/'+this.value + '/';"
    >
      <option value='0'><?php _T('XMLcms_no'); ?></option>
    <?php 
    foreach ($tag->menu_items as $item) { 
            $selected = '';
            if($item['id'] == $tag->key) {
              $selected = 'selected';
              $selectedTitle = $item['title'];
              $selectedOrder = $item['order'];
              $selectedParent = $item['parent'];
              $selectedLink = $item['href'];
            } 
    ?>
      <option <?php echo $selected; ?> value='<?php echo $item['id']; ?>'><?php echo $item['title']; ?></option>
    <?php } ?>
    </select>
  </p>
  <p class='title'>
    <?php _T('XMLcms_text_title'); ?>:<br />
    <input name='title' id='title' type='text' value='<?php echo $selectedTitle; ?>' class='input-100' />
  </p>
  <p class='title'>
    <?php _T('XMLcms_text_full_link'); ?>:<br />
    <input type='text' id='href' name='href' value='<?php echo $selectedLink; ?>' class='input-100' /><br />
    <span>
      <?php _T('XMLcms_text_link_from_list'); ?>: 
      <select onchange="GetReadyLink(this);" name='ready_href' id='ready_href' class='input-small'>
        <option value=''><?php _T('XMLcms_text_select'); ?></option>
        <optgroup label="<?php _T('XMLcms_pages'); ?>">
          <?php foreach ($tag->linksPages as $link) { ?>
            <option value='<?php echo URL_ROOT.'page/'.$link['alt']; ?>'><?php echo $link['title']; ?></option>  
          <?php } ?>    
        </optgroup>
        <optgroup label="<?php _T('XMLcms_text_posts'); ?>">
          <?php foreach ($tag->linksPosts as $link) { ?>
            <option value='<?php echo URL_ROOT.'posts/'.$link['alt']; ?>'><?php echo $link['name']; ?></option>  
          <?php } ?>    
        </optgroup>
        <optgroup label="<?php _T('XMLcms_text_modules'); ?>">
          <?php foreach ($tag->linksControllers as $link) { ?>
            <option value='<?php echo URL_ROOT.$link['href']; ?>'><?php echo $link['title']; ?></option>  
          <?php } ?>    
        </optgroup>
      </select>
    </span>
  </p>
  <p class='title'>
    <?php _T('XMLcms_text_parent_item'); ?>:<br />
    <select name='parent' id='parent' class='input-100'>
      <option value='0'><?php _T('XMLcms_no'); ?></option>
    <?php foreach ($tag->menu_items as $item) { 
            $selected = $item['id'] == $selectedParent ? 'selected' : '';
    ?>
      <option <?php echo $selected; ?> value='<?php echo $item['id']; ?>'><?php echo $item['title']; ?></option>
    <?php } ?>
    </select>
  </p>
  <p class='title'>
    <?php _T('XMLcms_text_order'); ?>:
    <input name='order' type='text' value='<?php echo $selectedOrder; ?>' class='input-small' onkeyup='fsCMS.IsNumeric(this, 0, true, true);' />
  </p>
  <hr />
  <div>
    <input class='fsCMS-btn admin-btn-save float-left' type='submit' value='<?php echo $tag->key == 0 ? T('XMLcms_add') : T('XMLcms_save'); ?>' />     
    <?php if ($tag->key > 0) { ?>
    <input name='key' type='hidden' value='<?php echo $tag->key; ?>' />
    <input class='fsCMS-btn admin-btn-delete-big float-left'
           type='button'
           value='<?php _T('XMLcms_delete'); ?>'
           onclick="DeleteMenuItem('<?php echo $tag->key; ?>');"
    />     
    <?php } ?>
    <div class='clr'></div>
  </div>
</form>      
[endblock-content]