<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Http\Requests\Admin\User as UserRequest;
use App\Support\Cropper;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{

    public function index()
    {
        $users = User::all();
        return  view('admin.users.index', [
            'users' => $users
        ]);
    }

    public function team()
    {
        $users = User::where('admin',1)->get();
        return  view('admin.users.team',[
            'users'=> $users
        ]);
    }


    public function create()
    {
        return  view('admin.users.create');
    }


    public function store(UserRequest $request)
    {

        $userCreate = User::create($request->all());
        if(!empty($request->file('cover'))){

            $userCreate->cover = $request->file('cover')->store('user');
            $userCreate->save();

        }
        return redirect()->route('admin.users.create',[
            'users'=>$userCreate->id
        ])->with(['color'=>'green', 'message'=>'Cliente cadastrado com sucesso!!!']);
        
    }


    public function show($id)
    {
        //
    }


    public function edit($id)
    {
        $user = User::where('id', $id)->first();
        return view('admin.users.edit', [
            'user' => $user
        ]);
    }


    public function update(UserRequest $request, $id)
    {
        $user = User::where('id', $id)->first();
        $user->setLessorAttribute($request->lessor);
        $user->setLesseeAttribute($request->lessee);

        if(!empty($request->file('cover'))){
            Storage::delete($user->cover);
            Cropper::flush($user->cover);
            $user->cover = '';
        }

        $user->fill($request->all());
        if(!empty($request->file('cover'))){

            $user->cover = $request->file('cover')->store('user');

        }
        if(!$user->save()){
            return redirect()->back()->withInput()->withErrors();
        }

        return redirect()->route('admin.users.edit',[
            'users'=>$user->id
        ])->with(['color'=>'green', 'message'=>'Cliente atualizado com sucesso!!!']);
        
        
    }


    public function destroy($id)
    {
        //
    }
}
