@extends('layouts.dashboard')

@section('content')

<header id="topbar" class="alt">
    <div class="topbar-left">
        <ol class="breadcrumb">
           <ol class="breadcrumb">
                <li class="crumb-active">
                     <a href="{{url('/offers_mang')}}"><span class="glyphicon glyphicon-gift"></span> Offers</a>
                </li>
                <li class="crumb-active">Edit Offer</li>
            </ol>
        </ol>
    </div>
</header>
<section id="content">
    <div class="panel">

        <div class="panel-heading">
            <span class="panel-title">Add New Offer</span>
        </div>
        <form method="POST" enctype="multipart/form-data" action="{{ url('/edit_offer') }}" class="form-horizontal" role="form" id="editOfferForm">
            <div class="panel-body">
                @if ($message = Session::get('success'))
                <div class="alert alert-success">
                    <p>{{ $message }}</p>
                </div>
                @endif

                {{ csrf_field() }}
                <div class="panel-body p25 bg-light">
                    <div class="section row form-group">
                        <label for="inputStandard" class="col-lg-4 control-label">Offer Name</label>
                        <div class="col-lg-6">
                            <input type="hidden" name="offerId" id="offerId" value="{{$offerData->id}}" />
                            <input type="text" name="name" id="name" value="{{ old('name',$offerData->name) }}" class="gui-input form-control" required="required" placeholder="Please type offer name...">
                            @if ($errors->has('name'))
                            <span class="help-block err">
                                <strong>{{ $errors->first('name') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="section row form-group">
                        <label for="inputStandard" class="col-lg-4 control-label">Offer Code</label>
                        <div class="col-lg-6">
                            <input type="text" name="szCoupnCode" id="szCoupnCode" value="{{ old('szCoupnCode',$offerData->szCoupnCode) }}" class="gui-input form-control" required="required" placeholder="Please type offer code...">
                            @if ($errors->has('szCoupnCode'))
                            <span class="help-block err">
                                <strong>{{ $errors->first('szCoupnCode') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="section row form-group">
                        <label for="inputStandard" class="col-lg-4 control-label">Offer Valid From</label>
                        <div class="col-lg-6">
                            <input type="text"  data-provide="datepicker" name="dt_from" id="dt_from" value="{{ old('dt_from',$offerData->dt_from) }}" class="date gui-input form-control hasDatepicker" placeholder="Please enter offer valid from date...">
                            @if ($errors->has('dt_from'))
                            <span class="help-block err">
                                <strong>{{ $errors->first('dt_from') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="section row form-group">
                        <label for="inputStandard" class="col-lg-4 control-label">Offer Valid Upto</label>
                        <div class="col-lg-6">
                            <input type="text"  data-provide="datepicker" name="dt_upto" id="dt_upto" value="{{ old('dt_upto',$offerData->dt_upto) }}" class="date gui-input form-control hasDatepicker" placeholder="Please enter offer valid upto date...">
                            @if ($errors->has('dt_upto'))
                            <span class="help-block err">
                                <strong>{{ $errors->first('dt_upto') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                     <?php $offer_type = $offerData->offer_type; ?>
                    <div class="section row form-group">
                        <label for="inputStandard" class="col-lg-4 control-label">Offer Type</label>
                        <div class="col-lg-6">
                            <select id="offer_type" name="offer_type" class="gui-input form-control" required="required" onchange="checkOfferType();">
                                <option value="" >Select Offer type..</option>
                                <option value="1" {{(old('offer_type') || $offer_type == '1' ? "selected=selected":"")}}>Percentage</option>
                                <option value="0" {{(old('offer_type') || $offer_type == '0' ? "selected=selected":"")}}>Amount</option>
                            </select>
                            @if ($errors->has('offer_type'))
                            <span class="help-block err">
                                <strong>{{ $errors->first('offer_type') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                   
                    <div class="section row form-group" id="percentage_offer" {{(old('offer_type') || $offer_type == '1' ? "style=display:block":"style=display:none;")}}>
                        <label for="inputStandard" class="col-lg-4 control-label">Offer Percentage</label>
                        <div class="col-lg-6">
                            <input type="text"  name="offer_percntg" id="offer_percntg" value="{{ old('offer_percntg',$offerData->offer_percntg) }}" class="gui-input form-control" placeholder="Please type offer percentage...">
                            @if ($errors->has('offer_percntg'))
                            <span class="help-block err">
                                <strong>{{ $errors->first('offer_percntg') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="section row form-group" id="amount_offer" {{(old('offer_type') || $offer_type == '0' ? "style=display:block":"style=display:none;")}}>
                        <label for="inputStandard" class="col-lg-4 control-label">Offer Amount($)</label>
                        <div class="col-lg-6">
                            <input type="text"  name="offer_amnt" id="offer_amnt" value="{{ old('offer_amnt',$offerData->offer_amnt) }}" class="gui-input form-control" placeholder="Please type offer amount...">
                            @if ($errors->has('offer_amnt'))
                            <span class="help-block err">
                                <strong>{{ $errors->first('offer_amnt') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="section row form-group">
                        <label for="checkbox-custom" class="col-lg-4 control-label">One Time Offer</label>
                        <div class="control-check col-lg-4">
                            <?php $one_time_offer = (int)$offerData->isOneTimeOffer;?>
                            <input type="checkbox"  name="isOneTimeOffer" id="isOneTimeOffer" value="1" {{ (old("isOneTimeOffer") || $one_time_offer == 1 ? "checked=checked":"") }} >
                            @if ($errors->has('isOneTimeOffer'))
                            <span class="help-block err">
                                <strong>{{ $errors->first('isOneTimeOffer') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="section row form-group">
                        <label for="inputStandard" class="col-lg-4 control-label">Offer Description</label>
                        <div class="col-lg-6">
                            <textarea  name="description" id="description" class="gui-input form-control" required="required" placeholder="Please type offer description...">{{ old('description',$offerData->description) }}</textarea>
                            @if ($errors->has('description'))
                            <span class="help-block err">
                                <strong>{{ $errors->first('description') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel-footer clearfix">
                <div class="col-lg-8"></div>
                <div class="col-lg-2 pull-right">
                    <button type="submit" class="button form-control btn-primary pull-right">Save</button>
                </div>
                <div class="col-lg-2"></div>
            </div>
        </form>
    </div>

</div>
</section>

@endsection