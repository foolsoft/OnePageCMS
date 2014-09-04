<?php
class Captcha extends cmsController
{
    public function actionCreate($Param)
    {
        fsCaptcha::Create();
    }
}
?>