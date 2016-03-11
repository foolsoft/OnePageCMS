<?php
class FunctionsPosts
{
    public static function GetFullCategoryName($categories, $category)
    {
        $title = $category['title'];
        while($category['id_parent'] != 0 && isset($categories[$category['id_parent']])) {
            $category = $categories[$category['id_parent']];
            $title = $category['title'].'\\'.$title;
        }
        return $title;
    }
    
    public static function GetPostUrl($postId)
    {
        $posts = new posts();
        $posts = $posts->Get(fsSession::GetInstance('LanguageId'), $postId);
        return fsHtml::Url(URL_ROOT.'post/'.$posts['alt']);
    }
}