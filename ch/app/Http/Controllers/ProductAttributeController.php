<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Attributes;
use DB;
use Config;
use View;

class ProductAttributeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    public function index(Request $request)
    {
        View::share('title', 'Attributes');
        $attributesAry= Attributes::orderBy('attrib_name','ASC')->paginate(5);
        return view('pages.attributes.attribute_listing',compact('attributesAry'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        View::share('title', 'Add Attribute');
        return view('pages.attributes.addAttribute');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'attrib_name' => 'required | max:255 | min:3'
        ]);
        Attributes::create($request->all());
        
        return redirect()->to('/pro_attributes')
                        ->with('success','New attribute successfully created.');
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
        View::share('title', 'Update Attribute');
        $attributesData= Attributes::find($id);
        return view('pages.attributes.editAttribute',compact('attributesData'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id=0)
    {
        $this->validate($request, [
            'attrib_name' => 'required | max:255 | min:3'
        ]);
        $idAttribute = $request->id;
        Attributes::find($idAttribute)->update($request->all());
        return redirect()->to('/pro_attributes')
                        ->with('success','Attribute successfully updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $idAttribute = $request->delAttributeId;
        Attributes::find($idAttribute)->delete();
        return redirect()->to('/pro_attributes')
                        ->with('success','Attribute successfully deleted.');
    }
    
    /**
     * Show the options for attributes.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function options(Request $request, $id=0)
    {
        View::share('title', "Attribute's Options");
        $attributesOptions= DB::table('attribute_vals')->where('attrib_id', '=', $id)->orderBy('value', 'ASC')->paginate(5);
        return view('pages.attributes.attributeOptions',compact('attributesOptions'))
                    ->with('idAttribute', $id)
                    ->with('i', ($request->input('page', 1) - 1) * 5);
    }
    /**
     * Add new attribute option.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function optionsAdd($idAttribute=0)
    {
        View::share('title', "Add Attribute's Option");
        return view('pages.attributes.addOption',compact('idAttribute'));
    }
    /**
     * Store a newly created attribute option in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function optionsSave(Request $request,$idAttribute=0)
    {
        $this->validate($request, [
            'attrib_id' => 'required | min:1',
            'value' => 'required | max:255 | min:1'
        ]);
        DB::table('attribute_vals')->insert(['attrib_id'=>$request->attrib_id, 'value'=> $request->value]);
        
        return redirect()->to("/attribute_options/".$idAttribute)
                        ->with('success','New attribute option successfully created.');
    }
    /**
     * Edit new attribute option.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function optionsEdit($idAttribute=0,$idOption=0)
    {
        View::share('title', "Update Attribute's Option");
        $attributesData= DB::table('attribute_vals')->select('id','attrib_id','value')->where('id', '=', $idOption)->get();
        return view('pages.attributes.editOption',compact('attributesData'))
                ->with('idOption',$idOption)
                ->with('idAttribute',$idAttribute);
    }
    
    /**
     * Update attribute option in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function optionsUpdate(Request $request,$idAttribute=0,$idOption=0)
    {
        $this->validate($request, [
            'value' => 'required | max:255 | min:1'
        ]);
        DB::table('attribute_vals')->where('id', $idOption)->update(['value'=> $request->value]);
        
        return redirect()->to("/attribute_options/".$idAttribute)
                        ->with('success','Attribute option successfully updated.');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function optionsDestroy(Request $request)
    {
        $attributeId = $request->attributeId;
        $OptionId = $request->OptionId;
        DB::table('attribute_vals')->where('id', $OptionId)->delete();
        return redirect()->to("/attribute_options/".$attributeId)
                        ->with('success','Attribute option successfully deleted.');
    }
}
