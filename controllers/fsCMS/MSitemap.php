<?php
class MSitemap extends cmsController
{
    public function actionGenerate($param)
    {
        $fileTemplate = PATH_ROOT.'sitemap{0}.xml';
        $file = fsFunctions::StringFormat($fileTemplate, array(''));
        
        for($i = 1; file_exists(fsFunctions::StringFormat($fileTemplate, array($i))); ++$i) {
            unlink(fsFunctions::StringFormat($fileTemplate, array($i)));
        }
        
        set_time_limit(0);
        header ('Content-type: text/xml');
        
        $sitemap = new sitemap();
        $sitemap->set_ignore(array('javascript:', '.css', '.xml', '.js', '.ico', '.jpg', '.png', '.jpeg', '.swf', '.gif'));
        $sitemap->get_links(URL_ROOT);
        $sitemap->generate_sitemap(PATH_ROOT, URL_ROOT_CLEAR);
        if(file_exists($file)) {
            return $this->Html(file_get_contents($file));
        } 
        return $this->HttpNotFound();
    }
}