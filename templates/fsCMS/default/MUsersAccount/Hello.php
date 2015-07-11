[parent:../MPages/Index.php]

[block-content]
<div>
    <div class="text-right">
        <?php echo fsHtml::Link(URL_ROOT.'MAuth/DoLogout', T('XMLcms_logout'), '', array('class' => 'btn btn-default')); ?> 
    </div>
    <div class="margin-top-15">
        <?php echo $tag->message; ?>
        <div>
        {% MUsersAccount/FormChangePassword %}
        </div>
        <div class="margin-top-15">
        {% MUsersAccount/FormFields %}
        </div>
    </div>
</div>
[endblock-content]