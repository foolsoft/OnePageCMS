[parent:../MPages/Index.php]

[block-content]
  <?php if(count($tag->results) > 0) {
    foreach ($tag->results as $result) { 
      include 'Result.php';
    }
    if ($tag->pages != '') {
      echo '<hr />'.$tag->pages;
    }
  } else { ?>
  <b><?php _T('XMLcms_search_result_null'); ?></b>
  <?php } ?>
[endblock-content]