<?php
foreach($tag->languages as $languageId => $languageName) { 
    echo fsHtml::Link(URL_ROOT.'language/'.$languageName, $languageName).' ';
}