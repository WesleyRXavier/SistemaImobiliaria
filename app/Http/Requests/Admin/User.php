<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class User extends FormRequest
{
   
    public function authorize()
    {
        return  Auth::check();//so permite requisicao se estiver logado
    }
    public function all($keys = null)
    {
        return $this->validateFields(parent::all());
    }

    public function validateFields(array $inputs)
    {
        $inputs['document'] = str_replace(['.', '-'], '', $this->request->all()['document']);
        return $inputs;
    }

    
    public function rules()
    {
        return [
            //dados pessoais
            'name'=>'required|min:3|max:191',
            'genre'=>'in:male,female,other',
            'document' => (!empty($this->request->all()['id']) ? 'required|min:11|max:14|unique:users,document,' . $this->request->all()['id'] : 'required|min:11|max:14|unique:users,document'),
            'document_secondary'=>'required|min:7|max:12',
            'document_secondary_complement'=>'required',
            'date_of_birth'=>'required|date_format:d/m/Y',
            'civil_status'=>'required|in:married,separated,single,divorced,widower',
            'cover'=>'image',

            //aba renda
            'occupation'=>'required',
            'income'=>'required',
            'company_work'=>'required',

            //aba endereco
            'zipcode'=>'required|min:8|max:9',
            'street'=>'required',
            'number'=>'required',
            'neighborhood'=>'required',
            'state'=>'required',
            'city'=>'required',

            //aba contato
            'cell'=>'required',

            //aba acesso
            'email' => (!empty($this->request->all()['id']) ? 'required|email|unique:users,email,' . $this->request->all()['id'] : 'required|email|unique:users,email'),
           // 'password'=>'required',
            //aba conjugue
           'type_of_communion'=>'required_if:civil_status, married,separated|in:Comunhão Universal de Bens,Comunhão Parcial de Bens,Separação Total de Bens,Participação Final de Aquestos',
            'spouse_name'=>'required_if:civil_status,married,separated|min:3|max:191',
            'spouse_genre'=>'required_if:civil_status,married,separated|in:male,female,other',
            'spouse_document'=>'required_if:civil_status, married,separated|min:11|max:14',
            'spouse_document_secondary'=>'required_if:civil_status,married,separated|min:7|max:12',
            'spouse_document_secondary_complement'=>'required_if:civil_status,married,separated',
            'spouse_date_of_birth'=>'required_if:civil_status, married,separated|date_format:d/m/Y',
            'spouse_occupation'=>'required_if:civil_status, married,separated',
            'spouse_income'=>'required_if:civil_status, married,separated',
            
            'spouse_company_work'=>'required_if:civil_status, married,separated',
            

        ];
    }
}
