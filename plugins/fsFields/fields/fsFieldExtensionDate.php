<?php
class fsFieldExtensionDate extends fsFieldExtension  
{
    protected static $_extensionFor = 'date';
    
    protected static function _Month($month) 
    {
        $months = array(T('XMLcms_month_1'), T('XMLcms_month_2'), T('XMLcms_month_3'), 
            T('XMLcms_month_4'), T('XMLcms_month_5'), T('XMLcms_month_6'),
            T('XMLcms_month_7'), T('XMLcms_month_8'), T('XMLcms_month_9'), 
            T('XMLcms_month_10'), T('XMLcms_month_11'), T('XMLcms_month_12'));
        return $months[$month - 1];
    }

    protected static function _DayRus($day) 
    {
        $days = array(
            'первое', 'второе', 'третье', 'четвертое', 'пятое', 'шестое', 'седьмое', 'восьмое', 'девятое', 
            'десятое', 'одиннадцатое', 'двенадцатое', 'тринадцатое', 'четырнадцатое', 'пятнадцатое', 
            'шестнадцатое', 'семнадцатое', 'восемнадцатое', 'девятнадцатое',
            'двадцатое', 'тридцатое', 'двадцать', 'тридцать'
        );
        if($day <= 20) {
            return $days[$day - 1];
        }
        $ost = $day % 10;
        if($ost == 0) {
            return $days[20]; 
        }
        return ($day < 30 ? $days[21] : $days[22]).' '.$days[$ost - 1]; 
    }

    public static function Run(&$fieldsArray, $name)
    {
        if(!isset($fieldsArray[$name])) {
            return;
        }
        $value = $fieldsArray[$name];
        $time = strtotime($value);
        $fieldsArray[$name.'_d'] = date('d', $time);
        $fieldsArray[$name.'_j'] = date('d', $time);
        $fieldsArray[$name.'_sj'] = self::_DayRus($fieldsArray[$name.'_j']);
        $fieldsArray[$name.'_m'] = date('m', $time);
        $fieldsArray[$name.'_n'] = date('n', $time);
        $fieldsArray[$name.'_Y'] = date('Y', $time);
        $fieldsArray[$name.'_y'] = date('y', $time);
        $fieldsArray[$name.'_sm_1'] = self::_Month($fieldsArray[$name.'_n']);
        
        if(class_exists('phpMorphy_FilesBundle')) {
          $dictBundle = new phpMorphy_FilesBundle(PHPMORPHY_DIC_DIR, 'rus');
          $morphy = null;
          try {
          	$morphy = new phpMorphy($dictBundle, array(
              	'storage' => PHPMORPHY_STORAGE_FILE,
              	'with_gramtab' => false,
              	'predict_by_suffix' => true, 
              	'predict_by_db' => true
              ));
          } catch(phpMorphy_Exception $e) {
          	die('Error occured while creating phpMorphy instance: ' . $e->getMessage());
          }
          $allForms = $morphy->getAllForms(mb_strtoupper($fieldsArray[$name.'_sm_1'], 'UTF-8'));
          $countForms = count($allForms); 
          for($i = 1; $i < $countForms; ++$i) {
              $fieldsArray[$name.'_sm_'.($i + 1)] = mb_strtolower($allForms[$i], 'UTF-8');
          } 
        }
    }
}