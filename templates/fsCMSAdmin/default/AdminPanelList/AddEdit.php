[parent:../AdminPanel/AddEdit.php]

[block-content]
<?php
$isAddAction = $item == null;
$actionLink = $myLink.($isAddAction ? 'DoAdd/referer/Index/' : 'DoEdit/key/'.$item[$tag->key].'/');
echo fsHtml::Link($myLink.'Index', T('XMLcms_back'), false, array('class' => 'fsCMS-btn admin-btn-back'));
?>
<hr />
<form enctype='multipart/form-data' action="<?php echo $actionLink; ?>" method='post'>
  <?php foreach($tag->columns as $name => $c) {
  $type = empty($c['type']) ? 'text' : $c['type'];
  ?>
  <p class='title' style="display:<?php echo $type != 'hidden' ? 'block' : 'none'; ?>">
    <?php _T($c['title']) ?>:<br />
    <?php if(!$isAddAction && $type == 'file' && !empty($item[$name])) { ?>
    <div>
    <a title="<?php _T('XMLcms_text_open'); ?>" href="<?php echo $item[$name]; ?>" target="_blank"><?php echo $item[$name]; ?></a>
    </div>
    <?php } ?>

    <?php switch($type) {
        case 'radio':
        ?>
            <?php if(isset($listValues[$name])) foreach($listValues[$name] as $key => $text) { ?>
            <input <?php echo $key == $item[$name] ? 'checked' : ''; ?> name="<?php echo $name ?>" type="radio" value="<?php echo $key ?>" /> <?php echo $text; ?>
            <?php } ?>
            <?php break;

       case 'select':
        ?>
            <select <?php echo isset($c['required']) ? 'required' : ''; ?> name="<?php echo $name ?>" id="<?php echo $name ?>" class="input-100">
            <?php if(isset($listValues[$name])) foreach($listValues[$name] as $key => $text) { ?>
            <option <?php echo $key == $item[$name] ? 'selected' : ''; ?> value="<?php echo $key; ?>"><?php echo $text; ?></option>
            <?php } ?>
            </select>
            <?php break;

        case 'hidden':
        case 'text':
        case 'password':
        case 'checkbox':
        case 'file':
        ?>
          <input <?php echo isset($c['required']) ? 'required' : ''; ?> id='<?php echo $name ?>' name='<?php echo $name ?>' class='input-100'
          <?php echo !$isAddAction && $type == 'checkbox' && $item[$name] == 1 ? 'checked' : ''; ?>
          value="<?php echo $isAddAction ? '' : $item[$name]; ?>"
          type='<?php echo $type; ?>' />
          <?php break;
    } ?>
  </p>
  <?php } ?>
  <hr />
  <input class='fsCMS-btn admin-btn-save' type='submit' value='<?php _T($isAddAction ? 'XMLcms_add' : 'XMLcms_save'); ?>' />
</form>
[endblock-content]