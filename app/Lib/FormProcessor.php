<?php

namespace Ovoform\Lib;

use Ovoform\BackOffice\Request;
use Ovoform\Models\Form;

class FormProcessor
{
    public function generate($act,$isUpdate = false, $identifierField = 'act',$identifier = null)
    {
        $request = new Request();
        $forms = $request->form_generator;
        $formData = [];
        if ($forms) {
            for ($i=0; $i < count($forms['form_label']); $i++) {
                $extensions = $forms['extensions'][$i];
                if ($extensions != 'null' && $extensions != null) {
                    $extensionsArr = explode(',',$extensions);
                    $notMatchedExt = count(array_diff($extensionsArr,$this->supportedExt()));
                    if ($notMatchedExt > 0) {
                        throw new \Exception( __("Your selected extensions are invalid", 'ovoform') );
                    }
                }
                $label = ovoform_title_to_key(sanitize_text_field($forms['form_label'][$i]));
                $formData[$label] = [
                    'name' => sanitize_text_field($forms['form_label'][$i]),
                    'label' => $label,
                    'is_required' => $forms['is_required'][$i],
                    'extensions' => $forms['extensions'][$i] == 'null' ? "" : $forms['extensions'][$i],
                    'options' => $forms['options'][$i] ? explode(",",$forms['options'][$i]) : [],
                    'type' => $forms['form_type'][$i],
                ];
            }
        }
        $db = new Form();
        $data = [
            'act'=> $act,
            'form_data'=> maybe_serialize($formData)
        ];
        if ($isUpdate) {
            if ($identifierField == 'act') {
                $identifier = $act;
            }
            $form = $db->where($identifierField,$identifier)->first();
            if ($form) {
                $db->where($identifierField,$identifier)->update($data);
            }
        }else{
            $formId = $db->insert($data);
            $form = $db->where('id',$formId)->first();
        }
        return $form;
    }

    public function valueValidation($formData)
    {
        $validationRule = [];
        $rule = [];

        foreach($formData as $data){
            if ($data->is_required == 'required') {
                $rule = array_merge($rule,['required']);
            }else{
                $rule = array_merge($rule,['nullable']);
            }
            if ($data->type == 'select' || $data->type == 'checkbox' || $data->type == 'radio'){
                $rule = array_merge($rule,['in:'. implode(',',$data->options)]);
            }
            if ($data->type == 'file') {
                $rule = array_merge($rule,['mimes:'.$data->extensions]);
            }
            if ($data->type == 'checkbox') {
                $validationRule[$data->label.'.*'] = $rule;
            }else{
                $validationRule[$data->label] = $rule;
            }
            $rule = [];
        }
        return $validationRule;
    }

    public function processFormData($request, $formData)
    {
        $requestForm = [];
        foreach($formData as $data){
            $name = $data->label;
            $value = $request->$name;
            if($data->type == 'file') {
                if($request->hasFile($name)){
                    $path = ovoform_file_path('verify');
                    $value = ovoform_file_uploader($value, $path);
                }else{
                    $value = null;
                }
            }
            $requestForm[] = [
                'name'=>$data->name,
                'type'=>$data->type,
                'value'=>$value,
            ];
        }
        return $requestForm;
    }

    public function supportedExt()
    {
        return [
            'jpg',
            'jpeg',
            'png',
            'pdf',
            'doc',
            'docx',
            'txt',
            'xlx',
            'xlsx',
            'csv'
        ];
    }
}