<?php

namespace Ovoform\BackOffice;

use Ovoform\BackOffice\Validator\Validator;

class Request{

    public function hasFile($keyName)
    {
        if(array_key_exists($keyName,$_FILES) && array_key_exists('name',$_FILES[$keyName]) && $_FILES[$keyName]['name']){
            if(is_array($_FILES[$keyName]['name'])){
                if(array_key_exists(0,$_FILES[$keyName]['name'])){
                    return true;
                }else{
                    return false;
                }
            }
            return true;
        }
        return false;
    }

    public function file($file)
    {
        $filePath = $file['full_path'];
        return ovoform_to_object(pathinfo($filePath,PATHINFO_ALL));
    }

    public function files($key){
        $files = $_FILES[$key];
        $fileGroup = [];
        foreach($files['name'] as $index => $file){
            $fileGroup[] = [
                'name'=>$files['name'][$index],
                'full_path'=>$files['full_path'][$index],
                'type'=>$files['type'][$index],
                'tmp_name'=>$files['tmp_name'][$index],
                'error'=>$files['error'][$index],
                'size'=>$files['size'][$index],
                'direct_file'=>true
            ];
        }

        return $fileGroup;
    }

    public function validate($rules,$customMessages = [])
    {
        $validations = Validator::make($rules,$customMessages);
        if (!empty($validations['errors'])) {
            ovoform_session()->flash('errors',$validations['errors']);
            ovoform_back();
        }
    }

    public function __get($name)
    {
        $reqData = $_REQUEST[$name] ?? null;

        if ($reqData === null) {
            $fileData = $_FILES[$name] ?? null;

            if ($fileData !== null) {
                $reqData = $this->sanitizeFileData($fileData);
            }
        } else {
            // Apply htmlspecialchars only if $reqData is a string
            $reqData = is_string($reqData) ? htmlspecialchars($reqData, ENT_QUOTES, 'UTF-8') : $reqData;
        }

        return $reqData;
    }

    private function sanitizeFileData($fileData)
    {
        $sanitizedFileData = [];

        foreach ($fileData as $key => $value) {
            if (is_array($value)) {
                $sanitizedFileData[$key] = $this->sanitizeFileData($value);
            } else {
                // Apply htmlspecialchars only if $value is a string
                $sanitizedFileData[$key] = is_string($value) ? htmlspecialchars($value, ENT_QUOTES, 'UTF-8') : $value;
            }
        }

        return $sanitizedFileData;
    }

}