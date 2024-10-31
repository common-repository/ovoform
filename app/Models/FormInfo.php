<?php

namespace Ovoform\Models;

use Ovoform\BackOffice\Database\Model;

class FormInfo extends Model
{
    protected static $table = 'ovoform_form_infos';

    public function submissions(){
        $this->belongsTo(SubmitForm::class,'form_info_id','id');
    }
}
