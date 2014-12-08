<?php
/**
* fsKernel .htaccess file creator
* @package fsKernel
*/
class fsHtaccess
{
    /** @var string Text for append */
    private static $_append = '';
  
    /**
    * Add additional code for .htaccess file.    
    * @since 1.0.0
    * @api    
    * @param string $string Custom code.
    * @return void.  
    */
    public static function Append($string) 
    {
      self::$_append .= '
          '.$string;
    }

    /**
    * Create .htaccess file.    
    * @since 1.0.0
    * @api    
    * @param string $linkSuffix Suffix for pages. If null get value from config. Default <b>null</b>.
    * @param boolean $multilang Flag for mulilanguage links. If null get value from config. Default <b>null</b>. 
    * @return void.  
    */
    public static function Create($linkSuffix = null, $multilang = null)
    {
      if($multilang === null || ($multilang !== true && $multilang !== false)) {
        $multilang = fsConfig::GetInstance('multi_language');
      }
      if($linkSuffix === null) {
        $linkSuffix = fsConfig::GetInstance('links_suffix');
      }
      $domain = explode('/', URL_ROOT);
      $protocol = $domain[0]; 
      $domain = explode('.', $domain[2]);
      $firstIndex = $domain[0] == 'www' ? 1 : 0;
      $domainZone = $domain[count($domain) - 1];
      $subDomain = false;
      if(count($domain) == 2) {
        $domain = $domain[0];
      } else {
        $temp = ''; $subDomain = true;
        for($i = $firstIndex; $i < count($domain) - 1; ++$i) {
          $temp .= (empty($temp) ? '' : '.').$domain[$i];  
        }
        $domain = $temp;
      }
      $f = new fsFileWorker(PATH_ROOT.'.htaccess', 'w+');
      $f->Write('
  php_flag magic_quotes_gpc off 

  Options All -Indexes

  ErrorDocument 401 /index.php?controller=MPages&method=View&page=?401
  ErrorDocument 404 /index.php?controller=MPages&method=View&page=?404
  ErrorDocument 403 /index.php?controller=MPages&method=View&page=?403
  ErrorDocument 500 /index.php?controller=MPages&method=View&page=?500

  <ifmodule mod_deflate.c>
    <filesmatch .(js|css)$="">
      SetOutputFilter DEFLATE
    </filesmatch>
  </ifmodule>

  FileETag MTime Size
  <ifmodule mod_expires.c>
    <filesmatch "\.(jpeg|jpg|gif|png|css|js)$">
      ExpiresActive on
      ExpiresDefault "access plus 60 seconds"
    </filesmatch>
  </ifmodule>

  <FilesMatch "\.(tpl|php)$">
    Deny from all 
  </FilesMatch>

  <Files "index.php">
      Order Deny,Allow
      Allow from all
  </Files>

  <Files "setup.php">
      Order Deny,Allow
      Allow from all
  </Files>

  <IfModule mod_rewrite.c>
    RewriteEngine On

    #Redirect to domain with www	
    {13}RewriteCond %{HTTP_HOST} ^{0}$ [NC]
    {13}RewriteRule ^(.*)$ {2}/$1 [R=301,L]

    #Redirect to domain without www	
    #RewriteCond %{HTTP_HOST} ^www\.{0}$ [NC]
    #RewriteRule ^(.*)$ {3}$1 [R=301,L]

    {4}

    #MPages
    RewriteRule ^{11}404{1}$ /index.php?{7}controller=MPages&method=View&page=?404 [L]
    RewriteRule ^{12}?$ /index.php?{6}method=StartPage [L]
    RewriteCond %{QUERY_STRING} (.*)
    RewriteRule ^{5}page/([0-9a-zA-Z_\-]+){1}$ /index.php?{6}controller=MPages&method=View&page=${9}&%1 [L]
    RewriteRule ^template/([0-9a-zA-Z_\-]+)$ /index.php?controller=MTemplate&method=Change&name=$1 [L]

    #MPosts
    RewriteRule ^{5}post/([0-9a-zA-Z_\-]+){1}$ /index.php?{6}controller=MPosts&method=Post&post=${9} [L]
    RewriteCond %{QUERY_STRING} (.*)
    RewriteRule ^{5}posts(/([0-9a-zA-Z_\-]*){1})?$ /index.php?{6}controller=MPosts&method=Category&category=${10}&%1 [L]
    
    #MUsers
    RewriteRule ^{5}user/registration{1}$ /index.php?{6}controller=MUsers&method=Registration [L]
    RewriteRule ^{5}user/auth{1}$ /index.php?{6}controller=MAuth&method=Auth [L]

    #fsKernel
    RewriteRule ^language/([a-zA-Z\-]+)$ /index.php?method=Language&name=$1 [L]
    RewriteCond %{QUERY_STRING} (.*)
    RewriteRule ^{5}(([A-Za-z0-9_]+/[A-Za-z0-9\-_]+/){2,})$ /index.php?{6}route=${9}&%1 [L]

    #Controller
    RewriteCond %{QUERY_STRING} (.*)
    RewriteRule ^{5}([A-Za-z0-9]+)/([A-Za-z0-9]*){1}$ /index.php?{6}controller=${9}&method=${10}&%1 [L]

    #fsKernel
    RewriteCond %{QUERY_STRING} (.*)
    RewriteRule ^{5}([A-Za-z0-9]+){1}$ /index.php?{6}method=${9}&%1 [L]
  </IfModule>  
      '.self::$_append, array(
        str_replace('.', '\.', $domain).'\.'.$domainZone,
        $linkSuffix,
        $protocol.'//www.'.$domain.'.'.$domainZone,
        $protocol.'//'.$domain.'.'.$domainZone,
        $multilang === true ? 'RewriteRule ^/?$ /'.fsConfig::GetInstance('system_language').' [L,R=301]' : '',
        $multilang === true ? '([A-Za-z\-]+)/' : '',
        $multilang === true ? 'language=$1&' : '',
        $multilang === true ? 'language=$2&' : '',
        $multilang === true ? 1 : 0,
        $multilang === true ? 2 : 1,
        $multilang === true ? 3 : 2,
        $multilang === true ? '(([A-Za-z\-]+)/)?' : '',
        $multilang === true ? '([A-Za-z\-]+)/' : '/',
        $subDomain === true ? '#' : '',
      ));
      $f->Close();  
    }
}