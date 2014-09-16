<ul id="menu-level-<?php echo $tag->level; ?>" class="<?php echo $tag->class; ?> <?php echo $tag->class; ?>-level-<?php echo $tag->level; ?>">
  <?php
  foreach ($tag->items as $itemId => $item) {
    $title = T($item['title']);
    ?>
    <li class='<?php echo $tag->class; ?>-item <?php echo $tag->class; ?>-item-level-<?php echo $tag->level; ?>' >
      <?php echo fsHtml::Link($item['href'], $title); ?>
      {% MMenu/Menu | name=<?php echo $tag->menu; ?>, parent=<?php echo $itemId; ?>, class=<?php echo $tag->class; ?>, level=<?php echo ($tag->level + 1); ?> %}
    </li>
    <?php
  }
  ?>
</ul>  