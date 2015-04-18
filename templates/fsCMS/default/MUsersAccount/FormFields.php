<?php
foreach ($fields as $fieldName => $field) {
  echo T($field['title']).':<br />';
  $datepicker = $field['regexp'] == '\d{2}\.\d{2}\.\d{4}';
  if(strpos($field['regexp'], '|') !== false
      && strpos($field['regexp'], '[') === false
      && strpos($field['regexp'], ']') === false) {
          echo fsHtml::Select('user_field['.$field['id'].']', explode('|', $field['regexp']), $field['value'], array('class' => 'input-100'));
      } else {
?>
<p>
  <input data-regexp="<?php echo $field['regexp']; ?>" class='input-100 <?php echo $datepicker ? 'datepicker-ru' : ''; ?>' value='<?php echo $field['value']; ?>' type='text' name='user_field[<?php echo $field['id']; ?>]' />
</p>
<?php
      }
}
?>
<input type="submit" value="<?php _T('XMLcms_save'); ?>" />