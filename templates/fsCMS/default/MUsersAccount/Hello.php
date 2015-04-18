[parent:../MPages/Index.php]

[block-content]
<div class="account padding-0-20">
    <div>
        <?php echo fsHtml::Link(URL_ROOT.'MAuth/DoLogout', T('XMLcms_logout')); ?> 
    </div>
    <div>
        <?php echo $tag->message; ?>
        {% MUsersAccount/FormChangePassword %}
        {% MUsersAccount/FormFields %}
    </div>
</div>
[endblock-content]