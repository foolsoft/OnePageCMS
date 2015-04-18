<?php
class MLanguage extends cmsController
{
    private $_cache_file = ''; 
    protected $_tableName = 'languages';

    public function Init($request)
    {
        $this->_cache_file = PATH_CACHE.FunctionsLanguages::$CACHE_PREFIX.'_'.fsSession::GetInstance('Language').'.html';
        parent::Init($request);
    }

    public function Get()
    {
        if(file_exists($this->_cache_file)) {
            return file_get_contents($this->_cache_file);
        }
        $this->Tag('languages', $this->_table->Get());
        $html = $this->CreateView();
        fsFileWorker::UpdateFile($this->_cache_file, $html);
        return $html;
    }
}