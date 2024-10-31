<?php

namespace Ovoform\Controllers\Admin;

use LDAP\Result;
use Ovoform\BackOffice\Facade\DB;
use Ovoform\BackOffice\Request;
use Ovoform\Controllers\Controller;
use Ovoform\Lib\FormProcessor;
use Ovoform\Models\Form;
use Ovoform\Models\FormInfo;
use Ovoform\Models\SubmitForm;

class FormController extends Controller{
    public function forms(){
        $pageTitle = 'Forms List';
        $forms     = FormInfo::orderBy('id', 'desc')->paginate(ovoform_paginate(20));
        $widget['total_forms']       = Form::count();
        $widget['total_submissions'] = SubmitForm::count();
        $widget['total_unread']      = SubmitForm::where('is_viewed',0)->count();
        
        $this->view('admin/forms/index',compact('pageTitle','forms','widget'));
    }

    public function formCreate(){
        $pageTitle = 'Create Form';
        $this->view('admin/forms/create',compact('pageTitle'));
    }
    
    public function formEdit(){
        $pageTitle = 'Edit Form';
        $formInfo  = FormInfo::find(ovoform_request()->id);
        $form      = Form::find($formInfo->form_id);
        $this->view('admin/forms/edit',compact('pageTitle','formInfo','form'));
    }

    public function formStore(){
        $request                    = ovoform_request();
        $formProcessor              = new FormProcessor();
        $generate                   = $formProcessor->generate('basic_form');
        $formInfo                   = new FormInfo;
        $formInfo->name             = sanitize_text_field($request->name);
        $formInfo->captcha_required = $request->ovoform_google_recaptcha ? 1 : 0;
        $formInfo->form_id          = intval($generate->id);
        $formInfo->created_at       = ovoform_date()->now();
        $formInfo->save();

        $notify[] = ['success', 'Form created successfully'];
        ovoform_back($notify);
    }

    public function formUpdate(){
        $request                    = ovoform_request();
        $formInfo                   = FormInfo::findOrFail($request->id);
        $formProcessor              = new FormProcessor();
        $generate                   = $formProcessor->generate('basic_form',true,'id',$formInfo->form_id);
        $formInfo->name             = sanitize_text_field($request->name) ;
        $formInfo->captcha_required = $request->ovoform_google_recaptcha ? 1 : 0;
        $formInfo->form_id          = intval($generate->id);
        $formInfo->updated_at       = ovoform_date()->now();
        $formInfo->save();

        $notify[] = ['success', 'Form updated successfully'];
        ovoform_back($notify);
    }

    public function formSubmissions(){
        $pageTitle   = "Submissions";
        
        $submissions = SubmitForm::orderBy('id','desc')->paginate(20);

        $this->view('admin/forms/submissions',compact('pageTitle','submissions'));
    }
    public function unreadSubmissions(){
        $pageTitle   = "Unread Submissions";
        $submissions = SubmitForm::orderBy('id','desc')->where('is_viewed',0)->paginate(20);
        $this->view('admin/forms/submissions',compact('pageTitle','submissions'));
    }

    public function submissionDetails(){
        $request            = new Request;
        $pageTitle          = "Submitted Message Details";
        $message            = SubmitForm::where('id',$request->id)->first();
        $message->is_viewed = 1;
        $message->save();
        $formData = maybe_unserialize($message->form_data);
        $this->view('admin/forms/submission_details',compact('pageTitle','formData'));

    }

    public function submissionDelete(){
        $request = new Request;
        SubmitForm::where('id',$request->id)->delete();
        $notify[]=['success','Submission Message deleted successfully'];
        ovoform_back($notify);
    }
    public function formDelete(){
        $request = new Request;
        FormInfo::where('form_id',$request->id)->delete();
        Form::where('id',$request->id)->delete();
        SubmitForm::where('form_info_id',$request->id)->delete();
        $notify[]=['success','Form deleted successfully'];
        ovoform_back($notify);
    }

    public function attachmentDownload()
    {
        $request   = new Request();
        $file      = ovoform_decrypt($request->file);
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        $title     = ovoform_title_to_key(get_bloginfo('name')) . '_attachments.' . $extension;
        $mimetype  = mime_content_type($file);
        header('Content-Disposition: attachment; filename="' . $title);
        header("Content-Type: " . $mimetype);
        ob_clean();
        flush();
        return readfile($file);
    }

}