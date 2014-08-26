[parent:../MPages/Index.php]

[block-content]
  <?php
  foreach ($tag->posts as $post) { 
    include $post['tpl_short'];
  }
  if ($tag->pages != '') {
    echo '<hr />'.$tag->pages;
  }
  ?>
[endblock-content]