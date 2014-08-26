[parent:../MPages/Index.php]

[block-content]
<div class='post' id='post-<?php echo $tag->post->id; ?>'>
  <div class='post-short-title'>
    <?php echo $tag->post->title; ?>
  </div>
  <div class='post-short'>
    <?php echo $tag->post->html_full; ?>
  </div>
  <div class='post-short-footer'>
    <?php echo $tag->post->date; ?>
  </div>
</div>  
[endblock-content]