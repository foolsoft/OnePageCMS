<?php
class MSitemap extends cmsController
{
    private $_fileTemplate = 'sitemap{0}.xml';
    private $_fileTemplatePath = '';
    private $_maxInOne = 50000;

    private function _clearOldFiles($fileTemplate)
    {
        for($i = 1; file_exists(fsFunctions::StringFormat($this->_fileTemplatePath, array($i))); ++$i) {
            unlink(fsFunctions::StringFormat($this->_fileTemplatePath, array($i)));
        }
    }

    private function _start($path)
    {
        set_time_limit(0);
        header ('Content-type: text/xml');
        fsFunctions::CreateDirectory($path);
        $this->_fileTemplatePath = $path.$this->_fileTemplate;
        $this->_clearOldFiles($this->_fileTemplatePath);
    }

    private function _finish()
    {
        $file = fsFunctions::StringFormat($this->_fileTemplatePath, array(''));
        return file_exists($file)
            ? $this->Html(file_get_contents($file))
            : $this->HttpNotFound();
    }

    public function actionGenerate($param)
    {
        $param->path = $param->path == '' ? PATH_ROOT : $param->path;
        $param->url = $param->url == '' ? URL_ROOT_CLEAR : $param->url;
        $param->maxInOne = $param->Exists('maxInOne', true) && $param->maxInOne > 0 ? $param->maxInOne : $this->_maxInOne;
        $param->languageId = $param->Exists('languageId', true) && $param->languageId > 0 ? $param->languageId : fsSession::GetInstance('LanguageId');
        $param->priority = $param->Exists('priority', true) && $param->priority >= 0 && $param->priority <= 1 ? $param->priority : 0.5;
        
        $urls = array();
        $this->_start($param->path);

        $controllers = fsFunctions::DirectoryInfo(PATH_ROOT.'controllers/', true, false, array(), array('php'), false);
        foreach($controllers['NAMES'] as $file) {
            $temp = explode('/', $file);
            $tempCount = count($temp);
            $class = str_replace('.php', '', $tempCount == 1 ? $temp[0] : $temp[$tempCount - 1]);
            if(class_exists($class, false) && method_exists($class, 'Sitemap')) {
                $urls = array_merge($urls, $class::Sitemap($param)); 
            }
        }
        
        $linksCount = count($urls);
        $needSplit =  $linksCount > $param->maxInOne;
        if($needSplit) {
            $sitemap = new SimpleXMLElement('<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></sitemapindex>');
            $parts = $linksCount / $param->maxInOne + ($linksCount % $param->maxInOne == 0 ? 0 : 1);
            for($i = 1; $i <= $parts; ++$i) {
                $tag = $sitemap->addChild("sitemap");
                $tag->addChild('loc', fsFunction::StringFormat($param->url.$this->_fileTemplate, array($i)));
            }
            file_put_contents(fsFunction::StringFormat($param->path.$this->_fileTemplate, array('')), $sitemap->asXML());
        }

        $xmlHead = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>';
        $sitemap = new SimpleXMLElement($xmlHead);
        $count = 0; $idx = 1; 
        foreach($urls as $url) {
            $link = htmlspecialchars($url['loc']);
            if(empty($link)) {
                continue;
            }
            $tag = $sitemap->addChild('url');
            $tag->addChild('loc', $link);
            $tag->addChild('priority', empty($url['priority']) ? $param->priority : $url['priority']);
            if(!empty($url['changefreq']) || $param->changefreq != '') {
                $tag->addChild('changefreq', empty($url['changefreq']) ? $param->changefreq : $url['changefreq']);
            }
            if((!empty($url['lastmod']) && is_numeric($url['lastmod']) && $url['lastmod'] > 0) || $param->lastmod != '') {
                $tag->addChild('lastmod', $param->lastmod != '' ? $param->lastmod : date('c', $url['lastmod']));
            }
            if(++$count == $param->maxInOne || $count == $linksCount) {
                $linksCount -= $param->maxInOne;
                file_put_contents(fsFunctions::StringFormat($param->path.$this->_fileTemplate, array($needSplit ? $idx++ : '')), $sitemap->asXML());
                if($needSplit) {
                    $count = 0;
                    $sitemap = new SimpleXMLElement($xmlHead);
                }
            }
        }

        unset($sitemap);
        return $this->_finish();
    }

    public function actionParse($param)
    {
        $param->path = $param->path == '' ? PATH_ROOT : $param->path;
        $param->url = $param->url == '' ? URL_ROOT_CLEAR : $param->url;
        $this->_start($param->path);
        $sitemap = new sitemap();
        $sitemap->set_ignore(array('javascript:', '.css', '.xml', '.js', '.ico', '.jpg', '.png', '.jpeg', '.swf', '.gif'));
        $sitemap->get_links(URL_ROOT);
        $sitemap->generate_sitemap($param->path, $param->url);
        return $this->_finish();
    }
}