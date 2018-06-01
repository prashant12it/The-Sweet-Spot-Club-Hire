<?php

namespace App\Http\Controllers;

use App;
use App\Banner;
use Config;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use View;

class AdminBannerController extends Controller
{
    public function index(Request $request)
    {
        View::share('title', 'Banners');
        $rowsPerPage = Config::get('constants.PaginationRowsPerPage');
        $baseUrl = Config::get('constants.BaseURL');
        $bannersAry = Banner::orderBy('id', 'DESC')->paginate($rowsPerPage);
        if (count($bannersAry) > 0) {
            $DBTables = Config::get('constants.DbTables');
            foreach ($bannersAry as $bannerKey => $banner) {
                $whereAry = array();
                $whereAry['banner_ref_key'] = trim($banner->banner_reference_id);
                $banner_count = DB::table($DBTables['Partner_Clicks'])
                    ->where($whereAry)->select('id')->get();
                $bannersAry[$bannerKey]->clicks_count = count($banner_count);
            }
        }


        return view('pages.banners.banner_listing', compact('bannersAry', 'baseUrl'))
            ->with('i', ($request->input('page', 1) - 1) * $rowsPerPage);
    }

    public function getBannerReferenceId()
    {

        $DBTables = Config::get('constants.DbTables');

        $chars = "234567890abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $i = 0;
        $reference_key = "";
        $unique_key = false;

        while ($i <= 5) {
            $reference_key .= $chars{mt_rand(0, strlen($chars) - 1)};
            $i++;
        }
        if (trim($reference_key) != '') {
            do {
                $invalid = 1;

                $partnerKeys = DB::table($DBTables['Banners'])
                    ->where('banner_reference_id', '=', trim($reference_key))
                    ->select('id')
                    ->get();

                if (count($partnerKeys) == 0) {
                    $unique_key = true;
                } else {
                    $reference_key .= $reference_key . $invalid;
                    $invalid = $invalid + 1;
                }

            } while (!$unique_key);
            return $reference_key;
        }


    }

    public function create()
    {
        View::share('title', 'Add Image Banner');
        return view('pages.banners.banner_add');
    }

    public function createTextBanner()
    {
        View::share('title', 'Add Text Banner');
        return view('pages.banners.banner_add_text');
    }

