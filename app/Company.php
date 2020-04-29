<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable=[

        
    'user',
    'social_name',
    'alias_name',
    'document_company',
    'document_company_secondary',
    'zipcode',
    'street',
    'number',
    'complement',
    'neighborhood',
    'state',
    'city',
    ];

    public function user(){
        return $this->belongsTo(User::class,'user', 'id');
    }

    public function setDocumentCompanyAttribute($value)
    {
        //recebe a string  cpf e chama a funcao pra  retirar os caracaters 
        $this->attributes['document_company'] = $this->clearField($value);
    }

    public function getDocumentCompanyAttribute($value)
    {
        
        return substr($value, 0, 2). '.' . substr($value, 2, 3). '.' . substr($value, 5, 3). '/'.substr($value, 8, 4). '-'. substr($value,12,2);
    }

    private function clearField(?string $parms)
    {
        if (empty($parms)) {
            return ''; //converte de nullo para vazio
        }
        //caracter a subsitituir, valor a ser colocado, string a ser alterada
        return str_replace(['.', '-', '/', '(', ')', ' '], '', $parms);
    }
}
