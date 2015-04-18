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
        100	��������� ������� � ��������. �� ��������� �������� �� ������� �������������� ������������ ��������� � ��� �� �������, � ������� �� ������� ������, �� ������� ����������� ��������.
        200	������������ api_id
        201	�� ������� ������� �� ������� �����
        202	����������� ������ ����������
        203	��� ������ ���������
        204	��� ����������� �� ����������� � ��������������
        205	��������� ������� ������� (��������� 8 ���)
        206	����� �������� ��� ��� �������� ������� ����� �� �������� ���������
        207	�� ���� ����� (��� ���� �� �������) ������ ���������� ���������, ���� ������� ����� 100 ������� � ������ �����������
        208	�������� time ������ �����������
        209	�� �������� ���� ����� (��� ���� �� �������) � ����-����
        210	������������ GET, ��� ���������� ������������ POST
        211	����� �� ������
        212	����� ��������� ���������� �������� � ��������� UTF-8 (�� �������� � ������ ���������)
        220	������ �������� ����������, ���������� ���� �����.
        230	��������� �� ������� � ��������, ��� ��� �� ���� ����� � ���� ������ ���������� ����� 60 ���������.
        300	������������ token (�������� ����� ���� ��������, ���� ��� IP ���������)
        301	������������ ������, ���� ������������ �� ������
        302	������������ �����������, �� ������� �� ����������� (������������ �� ���� ���, ���������� � ��������������� ���)
        */
        
        $result['Status'] = 0; 
        return $result;
     } 
}