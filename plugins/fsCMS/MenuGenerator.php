<?php
class MenuGenerator 
{
  private static function _GenerateULMenu($LinkPrefix, $Menu, $Class, $Level = 1)
  {
    $html = '<ul id="menu-level-'.$Level.'" class="'.$Class.' '.$Class.'-level-'.$Level.'">';
    foreach ($Menu as $Id => $Array) {
      $html .= "<li class='".$Class."-item ".$Class."-item-level-".$Level."' >";
      $html .= fsHtml::Link($LinkPrefix.$Array['href'], T($Array['title']));
      if (count($Array['child']) > 0)
        $html .= self::_GenerateULMenu($LinkPrefix, $Array['child'], $Class, $Level + 1);
      $html .= '</li>';
    }
    return $html.'</ul>';
  }

  private static function _FindMenuLevel(&$Menu, $row, $IdToFind, $IdToInsert, $Title, $Alt, $additionalFields = array())
  {
    $finded = 0;
    if (isset($Menu[$IdToFind])) {
      $Menu[$IdToFind]['child'][$IdToInsert] = array('href' => $Alt, 'title' => $Title, 'child' => array());
      if(count($additionalFields) > 0) {
        $Menu[$IdToFind]['child'][$IdToInsert]['additional'] = array();
        foreach($additionalFields as $a) {
            $Menu[$IdToFind]['child'][$IdToInsert]['additional'][$a] = $row->$a;
        }
      }
      ++$finded;
    }
    foreach ($Menu as $Id => &$Array) {
      $finded += self::_FindMenuLevel($Array['child'], $row, $IdToFind, $IdToInsert, $Title, $Alt, $additionalFields);
    }
    return $finded;
  }

  public static function GetArray($LinkPrefix, $Table, $FieldToParent = false, $Class = 'menu', $FieldToKey = 'id', $FieldToLink = 'alt', $FieldToTitle = 'title', $Order = array('order'), $Where = "`in_menu` = '1' AND `active` = '1'", $additionalFields = array()) 
  {
    $Page = new fsDBTable($Table, false, false);
    if ($FieldToParent) {
      array_unshift($Order, $FieldToParent);
    } 
    $Page->Select()->Order($Order)->Where($Where)->Execute('', false);
    $MenuArray = array();
    $af = count($additionalFields) > 0;
    while($Page->Next()) {
      $parent = ''; $finded = 0;
      if ($FieldToParent) {
        $Parent = $Page->result->$FieldToParent;
      }
      if (empty($Parent) || $Parent === 0) {
        $MenuArray[$Page->result->$FieldToKey] = array('href' => $Page->result->$FieldToLink, 'title' => $Page->result->$FieldToTitle, 'child' => array());
      } else {
        $finded = self::_FindMenuLevel($MenuArray, $Page->result, $Parent, $Page->result->$FieldToKey, $Page->result->$FieldToTitle, $Page->result->$FieldToLink, $additionalFields);
        if ($finded == 0) {
          $MenuArray[$Page->result->$FieldToKey] = array('href' => $Page->result->$FieldToLink, 'title' => $Page->result->$FieldToTitle, 'child' => array());
        }
      }
      if($finded == 0) {
        $MenuArray[$Page->result->$FieldToKey]['additional'] = array();
        foreach($additionalFields as $a) {
            $MenuArray[$Page->result->$FieldToKey]['additional'][$a] = $Page->result->$a;
        }
      }
    }
    return $MenuArray;
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
?>