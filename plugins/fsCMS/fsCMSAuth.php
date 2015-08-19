<?php
/**
 * fsCMS base class with auth.
 */
class cmsNeedAuthController extends cmsController
{
    /**
    * Action before main conroller action.
    * @param fsStruct $request User request.
    * @return void 
    */
    public function Init($request)
    {
        if (!AUTH) {
            return $this->Redirect(fsHtml::Url(URL_ROOT.'user/auth'), 401);
        }
        parent::Init($request);
    }
}