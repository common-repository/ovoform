<?php

namespace Ovoform\Controllers;

use Ovoform\BackOffice\Request;
use Ovoform\Controllers\Controller;
use Ovoform\Lib\FormProcessor;
use Ovoform\Models\Form;
use Ovoform\Models\FormInfo;
use Ovoform\Models\SubmitForm;

class FormController extends Controller
{
    public function submitForm(){
        $request = new Request;
        $formInfo = FormInfo::findOrFail($request->id);
        if ($formInfo->captcha_required && !ovoform_verify_captcha()) {
            $notify[] = ['danger', 'Captcha is not verified'];
            ovoform_back($notify);
        }
        
        $form = Form::findOrFail($formInfo->form_id);
        
        
        $formData      = json_decode(json_encode(maybe_unserialize($form->form_data)));
        $formProcessor = new FormProcessor();
        $userData      = $formProcessor->processFormData($request, $formData);

        $submitForm = new SubmitForm;
        $submitForm->form_info_id = intval($formInfo->id);
        $submitForm->form_data = maybe_serialize($userData);
        $submitForm->created_at = ovoform_date()->now();
        $submitForm->save();

        $notify[] = ['success', 'Form submitted successfully'];
        ovoform_back($notify);
    }
}