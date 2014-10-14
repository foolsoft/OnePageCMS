<?php
class MCaptcha extends cmsController
{
    public function actionCreate($param)
    {
        fsCaptcha::Create();
    }
}