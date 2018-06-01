<?php

namespace App\Http\Controllers;
use App\Shipping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use DB;
use Config;
use View;
use Session;

class ShippingController extends Controller
{
    public function __construct() {
        $this->DBTables = Config::get( 'constants.DbTables' );
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return redirect( '/regions' );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        View::share( 'title', 'Add new postcode' );
        $StatesArr     = DB::table( $this->DBTables['States'] )->orderBy( 'name', 'ASC' )->get();

        return view( 'pages.postcodes.addPostcode', compact( 'StatesArr' ) );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Input::merge( array_map( 'trim', Input::all() ) );

        $rules       = array(
            'stateid'     => 'required | integer | min:1',
            'region_id'         => 'required | integer | min:1',
            'postcode' => 'required',
            'shipping_cost' => 'required|numeric|between:0.01,9999999.99',
            'suburb' => 'required'
        );
        $messages = array(
            'stateid.required' => 'State field is required.',
            'stateid.integer' => 'Invalid state. Please select a valid state.',
            'region_id.required' => 'Region field is required.',
            'region_id.integer' => 'Invalid region. Please select a valid region.',
            'shipping_cost.required' => 'Shipping cost field is required.',
            'shipping_cost.integer' => 'Invalid shipping cost. Please enter a valid shipping cost.'
        );
        $validator = $this->getValidationFactory()->make( $request->only( 'stateid', 'region_id', 'postcode', 'shipping_cost', 'suburb'), $rules,$messages );

        if ( $validator->fails() ) {
            return redirect()->to( $this->getRedirectUrl() )
                ->withInput( $request->input() )
                ->withErrors( $this->formatValidationErrors( $validator ), $this->errorBag() );
        } else {
            $ShippingData = Shipping::create( $request->all() );
            $shippingid      = $ShippingData->id;
            if ( $shippingid > 0 ) {

                return redirect()->to( '/regions' )
                    ->with( 'success', 'Postcode added successfully' );
            } else {
                return redirect()->to( $this->getRedirectUrl() )
                    ->withInput( $request->input() )
                    ->withErrors( $this->formatValidationErrors( $validator ), $this->errorBag() );
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        View::share( 'title', 'Postcodes' );
        $rowsPerPage = Config::get( 'constants.PaginationRowsPerPage' );
        $RegionPostCodesArr = DB::table( $this->DBTables['Shipping'].' as S' )
            ->join( $this->DBTables['Regions'].' as R', 'S.region_id', '=', 'R.id' )
            ->where('R.id','=',$id)
            ->select( 'S.*','R.stateid','R.region' )
            ->orderBy('S.postcode','ASC')
            ->paginate($rowsPerPage);
        return view( 'pages.postcodes.viewPostcodes', compact( 'RegionPostCodesArr','id','rowsPerPage') );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        View::share( 'title', 'Edit postcode' );
        $shippinginfo           = Shipping::find( $id );
        $StatesArr     = DB::table( $this->DBTables['States'] )->orderBy( 'name', 'ASC' )->get();
        $stateid = DB::table( $this->DBTables['Regions'] )->where('id','=',$shippinginfo->region_id)->get();
        $regions = DB::table( $this->DBTables['Regions'] )->where('stateid','=',$stateid[0]->stateid)->get();
        $selectedRegion = $shippinginfo->region_id;
//        dd($stateid);
        return view( 'pages.postcodes.editPostcode', compact( 'shippinginfo', 'StatesArr','stateid','regions','selectedRegion') );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        Input::merge( array_map( 'trim', Input::all() ) );

        $rules       = array(
            'stateid'     => 'required | integer | min:1',
            'region_id'         => 'required | integer | min:1',
            'postcode' => 'required',
            'shipping_cost' => 'required|numeric|between:0.01,9999999.99',
            'suburb' => 'required'
        );
        $messages = array(
            'stateid.required' => 'State field is required.',
            'stateid.integer' => 'Invalid state. Please select a valid state.',
            'region_id.required' => 'Region field is required.',
            'region_id.integer' => 'Invalid region. Please select a valid region.',
            'shipping_cost.required' => 'Shipping cost field is required.',
            'shipping_cost.integer' => 'Invalid shipping cost. Please enter a valid shipping cost.'
        );

        $id       = $request->postcodeid;
        $validator = $this->getValidationFactory()->make( $request->only( 'stateid', 'region_id', 'postcode', 'shipping_cost', 'suburb'), $rules,$messages );
        if ( $validator->fails() ) {

            return redirect()->to( $this->getRedirectUrl() )
                ->withInput( $request->input() )
                ->withErrors( $this->formatValidationErrors( $validator ), $this->errorBag() );
        } else {
            if ( $id > 0 ) {
                Shipping::find( $id )->update( $request->all() );
                return redirect()->to( '/regions' )
                    ->with( 'success', 'Postcode updated successfully' );
            }

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy( Request $request )
    {
        $id = $request->delProdid;
        Shipping::find( $id )->delete();

        return redirect()->to( '/regions' )
            ->with( 'success', 'Postcode deleted successfully' );
    }

    public function search(Request $request)
    {
        $id = Input::get ( 'regid' );
        $searchPostcode = Input::get ( 'searchPostcode' );
        View::share( 'title', 'Postcodes' );
        $rowsPerPage = Config::get( 'constants.PaginationRowsPerPage' );
        $RegionPostCodesArr = DB::table( $this->DBTables['Shipping'].' as S' )
            ->join( $this->DBTables['Regions'].' as R', 'S.region_id', '=', 'R.id' )
            ->where('R.id','=',$id)
            ->where('S.postcode','like','%'.$searchPostcode.'%')
            ->select( 'S.*','R.stateid','R.region' )
            ->orderBy('S.postcode','ASC')
            ->paginate($rowsPerPage);
        $RegionPostCodesArr->appends ( array (
            'searchPostcode' => Input::get ( 'searchPostcode' ),
            'regid' => Input::get ( 'regid' )
        ) );
        return view( 'pages.postcodes.viewPostcodes', compact( 'RegionPostCodesArr','id','rowsPerPage','searchPostcode') )->withDetails($RegionPostCodesArr)->withQuery($searchPostcode,$id);
    }
}