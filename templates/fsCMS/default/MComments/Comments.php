<div id="comments-<?php echo $tag->group; ?>" class="comments comments-<?php echo $tag->group; ?>">
  <?php foreach($tag->comments as $comment) {
    include $tag->template;
  } 
  if($tag->pages != '') {
    echo $tag->pages;
  } ?>
</div>