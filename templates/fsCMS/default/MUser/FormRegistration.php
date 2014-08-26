<table>
  <tr>
    <th align='right'>
      <?php _T('XMLcms_text_login'); ?>:
    </th>
    <td align='left'>
      <input class='input-100' type='text' name='login' />  
    </td>
  </tr>
  <tr>
    <th align='right'>
      <?php _T('XMLcms_text_password'); ?>:
    </th>
    <td align='left'>
      <input class='input-100' type='password' name='password' />  
    </td>
  </tr>
  <tr>
    <th align='right'>
      <?php _T('XMLcms_text_repassword'); ?>:
    </th>
    <td align='left'>
      <input class='input-100' type='password' name='repassword' />  
    </td>
  </tr>
  <tr>
    <td align='center' colspan='2'>
      <?php echo $tag->message; ?>
    </td>
  </tr>
  <tr>
    <td align='left'>
      <a title="<?php _T('XMLcms_back'); ?>" href='<?php echo $referer; ?>'><?php _T('XMLcms_back'); ?></a>
    </td>
    <td align='right'>
      <input type='submit' value='<?php _T('XMLcms_text_registration'); ?>'>  
    </td>
  </tr>
</table>