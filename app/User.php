<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Storage;
use App\Support\Cropper;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'genre',
        'document',
        'document_secondary',
        'document_secondary_complement',
        'date_of_birth',
        'place_of_birth',
        'civil_status',
        'cover',
        'occupation',
        'income',
        'company_work',
        'zipcode',
        'street',
        'number',
        'complement',
        'neighborhood',
        'state',
        'city',
        'telephone',
        'cell',
        'type_of_communion',
        'spouse_name',
        'spouse_genre',
        'spouse_document',
        'spouse_document_secondary',
        'spouse_document_secondary_complement',
        'spouse_date_of_birth',
        'spouse_place_of_birth',
        'spouse_occupation',
        'spouse_income',
        'spouse_company_work',
        'lessor',
        'lessee',
        'admin',
        'client'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    //relacionamentos
    public function companies(){
        return $this->hasMany(Company::class, 'user', 'id');

    }
    public function properties(){
        return $this->hasMany(Property::class, 'user', 'id');

    }




    //retorna a url da foto
    public function getUrlCoverAttribute()
    {
        if(!empty($this->cover)){
            return Storage::url(Cropper::thumb($this->cover, 500,500)); 
        }
        return '';
    }

    public function scopeLessors($query){
        return $query->where('lessor', true);

    }
    public function scopeLessees($query){
        return $query->where('lessee', true);

    }

    public function setLessorAttribute($value)
    {
        //operador ternario pra indicar o valor de lessor pq no banco so pode ser 0 ou 1
        $this->attributes['lessor'] = ($value === true || $value === 'on' ? 1 : 0);
    }
    public function setLesseeAttribute($value)
    {
        //operador ternario pra indicar o valor de lessor pq no banco so pode ser 0 ou 1
        $this->attributes['lessee'] = ($value === true || $value === 'on' ? 1 : 0);
    }

    public function setDocumentAttribute($value)
    {
        //recebe a string  cpf e chama a funcao pra  retirar os caracaters 
        $this->attributes['document'] = $this->clearField($value);
    }

    public function getDocumentAttribute($value)
    {
        //    (string, inicio, fim(tamanho))concatena com
        return substr($value, 0, 3). '.' . substr($value, 3, 3). '.' . substr($value, 6, 3). '-'.substr($value, 9, 2);
    }

    public function setDateOfBirthAttribute($value)
    {
        //recebe a string da data e chama a funcao pra converter
        $this->attributes['date_of_birth'] = $this->convertStringToDate($value);
    }
    public function getDateOfBirthAttribute($value)
    {
        return date('d/m/Y', strtotime($value));
    }

    public function setIncomeAttribute($value)
    {
        //recebe a string da data e chama a funcao pra converter
        $this->attributes['income'] = floatval($this->convertStringToDouble($value));
    }

    public function getIncomeAttribute($value)    
    {
        //(number_format(valor, casas decimal, ',',milhar, '.'))
        return number_format($value, 2, ',' , '.' );
        
    }

    public function setZipcodeAttribute($value)
    {
        //recebe a string  e chama a funcao pra retirar a mascara
        $this->attributes['zipcode'] = $this->clearField($value);
    }
    public function setTelephoneAttribute($value)
    {
        //recebe a string  e chama a funcao pra retirar a mascara
        $this->attributes['telephone'] = $this->clearField($value);
    }
    public function setCellAttribute($value)
    {
        //recebe a string  e chama a funcao pra retirar a mascara
        $this->attributes['cell'] = $this->clearField($value);
    }
    public function setPasswordAttribute($value)
    {
        //recebe a string  e chama a funcao pra retirar a mascara
        $this->attributes['password'] = bcrypt($value);
    }
    //dados conjugue
    public function setSpouseDocumentAttribute($value)
    {
        //recebe a string dp cpf e chama a funcao pra 
        $this->attributes['spouse_document'] = $this->clearField($value);
    }

    public function getSpouseDocumentAttribute($value)
    {
        //    (string, inicio, fim(tamanho))concatena com
        return substr($value, 0, 3). '.' . substr($value, 3, 3). '.' . substr($value, 6, 3). '-'.substr($value, 9, 2);
    }

    public function setSpouseDateOfBirthAttribute($value)
    {
        //recebe a string da data e chama a funcao pra converter
        $this->attributes['spouse_date_of_birth'] = $this->convertStringToDate($value);
    }
    public function getSpouseDateOfBirthAttribute($value)
    {
        return date('d/m/Y', strtotime($value));
    }
    public function setSpouseIncomeAttribute($value)
    {
        //recebe a string da data e chama a funcao pra converter
        $this->attributes['spouse_income'] = floatval($this->convertStringToDouble($value));
    }
    public function getSpouseIncomeAttribute($value)    
    {
        //(number_format(valor, casas decimal, ',',milhar, '.'))
        return number_format($value, 2, ',' , '.' );
        
    }

    public function setAdminAttribute($value)
    {
        //operador ternario pra indicar o valor de lessor pq no banco so pode ser 0 ou 1
        $this->attributes['admin'] = ($value === true || $value === 'on' ? 1 : 0);
    }
    public function setClientAttribute($value)
    {
        //operador ternario pra indicar o valor de lessor pq no banco so pode ser 0 ou 1
        $this->attributes['client'] = ($value === true || $value === 'on' ? 1 : 0);
    }
    //FUNCOES PRIVADAS


    //converte moeda para double
    private function convertStringToDouble(?string $params)
    {
        if (empty($params)) {
            return null;
        }
        return str_replace(',', '.', str_replace('.', '', $params));
    }

    //coverte dia/mes/ano para ano-mes-dia
    private function convertStringToDate(?string $parms)
    {
        if (empty($parms)) {
            return null;
        }
        list($day, $month, $year) = explode('/', $parms);
        return (new \DateTime($year . '-' . $month . '-' . $day))->format('Y-m-d');
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
