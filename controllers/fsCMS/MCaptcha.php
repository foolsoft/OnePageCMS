<?php
class Captcha extends cmsController
{
    public function actionCreate($param)
    {
        fsCaptcha::Create();
    }
}