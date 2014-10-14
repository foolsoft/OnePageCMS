[parent:../AdminPanel/Index.php]

[block-content]
<?php 
echo fsHtml::Link($myLink.'Groups', T('XMLcms_back'), false, array('class' => 'fsCMS-btn admin-btn-back'));
$type = $tag->type;
?>
<hr />
<form action="<?php echo $myLink; ?>DoEditGroup/key/<?php echo $type['id']; ?>/" method='post'>
  <p class='title'>
    <?php _T('XMLcms_text_name'); ?>:<br />
    <input class='input-100' maxlength='100' type='text' name='name' value="<?php echo $type['name']; ?>" />
  </p>
  <p class='title'>
    <?php _T('XMLcms_allow_actions'); ?>:<br />
    <textarea class='input-100' name='allow'><?php echo $type['allow']; ?></textarea>
  </p>
  <p class='title'>
    <?php _T('XMLcms_disallow_actions'); ?>:<br />
    <textarea class='input-100' name='disallow'><?php echo $type['disallow']; ?></textarea>
  </p>
  <hr /> 
  <input class='fsCMS-btn admin-btn-save' type='submit' value='<?php _T('XMLcms_save'); ?>' />   
</form>
[endblock-content]