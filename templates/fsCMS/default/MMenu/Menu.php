<ul class="nav navbar-nav <?php echo $tag->class; ?> <?php echo $tag->class; ?>-level-<?php echo $tag->level; ?>" id="menu-level-<?php echo $tag->level; ?>">
    <?php foreach ($tag->items as $itemId => $item) { ?>
        <li class="<?php echo $tag->class; ?>-item <?php echo $tag->class; ?>-item-level-<?php echo $tag->level; ?>">
            <?php echo fsHtml::Link($item['href'], T($item['title']), '', array('target' => $item['additional']['target'])); ?>
            {% MMenu/Menu | name=<?php echo $tag->menu; ?>, parent=<?php echo $itemId; ?>, class=<?php echo $tag->class; ?>, level=<?php echo ($tag->level + 1); ?> %}
        </li>
    <?php } ?>
</ul>