<?php foreach($posts as $post) { ?>
    <div class="post-<?php echo $post['id']; ?> post">
            <a href="<?php echo fsHtml::Url(URL_ROOT.'post/'.$post['alt']); ?>">
                <?php echo $post['title']; ?>
            </a>
            <img src="<?php echo $post['image']; ?>" alt="" />
            <div><?php echo $post['html_short']; ?></div>
    </div>
<?php } ?>