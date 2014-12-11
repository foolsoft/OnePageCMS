[parent:../MPages/Index.php]

[block-content]
<?php if ($tag->compleate) { ?>
<p>
<?php echo fsFunctions::StringFormat(T('XMLcms_register_success'), array(fsHtml::Url(URL_ROOT.'user/auth'))); ?>
</p>
<?php } else { ?>
{% MUsers/FormRegistration %}
<?php } ?>
[endblock-content]