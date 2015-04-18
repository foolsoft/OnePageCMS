[parent:../AdminPanel/Index.php]

[block-content]
<?php echo fsHtml::Link($myLink.'Index', T('XMLcms_back'), false, array('class' => 'fsCMS-btn admin-btn-back')); ?>
<hr />
<form action="<?php echo fsHtml::Url($myLink.'DoAddSiteLanguage'); ?>" method="post">
    <table class="list-table">
        <tr>
          <th>№</th>
          <th><?php _T('XMLcms_text_name'); ?></th>
          <th><?php _T('XMLcms_text_action'); ?></th>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td><input name="name" type="text" value="" placeholder="<?php _T('XMLcms_text_new_name'); ?>" maxlength="10" /></td>
            <td><input type="submit" value="<?php _T('XMLcms_add'); ?>" /></td>
        </tr>
        <?php foreach($tag->languages as $language) { ?>
        <tr class="admin-row-active-<?php echo $language['active']; ?>">
            <td><?php echo $language['id']; ?></td>
            <td><?php echo $language['name']; ?></td>
            <td>
                <div class='admin-action-td'>
                    <?php if ($language['active'] == 0) { ?>
                    <a href='<?php echo $myLink.'Activate/key/'.$language['id'].'/'; ?>'
                       title='<?php _T('XMLcms_activate'); ?>'
                       class='admin-btn-small admin-btn-activate'></a>   
                    <?php } else { ?>
                    <a href='<?php echo $myLink.'DeActivate/key/'.$language['id'].'/'; ?>'
                       title='<?php _T('XMLcms_deactivate'); ?>'
                       class='admin-btn-small admin-btn-deactivate'></a>
                    <?php } ?>    
                    <a href="#edit-language"
                       title='<?php _T('XMLcms_edit'); ?>'
                       onclick="ChangeSiteLanguage(<?php echo $language['id']; ?>, '<?php echo $language['name']; ?>');"
                       class='fancybox admin-btn-small admin-btn-edit'></a>
                    <a href='<?php echo $myLink; ?>DeleteSiteLanguage/referer/Languages/key/<?php echo $language['id']; ?>/'
                       title='<?php _T('XMLcms_delete'); ?>'
                       class='admin-btn-small admin-btn-delete'></a>   
                    <div class='clr'></div>
                </div>
            </td>
        </tr>
        <?php } ?>
    </table>
</form>
<div class="hidden" id="edit-language">
    <form action="<?php echo fsHtml::Url($myLink.'DoEditSiteLanguage'); ?>" method="post">
        <div class="title vspace center"><?php _T('XMLcms_text_new_name'); ?></div>
        <div class="vspace center"><input type="text" name="name" value="" /></div>
        <div class="vspace center">
            <input type="hidden" value="" name="key" />
            <input type="submit" value="<?php _T('XMLcms_save'); ?>" />
        </div>
    </form>
</div>
[endblock-content]