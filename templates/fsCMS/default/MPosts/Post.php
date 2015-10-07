[parent:../MPages/Index.php]

[block-content]
<div class='post' id='post-<?php echo $page['id']; ?>'>
  <div class='post-short-title'>
    <?php echo $page['title']; ?>
  </div>
  <div class='post-short'>
    <!--noindex-->
    <?php echo $page['html_short']; ?>
    <!--/noindex-->  
    <?php echo $page['html_full']; ?>
  </div>
  <div class='post-short-footer'>
    <?php echo $page['date']; ?>
  </div>
</div>  
[endblock-content]