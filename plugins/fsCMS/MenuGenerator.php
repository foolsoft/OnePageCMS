<?php
/**
 * Class for menu generation
 */
class MenuGenerator 
{
    private static function _GenerateULMenu($linkPrefix, $menu, $class, $level = 1)
    {
      $html = '<ul id="menu-level-'.$level.'" class="'.$class.' '.$class.'-level-'.$level.'">';
      foreach ($menu as $array) {
        $html .= "<li class='".$class."-item ".$class."-item-level-".$level."'>";
        $html .= fsHtml::Link($linkPrefix.$array['href'], T($array['title']));
        if (count($array['child']) > 0) {
          $html .= self::_GenerateULMenu($linkPrefix, $array['child'], $class, $level + 1);
        }
        $html .= '</li>';
      }
      return $html.'</ul>';
    }

    private static function _FindMenuLevel(&$menu, $row, $idToFind, $idToInsert, $title, $alt, $additionalFields = array())
    {
      $finded = 0;
      if (isset($menu[$idToFind])) {
        $menu[$idToFind]['child'][$idToInsert] = array('href' => $alt, 'title' => $title, 'child' => array());
        if(count($additionalFields) > 0) {
          $menu[$idToFind]['child'][$idToInsert]['additional'] = array();
          foreach($additionalFields as $a) {
              $menu[$idToFind]['child'][$idToInsert]['additional'][$a] = $row->$a;
          }
        }
        ++$finded;
      }
      foreach ($menu as &$array) {
        $finded += self::_FindMenuLevel($array['child'], $row, $idToFind, $idToInsert, $title, $alt, $additionalFields);
      }
      return $finded;
    }

    public static function GetArray($linkPrefix, $table, $fieldToParent = '', $class = 'menu', $fieldToKey = 'id', $fieldToLink = 'alt', $fieldToTitle = 'title', $order = array('order'), $where = "`in_menu` = '1' AND `active` = '1'", $additionalFields = array()) 
    {
      $page = new fsDBTable($table, false, false);
      if ($fieldToParent) {
        array_unshift($order, $fieldToParent);
      } 
      $page->Select()->Order($order)->Where($where)->Execute('', false);
      $menuArray = array();
      $af = count($additionalFields) > 0;
      while($page->Next()) {
        $parent = ''; $finded = 0;
        if ($fieldToParent != '') {
          $parent = $page->result->$fieldToParent;
        }
        if ($parent === '' || $parent === 0) {
          $menuArray[$page->result->$fieldToKey] = array('href' => $page->result->$fieldToLink, 'title' => $page->result->$fieldToTitle, 'child' => array());
        } else {
          $finded = self::_FindMenuLevel($menuArray, $page->result, $parent, $page->result->$fieldToKey, $page->result->$fieldToTitle, $page->result->$fieldToLink, $additionalFields);
          if ($finded == 0) {
            $menuArray[$page->result->$fieldToKey] = array('href' => $page->result->$fieldToLink, 'title' => $page->result->$fieldToTitle, 'child' => array());
          }
        }
        if($finded == 0) {
          $menuArray[$page->result->$fieldToKey]['additional'] = array();
          foreach($additionalFields as $a) {
              $menuArray[$page->result->$fieldToKey]['additional'][$a] = $page->result->$a;
          }
        }
      }
      return $menuArray;
    }

    public static function Get($linkPrefix,
                               $table,
                               $fieldToParent = false,
                               $class = 'menu',
                               $fieldToKey = 'id', 
                               $fieldToLink = 'alt',
                               $fieldToTitle = 'title',
                               $order = array('order'),
                               $where = "`in_menu` = '1' AND `active` = '1'")
    {
      $menuArray = self::GetArray($linkPrefix, $table, $fieldToParent, $class, $fieldToKey, $fieldToLink, $fieldToTitle, $order, $where);
      return self::_GenerateULMenu($linkPrefix, $menuArray, $class);
    }
}