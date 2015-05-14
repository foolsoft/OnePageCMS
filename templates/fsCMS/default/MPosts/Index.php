[parent:../MPages/Index.php]

[block-content]
<?php
if(count($tag->childs) > 0) {
    foreach ($tag->childs as $child) { 
      echo fsFunctions::StringFormat('<a href="{1}" title="{0}">{0}</a><br />', 
              array($child['title'], fsHtml::Url(URL_ROOT.'posts/'.$child['alt'])));
    }
    echo '<hr />';
}
if(count($tag->posts) > 0) {
    foreach ($tag->posts as $post) { 
      include $post['tpl_short'];
    }
    if ($tag->pages != '') {
      echo '<hr />'.$tag->pages;
    }
} else {
    _T('XMLcms_search_result_null');
}
?>
[endblock-content]