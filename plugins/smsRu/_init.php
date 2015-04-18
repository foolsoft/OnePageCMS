<?php
/* Please register in service using this URL.
 * http://foolsoft.sms.ru
 */
class SmsRu 
{
    private static $_apiId = '';      //Your API id
    private static $_fromName = '';   //Your sender name
         
    public static function Send($to, $text, $translit = false, $test = false, $apiId = '', $fromName = '') 
    {
        if($apiId == '') {
            $apiId = self::$_apiId;
        }
        if($fromName == '') {
            $fromName = self::$_fromName;                    
        }
        if(!is_array($to) || $apiId == '') {
            return array('Status' => 1, 'Message' => 'No API id');
        }
        //$to = array('79000000000', ...)
        $result = array();
        foreach($to as $number) {
            $urlSms = "http://sms.ru/sms/send?partner_id=103184&api_id=".$apiId.
                "&to=".$number.
                "&text=".urlencode($text).
                ($fromName !== '' ? '$from='.$fromName : '').
                ($test ? "&test=1" : '').
                ($translit ? "&translit=1" : '');
                
            $answer = file_get_contents($urlSms);
            $result[$number] = array();
            $temp = explode("\n", $answer);
            $result[$number]['Status'] = $temp[0];
            $result[$number]['Ids'] = array();
            $count = count($temp);
            for($i = 1; $i < $count; ++$i) {
                $result[$number]['Ids'][] = $temp[$i];
            } 
        }
        
        /* SMS statuses
        100	Сообщение принято к отправке. На следующих строчках вы найдете идентификаторы отправленных сообщений в том же порядке, в котором вы указали номера, на которых совершалась отправка.
        200	Неправильный api_id
        201	Не хватает средств на лицевом счету
        202	Неправильно указан получатель
        203	Нет текста сообщения
        204	Имя отправителя не согласовано с администрацией
        205	Сообщение слишком длинное (превышает 8 СМС)
        206	Будет превышен или уже превышен дневной лимит на отправку сообщений
        207	На этот номер (или один из номеров) нельзя отправлять сообщения, либо указано более 100 номеров в списке получателей
        208	Параметр time указан неправильно
        209	Вы добавили этот номер (или один из номеров) в стоп-лист
        210	Используется GET, где необходимо использовать POST
        211	Метод не найден
        212	Текст сообщения необходимо передать в кодировке UTF-8 (вы передали в другой кодировке)
        220	Сервис временно недоступен, попробуйте чуть позже.
        230	Сообщение не принято к отправке, так как на один номер в день нельзя отправлять более 60 сообщений.
        300	Неправильный token (возможно истек срок действия, либо ваш IP изменился)
        301	Неправильный пароль, либо пользователь не найден
        302	Пользователь авторизован, но аккаунт не подтвержден (пользователь не ввел код, присланный в регистрационной смс)
        */
        
        $result['Status'] = 0; 
        return $result;
     } 
}