    public function store(Request $request)
    {

        Input::merge(array_map('trim', Input::all()));

        $rules = array(
            'title' => 'required | max:255',
            'width' => 'required | numeric | min:1',
            'height' => 'required | numeric | min:1',
            'file_name' => 'required | image | mimes:jpeg,bmp,png,gif',
        );

        $validator = $this->getValidationFactory()->make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->to($this->getRedirectUrl())
                ->withInput($request->input())
                ->withErrors($this->formatValidationErrors($validator), $this->errorBag());
        } else {

            if ($request->hasFile('file_name')) {
                $imageTempName = $request->file_name->getPathname();
                $imageName = time() . '.' . $request->file_name->getClientOriginalExtension();
                $request->file_name->move(public_path('banners_img'), $imageName);
            }


            $inputData = $request->all();
            $inputData['iActive'] = '1';
            $inputData['file_name'] = trim($imageName);
            $inputData['banner_reference_id'] = $this->getBannerReferenceId();

            Banner::create($inputData);

            return redirect()->to('/banners')
                ->with('success', 'New banner successfully created.');
        }
    }

    public function storeTextBanner(Request $request)
    {

        Input::merge(array_map('trim', Input::all()));
        /*$regex = '/^(http:\/\/|https:\/\/|http:\/\/|https:\/\/)[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/';

        $rules = array('title' => array('required', 'max:255'), 'url_val' => array('required', 'regex:' . $regex));
        $messages = [
            'title.required' => "Banner title can't be left blank.",
            'title.max' => 'Banner title must be less than 256 characters.',
            'url_val.required' => "URL can't be left blank.",
            'url_val.regex' => 'Enter valid URL. (try putting http:// or https:// or another prefix at the beginning)'
        ];*/
        $rules = array(
            'title' => 'required | max:255',
        );
        $messages = [
            'title.required' => "Banner text can't be left blank.",
            'title.max' => 'Banner text must be less than 256 characters.'
        ];
        $validator = $this->getValidationFactory()->make($request->all(), $rules,$messages);
//        $validator = $this->getValidationFactory()->make($request->only('title', 'url_val'), $rules, $messages);
        if ($validator->fails()) {
            return redirect()->to($this->getRedirectUrl())
                ->withInput($request->input())
                ->withErrors($this->formatValidationErrors($validator), $this->errorBag());
        } else {

            $inputData = $request->all();
            $inputData['iActive'] = '1';
            $inputData['banner_type'] = '1';
            $inputData['file_name'] = '';
            $inputData['banner_reference_id'] = $this->getBannerReferenceId();

            Banner::create($inputData);

            return redirect()->to('/banners')
                ->with('success', 'New banner successfully created.');
        }
    }

    public function show($idBanner = 0)
    {
        if ($idBanner > 0) {
            View::share('title', 'Banner Details');

            $bannerDetailsData = Banner::find($idBanner);

            $baseUrl = Config::get('constants.BaseURL');
            $DBTables = Config::get('constants.DbTables');
            $whereAry = array();
            $whereAry['banner_ref_key'] = trim($bannerDetailsData->banner_reference_id);
            $banner_count = DB::table($DBTables['Partner_Clicks'])
                ->where($whereAry)->select('id')->get();

            $bannerDetailsData->clicks_count = count($banner_count);


            $bannerPartnerAry = DB::table($DBTables['Partner_Clicks'])
                ->join($DBTables['Partners'], $DBTables['Partners'] . '.reference_id', '=', $DBTables['Partner_Clicks'] . '.partner_ref_key')
                ->where($DBTables['Partner_Clicks'] . '.banner_ref_key', '=', trim($bannerDetailsData->banner_reference_id))
                ->select(DB::raw("count(" . $DBTables['Partner_Clicks'] . ".id) as clickCount," . $DBTables['Partners'] . ".name," . $DBTables['Partners'] . ".email, MAX(" . $DBTables['Partner_Clicks'] . ".dt_clicked_on) as last_clicked," . $DBTables['Partner_Clicks'] . ".partner_ref_key"))
                ->groupBy($DBTables['Partner_Clicks'] . '.partner_ref_key')
                ->orderBy($DBTables['Partners'] . '.name', 'ASC')
                ->get();

            if (count($bannerPartnerAry) > 0) {
                foreach ($bannerPartnerAry as $p => $bannerPartner) {
                    $whereAryBanner = array();
                    $whereAryBanner['banner_ref_key'] = trim($bannerDetailsData->banner_reference_id);
                    $whereAryBanner['partner_ref_key'] = trim($bannerPartner->partner_ref_key);
                    $banner_sale = DB::table($DBTables['Orders'])
                        ->where($whereAryBanner)
                        ->select(DB::raw('SUM(paid_amnt) as total_sale'))
                        ->get();

                    $bannerPartnerAry[$p]->total_sale = $banner_sale[0]->total_sale;
                }
            }
            return view('pages.banners.banner_details', compact('bannerDetailsData', 'bannerPartnerAry', 'baseUrl'));
        } else {
            return redirect()->to("/banners");
        }
    }

    public function edit($idBanner = 0)
    {
        if ($idBanner > 0) {
            View::share('title', 'Edit Image Banner');
            $baseUrl = Config::get('constants.BaseURL');
            $banner = Banner::find($idBanner);
            return view('pages.banners.banner_edit', compact('banner', 'baseUrl'));

        } else {
            return redirect()->to("/banners");
        }


    }

    public function editTextBanner($idBanner = 0)
    {
        if ($idBanner > 0) {
            View::share('title', 'Edit Text Banner');
            $baseUrl = Config::get('constants.BaseURL');
            $banner = Banner::find($idBanner);
            return view('pages.banners.banner_edit_text', compact('banner', 'baseUrl'));

        } else {
            return redirect()->to("/banners");
        }


    }

    public function update(Request $request)
    {

        if ((int)$request->bannerId > 0) {
            Input::merge(array_map('trim', Input::all()));

            $rules = array(
                'title' => 'required | max:255',
                'width' => 'required | numeric | min:1',
                'height' => 'required | numeric | min:1'
            );

            $old_file_name = trim($request->old_file_name);
            $file_name = trim($request->file_name);

            if (trim($file_name) != '') {
                $rules[0]['file_name'] = "image | mimes:jpeg,bmp,png";
            }

            $validator = $this->getValidationFactory()->make($request->all(), $rules);

            if ($validator->fails()) {
                return redirect()->to($this->getRedirectUrl())
                    ->withInput($request->input())
                    ->withErrors($this->formatValidationErrors($validator), $this->errorBag());
            } else {
                if (trim($file_name) != '') {
                    if ($request->hasFile('file_name')) {
                        $imageTempName = $request->file_name->getPathname();
                        $imageName = time() . '.' . $request->file_name->getClientOriginalExtension();
                        $request->file_name->move(public_path('banners_img'), $imageName);
                    }
                } else {
                    $imageName = trim($old_file_name);
                }

                $inputData = $request->all();
                $inputData['file_name'] = trim($imageName);

                Banner::find($request->bannerId)->update($inputData);

                return redirect()->to('/banners')
                    ->with('success', 'Banner successfully updated.');
            }

        } else {
            return redirect()->to("/banners");
        }

    }

    public function updateTextBanner(Request $request)
    {
        if ((int)$request->bannerId > 0) {
            Input::merge(array_map('trim', Input::all()));

            /*$regex = '/^(http:\/\/|https:\/\/|http:\/\/|https:\/\/)[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/';

            $rules = array('title' => array('required', 'max:255'), 'url_val' => array('required', 'regex:' . $regex));
            $messages = [
                'title.required' => "Banner title can't be left blank.",
                'title.max' => 'Banner title must be less than 256 characters.',
                'url_val.required' => "URL can't be left blank.",
                'url_val.regex' => 'Enter valid URL. (try putting http:// or https:// or another prefix at the beginning)'
            ];
            $validator = $this->getValidationFactory()->make($request->only('title', 'url_val'), $rules, $messages);*/
            $rules = array(
                'title' => 'required | max:255',
            );
            $messages = [
                'title.required' => "Banner text can't be left blank.",
                'title.max' => 'Banner text must be less than 256 characters.'
            ];
            $validator = $this->getValidationFactory()->make($request->all(), $rules,$messages);
            if ($validator->fails()) {
                return redirect()->to($this->getRedirectUrl())
                    ->withInput($request->input())
                    ->withErrors($this->formatValidationErrors($validator), $this->errorBag());
            } else {
                $inputData = $request->all();
                Banner::find($request->bannerId)->update($inputData);
                return redirect()->to('/banners')
                    ->with('success', 'Banner successfully updated.');
            }

        } else {
            return redirect()->to("/banners");
        }

    }

    public function searchBanner(Request $request)
    {
        View::share('title', 'Banner Search');
        $DBTables = Config::get('constants.DbTables');
        $rowsPerPage = Config::get('constants.PaginationRowsPerPage');

        $previousSearchAry = session('searchBanner');
        if ($previousSearchAry && !$request->searchAry) {
            $request->searchAry = $previousSearchAry;
        }

        if ($request->searchAry) {
            session()->flash('searchBanner', $request->searchAry); // Store it as flash data.

            $title = trim($request->searchAry['title']);
            $iActive = $request->searchAry['iActive'];

            if (trim($iActive) != '') {
                $where = "title like '%" . $title . "%' AND iActive = '" . (int)$iActive . "'";
            } else {
                $where = "title like '%" . $title . "%'";
            }
//            DB::enableQueryLog();

            $bannersAry = DB::table($DBTables['Banners'])
                ->whereRaw($where)
                ->select('*')
                ->orderBy('title', 'ASC')
                ->paginate($rowsPerPage);

//            dd(DB::getQueryLog());die;
        } else {
            $bannersAry = Banner::orderBy('title', 'ASC')->paginate($rowsPerPage);
        }

        if (count($bannersAry) > 0) {
            $DBTables = Config::get('constants.DbTables');
            foreach ($bannersAry as $bannerKey => $banner) {
                $whereAry = array();
                $whereAry['banner_ref_key'] = trim($banner->banner_reference_id);
                $banner_count = DB::table($DBTables['Partner_Clicks'])
                    ->where($whereAry)->select('id')->get();
                $bannersAry[$bannerKey]->clicks_count = count($banner_count);
            }
        }

        return view('pages.banners.banner_listing', compact('bannersAry'))
            ->with('i', ($request->input('page', 1) - 1) * $rowsPerPage);
    }

    public function status_update(Request $request)
    {
        $DBTables = Config::get('constants.DbTables');

        $idBanner = (int)$request->idBanner;
        $iActive = (int)$request->iActive;

        if ($idBanner > 0) {
            DB::table($DBTables['Banners'])->where('id', $idBanner)->update(['iActive' => (int)$iActive]);
            return redirect()->to("/banners")
                ->with('success', 'Banner status successfully updated.');
        } else {
            return redirect()->to("/partners")
                ->with('error', 'Something is wrong, status not updated successfully.');
        }
    }
}
