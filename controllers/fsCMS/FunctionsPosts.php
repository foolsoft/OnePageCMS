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
}