<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Property;
use App\Contract;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
       // $user = User::where('id', 1);
      //  $user->password = bcrypt('123');
         //dd($user);
     //   $user->save();
        if(Auth::check()===true){
            return redirect()->route('admin.home');
        }
        return view('admin.index');
    }

    public function home()
    {
        $lessors = User::lessors()->count();
        $lessees = User::lessees()->count();
        $team = User::where('admin', 1)->count();

        $propertiesAvailable = Property::available()->count();
        $propertiesUnavailable = Property::unavailable()->count();
        $propertiesTotal = Property::all()->count();

        $contractsPendent = Contract::pendent()->count();
        $contractsActive = Contract::active()->count();
        $contractsCanceled = Contract::canceled()->count();
        $contractsTotal = Contract::all()->count();

        $contracts = Contract::orderBy('id', 'DESC')->limit(10)->get();

        $properties = Property::orderBy('id', 'DESC')->limit(3)->get();

        return view('admin.dashboard', [
            'lessors' => $lessors,
            'lessees' => $lessees,
            'team' => $team,
            'propertiesAvailable' => $propertiesAvailable,
            'propertiesUnavailable' => $propertiesUnavailable,
            'propertiesTotal' => $propertiesTotal,
            'contractsPendent' => $contractsPendent,
            'contractsActive' => $contractsActive,
            'contractsCanceled' => $contractsCanceled,
            'contractsTotal' => $contractsTotal,
            'contracts' => $contracts,
            'properties' => $properties,
        ]);
    }
    public function login(Request $request)
    {
        //verifica se o campo email e password e vazio, outro campos passara direto
        if (in_array('', $request->only('email', 'password'))) {
            $json['msg'] = $this->message->error('Informe os dados')->render();
            return response()->json($json);
        }
        if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            $json['msg'] = $this->message->error('Email Invalido !!!')->render();
            return response()->json($json);
        }

        $credential = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        //faz o login com as credenciais
        if (!Auth::attempt($credential)) {
            $json['msg'] = $this->message->error('Email ou Senha não conferem !!!')->render();
            return response()->json($json);
        }
        $this->authenticate($request->getClientIp());//getClientIp() pega o ip de onde esta partindo a requisicao

        $json['redirect'] = route('admin.home');
        return response()->json($json);
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('admin.login');
    }
    private function authenticate(string $ip){
        $user = User::Where('id', Auth::user()->id);
        $user->update([
            'last_login_at'=>date('Y-m-d:H:i:s'),
            'last_login_ip'=>$ip,
        ]);

    }
}
