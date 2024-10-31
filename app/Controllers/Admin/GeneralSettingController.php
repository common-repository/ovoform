<?php

namespace Ovoform\Controllers\Admin;

use Ovoform\BackOffice\Request;
use Ovoform\Controllers\Controller;
use Ovoform\Models\Extension;

class GeneralSettingController extends Controller
{
    public function index()
    {
        $pageTitle = "Captcha Setting";
        $extensions = Extension::where('id',1)->first();
        $shortcode=json_decode($extensions->shortcode);
        $this->view('admin/setting/index', compact('pageTitle','shortcode'));
    }

    

    public function extensionUpdate(){
        $request=new Request;
        $extension = Extension::find(1);
        $validation_rule = [];
        foreach (json_decode($extension->shortcode) as $key => $val) {
            $validation_rule = array_merge($validation_rule, [$key => 'required']);
        }
        $request->validate($validation_rule);

        $shortcode = json_decode($extension->shortcode, true);
        foreach ($shortcode as $key => $value) {
            $shortcode[$key]['value'] = $request->$key;
        }

        $extension->shortcode = json_encode($shortcode);
        $extension->save();
        $notify[] = ['success', $extension->name . ' updated successfully'];
        ovoform_back($notify);
    }
}
