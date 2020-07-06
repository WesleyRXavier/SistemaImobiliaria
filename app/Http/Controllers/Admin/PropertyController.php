<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Property;
use App\User;
use App\PropertyImage;
use App\Http\Requests\Admin\Property as PropertyRequest;
use App\Support\Cropper;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $properties = Property::orderBy('id', 'DESC')->get();

        return view('admin.properties.index',[
            'properties'=>$properties
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::orderBy('name')->get();
        return view('admin.properties.create', [
            'users'=> $users
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PropertyRequest $request)
    {  
        $createProperty = Property::create($request->all());
         $createProperty->setSlug();

        $validator = Validator::make($request->only('files'), ['files.*' => 'image']);

        if ($validator->fails() === true) {
            return redirect()->back()->withInput()->with([
                'color' => 'orange',
                'message' => 'Todas as imagens devem ser do tipo jpg, jpeg ou png.',
            ]);
        }

        if($request->allFiles()){
            foreach($request->allFiles()['files'] as $image){
                $propertyImage = new PropertyImage();
                $propertyImage->property = $createProperty->id;
                $propertyImage->path = $image->store('properties/' . $createProperty->id);
                $propertyImage->save();

                unset($propertyImage);//limpa a memoria 
            }
          
        }

        return redirect()->route('admin.properties.edit',[
            'property'=>$createProperty->id
        ])->with(['color'=>'green', 'message'=>'Imovél cadastrado com sucesso!!!']);
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $property = Property::where('id', $id)->first();
        $users = User::orderBy('name')->get();
        return view('admin.properties.edit', [
            'property' =>$property,
            'users'=> $users
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PropertyRequest $request, $id)
    {
        $property = Property::where('id', $id)->first();
        $property->fill($request->all());
        $property->setSaleAttribute($request->sale);
        $property->setRentAttribute($request->rent);
        $property->setAirConditioningAttribute($request->air_conditioning);
        $property->setBarAttribute($request->bar);
        $property->setLibraryAttribute($request->library);
        $property->setBarbecueGrillAttribute($request->barbecue_grill);
        $property->setAmericanKitchenAttribute($request->american_kitchen);
        $property->setFittedKitchenAttribute($request->fitted_kitchen);
        $property->setPantryAttribute($request->pantry);
        $property->setEdiculeAttribute($request->edicule);
        $property->setOfficeAttribute($request->office);
        $property->setBathtubAttribute($request->bathtub);
        $property->setFirePlaceAttribute($request->fireplace);
        $property->setLavatoryAttribute($request->lavatory);
        $property->setFurnishedAttribute($request->furnished);
        $property->setPoolAttribute($request->pool);
        $property->setSteamRoomAttribute($request->steam_room);
        $property->setViewOfTheSeaAttribute($request->view_of_the_sea);

        $property->save();
        $property->setSlug();
        
        $validator = Validator::make($request->only('files'), ['files.*' => 'image']);

        if ($validator->fails() === true) {
            return redirect()->back()->withInput()->with([
                'color' => 'orange',
                'message' => 'Todas as imagens devem ser do tipo jpg, jpeg ou png.',
            ]);
        }

        if($request->allFiles()){
            foreach($request->allFiles()['files'] as $image){
                $propertyImage = new PropertyImage();
                $propertyImage->property = $property->id;
                $propertyImage->path = $image->store('properties/' . $property->id);
                $propertyImage->save();

                unset($propertyImage);//limpa a memoria 
            }
           
        }
        

        return redirect()->route('admin.properties.edit',[
            'property'=>$property->id
        ])->with(['color'=>'green', 'message'=>'Imovél atualizado com sucesso!!!']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    public function imageSetCover(Request $request){

       
        $imageSetCover = PropertyImage::where('id', $request->image)->first();
        $allImage = PropertyImage::where('property', $imageSetCover->property)->get();
        foreach($allImage as $image){
            $image->cover = null;
            $image->save();
        }
        $imageSetCover->cover = true;
        $imageSetCover->save();

        $json = [
            'success'=> true
        ];

        return response()->json($json);

    }

    public function imageRemove(Request $request)
    {
        $imageDelete = PropertyImage::where('id', $request->image)->first();

        Storage::delete($imageDelete->path);
        Cropper::flush($imageDelete->path);
        $imageDelete->delete();

        $json = [
            'success' => true
        ];
        return response()->json($json);
    }

}
