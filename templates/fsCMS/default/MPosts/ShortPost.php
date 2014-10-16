<div class='post' id='post-<?php echo $post['id']; ?>'>
  <div class='post-short-title'>
    <?php $title = T($post['title']); ?>
    <a href='<?php echo fsHtml::Url(URL_ROOT.'post/'.$post['alt']); ?>' title='<?php echo $title; ?>'><?php echo $title; ?></a>
  </div>
  <div class='post-short'>
    <?php echo $post['html_short']; ?>
  </div>
  <div class='post-short-footer'>
    <?php echo $post['date']; ?>
  </div>
</div>