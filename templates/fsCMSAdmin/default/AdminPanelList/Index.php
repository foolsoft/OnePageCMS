[parent:../AdminPanel/Index.php]

[block-content]
<?php
$textDelete = T('XMLcms_delete');
$textEdit = T('XMLcms_edit');
echo fsHtml::Link($myLink.'AddEdit', T('XMLcms_add'), false, array('class' => 'fsCMS-btn admin-btn-add'));
?>
<hr />
<table class="list-table">
  <tr>
    <?php foreach($tag->columns as $c) { ?>
    <th><?php _T($c['title']); ?></th>
    <?php } ?>
    <th><?php _T('XMLcms_text_action'); ?></th>
  </tr>
  <?php foreach ($rows as $row) { ?>
  <tr>
    <?php foreach($tag->columns as $name => $c) {
    $type = empty($c['type']) ? 'text' : $c['type'];
    ?>
    <td><?php
        if($type == 'file') {
            if(empty($row[$name])) {
                echo '-';
            } else {
                $ext = pathinfo(basename($row[$name]), PATHINFO_EXTENSION);
                if(in_array($ext, array('gif', 'png', 'bmp', 'jpg', 'jpeg', 'ico'))) {
                    echo '<a href="'.$row[$name].'" title="'.T('XMLcms_text_open').'">'.
                        '<img src="'.$row[$name].'" title="'.T('XMLcms_text_open').'" alt="'.T('XMLcms_text_image').'" width="50" /></a>';
                } else {
                    echo '<a href="'.$row[$name].'" title="'.T('XMLcms_text_open').'">'.$row[$name].'</a>';
                }
            }
        } else {
            $display = $row[$name];
            if(isset($listValues[$name])) {
                foreach($listValues[$name] as $key => $value) {
                    if($key == $row[$name]) {
                        $display = $value;
                        break;
                    }
                }
            }
            echo $display;
        }
    ?></td>
    <?php } ?>
    <td>
      <div class='admin-action-td'>
        <a href='<?php echo $myLink; ?>AddEdit/key/<?php echo $row[$tag->key]; ?>/'
           title='<?php echo $textEdit; ?>' class='admin-btn-small admin-btn-edit'></a>
        <a href='<?php echo $myLink; ?>Delete/key/<?php echo $row[$tag->key]; ?>/'
           title='<?php echo $textDelete; ?>' class='admin-btn-small admin-btn-delete'></a>
        <div class='clr'></div>
      </div>
    </td>
  </tr>
  <?php } ?>
</table>
[endblock-content]