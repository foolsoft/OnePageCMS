<?php
class MSearch extends cmsController 
{
    protected $_tableName = 'search';
  
    public function FormSearch($param) 
    {
        $html = $this->CreateView(array(), $this->_Template('FormSearch'));
        return '<form method="get" action="'.$this->_My('Search').'" id="search-form" class="search-form">'.
            $html.
            '</form>';
    }
  
    public function actionSearch($param) 
    {
        $data = $this->_table->CreateQuery($param->text);  
        $results = array();
        foreach ($data as $row) {
            $matches = array();
            $matchesCount = preg_match_all("/{([0-9_a-zA-Z]+)}/", $row['title'], $matches);
            if ($matchesCount == 0) {
                continue;
            }
            $matchesLink = array();
            $matchesCountLink = preg_match_all("/{([0-9_a-zA-Z]+)}/", $row['link'], $matchesLink);
            if ($matchesCountLink == 0) {
                continue;
            } 
            $this->_table->Execute($row['query'], false);
            while ($this->_table->Next()) {
                $title = $row['title'];
                $link = $row['link'];
                foreach ($matches[1] as $match) {
                    $title = str_replace('{'.$match.'}', $this->_table->result->mysqlRow[$match], $title);
                }
                foreach ($matchesLink[1] as $match) {
                    $link = str_replace('{'.$match.'}', $this->_table->result->mysqlRow[$match], $link);
                }
                $results[] = array(
                    'title' => $title,
                    'link' => fsHtml::Url(URL_ROOT.$link)
                );  
            }
        }
        $this->Tag('results', $results);
        $this->Tag('title', T('XMLcms_search_result').': '.$param->text);
        $this->Tag('meta_keywords', $param->text.','.CMSSettings::GetInstance('default_keywords'));
        $this->Tag('meta_description', $param->text.'. '.CMSSettings::GetInstance('default_description'));
    }
}