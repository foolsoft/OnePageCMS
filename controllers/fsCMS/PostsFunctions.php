<?php
class PostsFunctions
{
    public static function GetFullCategoryName($categories, $category)
    {
        $name = $category['name'];
        while($category['id_parent'] != 0 && isset($categories[$category['id_parent']])) {
            $category = $categories[$category['id_parent']];
            $name = $category['name'].'\\'.$name;
        }
        return $name;
    }
}