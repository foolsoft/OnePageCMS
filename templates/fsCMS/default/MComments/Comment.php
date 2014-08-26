<div class="comment" id="comment-<?php echo $comment['id']; ?>">
  <div class="div-row">
    <div class="title"><?php echo $comment['author'].($comment['answer'] == '' ? '' : ' > '.$comment['answer']); ?>  / <?php echo $comment['date']; ?></div>
    <div class="comment-text"><?php echo $comment['text']; ?></div>
    <div class="comment-buttons">
      <a href="javascript:CommentAnswer(<?php echo $comment['id']; ?>);"><?php _T('XMLcms_answer'); ?></a>
      <?php
      if(fsFunctions::AddTime('+'.$tag->edit_time.' seconds', $comment['date']) > date('Y-m-d H:i:s')
        && ((AUTH && $comment['author_id'] != '0' && fsSession::GetArrInstance('AUTH', 'id') == $comment['author_id'])
          || ($comment['author_id'] == '0' && $comment['ip'] == fsFunctions::GetIp()))) { ?>
        | <a href="javascript:CommentDelete(<?php echo $comment['id']; ?>);" title="<?php _T('XMLcms_delete'); ?>"><?php _T('XMLcms_delete'); ?></a>  
      <?php } ?>
    </div>
    <div id="comment-<?php echo $comment['id']; ?>-ajax"></div>
  </div>
</div>