<?php
class FunctionsPages
{
    public static function GetPageUrl($pageId)
    {
        $pages = new pages();
        $pages = $pages->Load(fsSession::GetInstance('LanguageId'), $pageId);
        return fsHtml::Url(URL_ROOT.'page/'.$pages['alt']);
    }
}