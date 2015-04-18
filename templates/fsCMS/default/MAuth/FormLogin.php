<table width='300'>
  <tr>
    <th align='right'  width='100'>
      <?php _T('XMLcms_text_login'); ?>:
    </th>
    <td align='left'>
      <input class='input-100' type='text' name='login'>
    </td>
  </tr>
  <tr>   
    <th align='right'>
      <?php _T('XMLcms_text_password'); ?>:
    </th>
    <td align='left'>
      <input class='input-100' type='password' name='password'>  
    </td>
  </tr>
  <tr>   
    <td align='right' colspan='2'>
      <input type='submit' value='<?php _T('XMLcms_text_enter'); ?>' />  
    </td>
  </tr>
  <tr>
    <td align='center' colspan='2'>
      <?php echo $tag->message; ?>
    </td>
  </tr>
  <tr>
    <td align='center' colspan='2'>
      <?php echo fsHtml::Link(URL_ROOT.'user/registration', T('XMLcms_text_registration')); ?>
    </td>
  </tr>
  <tr>
    <td align='center' colspan='2'>
      <?php echo fsHtml::Link(URL_ROOT.'user/forgot', T('XMLcms_text_fogot_password')); ?>?
    </td>
  </tr>
</table>