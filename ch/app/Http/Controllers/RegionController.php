<?php

namespace App\Http\Controllers;
use App\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use DB;
use Config;
use View;
use Session;

class RegionController extends Controller
{
    public function __construct() {
		$this->DBTables = Config::get( 'constants.DbTables' );
	}
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        View::share( 'title', 'View Regions' );
        Session::forget('backpage');
		session()->flash( 'searchRegion', null );
		$StatesArr     = DB::table( $this->DBTables['States'] )->orderBy( 'name', 'ASC' )->get();
		$rowsPerPage = Config::get( 'constants.PaginationRowsPerPage' );
//		$RegionsArr    = Region::orderBy( 'stateid', 'ASC' )->paginate( $rowsPerPage );

            $RegionsArr = DB::table( $this->DBTables['Regions'] . ' as r' )
                ->join( $this->DBTables['States'] . ' as s', 'r.stateid', '=', 's.id' )
                ->select( 'r.*', 's.name')
                ->orderBy( 'r.stateid', 'ASC' )

                ->paginate($rowsPerPage);
//        dd($products);die;
		return view( 'pages.postcodes.viewregions', compact( 'RegionsArr','StatesArr','rowsPerPage' ) );
			
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        View::share( 'title', 'Add new region' );
		$StatesArr     = DB::table( $this->DBTables['States'] )->orderBy( 'name', 'ASC' )->get();

		return view( 'pages.postcodes.addRegion', compact( 'StatesArr' ) );
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
			'region'         => 'required | max:255'
		);
                $messages = array(
    'stateid.required' => 'State field is required.',
                    'stateid.integer' => 'Invalid state. Please select a valid state.'
);
		$validator = $this->getValidationFactory()->make( $request->only( 'stateid', 'region'), $rules,$messages );

		if ( $validator->fails() ) {
			return redirect()->to( $this->getRedirectUrl() )
			                 ->withInput( $request->input() )
			                 ->withErrors( $this->formatValidationErrors( $validator ), $this->errorBag() );
		} else {
			$RegionData = Region::create( $request->all() );
				$regionid      = $RegionData->id;
			if ( $regionid > 0 ) {

				return redirect()->to( '/regions' )
				                 ->with( 'success', 'Region added successfully' );
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
     * @param  \App\Region  $region
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $rowsPerPage = Config::get( 'constants.PaginationRowsPerPage' );
        $RegionPostCodesArr = DB::table( $this->DBTables['Shipping'].' as S' )
            ->join( $this->DBTables['Regions'].' as R', 'S.region_id', '=', 'R.id' )
            ->where('R.id','=',$id)
            ->select( 'S.*','R.stateid','R.region' )
            ->paginate($rowsPerPage);
        return view( 'pages.postcodes.viewPostcodes', compact( 'RegionPostCodesArr','id','rowsPerPage') );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Region  $region
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
       View::share( 'title', 'Edit region' );
		$regioninfo           = Region::find( $id );
		
$StatesArr     = DB::table( $this->DBTables['States'] )->orderBy( 'name', 'ASC' )->get();
		return view( 'pages.postcodes.editRegion', compact( 'regioninfo', 'StatesArr') );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Region  $region
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Region $region)
    {
        Input::merge( array_map( 'trim', Input::all() ) );

		$rules       = array(
			'stateid'     => 'required | integer | min:1',
			'region'         => 'required | max:255'
		);
                $messages = array(
    'stateid.required' => 'State field is required.',
                    'stateid.integer' => 'Invalid state. Please select a valid state.'
);

		$id       = $request->regionid;
$validator             = $this->getValidationFactory()->make( $request->only( 'stateid', 'region' ), $rules, $messages );
		if ( $validator->fails() ) {
			
			return redirect()->to( $this->getRedirectUrl() )
			                 ->withInput( $request->input() )
			                 ->withErrors( $this->formatValidationErrors( $validator ), $this->errorBag() );
		} else {
			if ( $id > 0 ) {
                            Region::find( $id )->update( $request->all() );				
				return redirect()->to( '/regions' )
				                 ->with( 'success', 'Region updated successfully' );
			}

		}
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Region  $region
     * @return \Illuminate\Http\Response
     */
    public function destroy( Request $request )
    {
       $id = $request->delProdid;
		Region::find( $id )->delete();

		return redirect()->to( '/regions' )
		                 ->with( 'success', 'Region deleted successfully' );
    }

    public function getregions(Request $request){
        $regions = Region::where('stateid','=',$request->stateid)->get();
        return $regions;
    }

    public function search(Request $request)
    {
        View::share( 'title', 'View Regions' );
        Session::forget('backpage');
        session()->flash( 'searchRegion', null );
        $StatesArr     = DB::table( $this->DBTables['States'] )->orderBy( 'name', 'ASC' )->get();
        $rowsPerPage = Config::get( 'constants.PaginationRowsPerPage' );
        $allInput    = $request->input();
        $searchState = Input::get ( 'searchState' );
        if(count($allInput)>0){
        $RegionsArr = DB::table( $this->DBTables['Regions'] . ' as r' )
            ->join( $this->DBTables['States'] . ' as s', 'r.stateid', '=', 's.id' )
            ->where('r.stateid','=',$searchState)
            ->select( 'r.*', 's.name')
            ->orderBy( 'r.stateid', 'ASC' )
            ->paginate($rowsPerPage)
        ->setpath('');
            $RegionsArr->appends ( array (
                'searchState' => Input::get ( 'searchState' )
            ) );
            return view( 'pages.postcodes.viewregions', compact( 'RegionsArr','StatesArr','rowsPerPage','allInput','searchState' ) )->withDetails($RegionsArr)->withQuery($searchState);
        }else{
            return redirect( '/regions' );
        }

    }
}
