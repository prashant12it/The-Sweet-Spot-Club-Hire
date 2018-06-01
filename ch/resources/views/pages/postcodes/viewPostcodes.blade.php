@extends('layouts.dashboard')

@section('content')

    <header id="topbar" class="alt">
        <div class="topbar-left">
            <ol class="breadcrumb">
                <li class="crumb-active">
                    <a href="{{url('/regions')}}"><span class="glyphicon glyphicon-book"></span> Postcodes</a>
                </li>
            </ol>
        </div>
    </header>
    <section id="content">
        <div class="panel">
            <div class="panel-heading">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-left">
                        <h2>Postcodes</h2>
                    </div>
                    <div class="pull-right">@if(!Session::has('backpage'))
                            {{Session::put('backpage', url()->previous())}}
                        @endif
                        <a class="btn btn-success" href="{{Session::get('backpage')}}"> Back</a>
                        <a class="btn btn-success" href="{{url('/postcodes/create')}}"> Add New Postcode</a>
                    </div>

                </div>
            </div>
            <div class="panel-body">
                @if ($message = Session::get('success'))
                    <div class="alert alert-success">
                        <p>{{ $message }}</p>
                    </div>
                @endif
                <form method="POST" action="{{url('/search-postcode')}}" class="form-horizontal" role="form"
                      id="account2">
                    {{ csrf_field() }}
                    <div class="panel panel-widget calendar-widget calendar-alt" id="p02">
                        <div class="panel-heading ui-sortable-handle">
                        <span class="panel-icon">
                            <i class="fa fa-search"></i>
                        </span>
                            <span class="panel-title"> Search</span>
                        </div>
                        <div class="panel-body bg-white p15">
                            <div class="row">
                                <input type="hidden" name="regid" value="{{$id}}"/>
                                <div class="col-lg-3  control-label">
                                    <input type="text" name="searchPostcode" value="{{(old("searchPostcode")?old("searchPostcode"):(isset($searchPostcode)?$searchPostcode:''))}}" class="form-control" id="searchPostcode"
                                           placeholder="Search Postcode"/>
                                </div>
                                <div class="col-md-1 control-label">
                                    <a class="btn btn-danger" href="{{ route('regions.show',$id) }}"
                                       title="Reset Search"><i
                                                class="fa fa-refresh" aria-hidden="true"></i> Reset</a>
                                </div>
                                <div class="col-md-2 control-label">
                                    <button type="submit" class="button form-control btn-info pull-right"><i
                                                class="fa fa-search" aria-hidden="true"></i> Search
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <tr>
                            <th>Sr.</th>
                            <th>Postcode</th>
                            <th>Shipping Cost</th>
                            <th>Suburb</th>
                            <th>Comments</th>
                            <th>Action</th>
                        </tr>
                        @if ($RegionPostCodesArr->count() > 0)
                            <?php $i = 0;?>
                            @foreach ($RegionPostCodesArr as $regionPostcode)
                                <tr>
                                    <td>{{ ++$i + ((isset($_GET['page'])?$_GET['page']*$rowsPerPage - $rowsPerPage:0)) }}
                                        .
                                    </td>
                                    <td>{{ $regionPostcode->postcode}}</td>
                                    <td>${{ $regionPostcode->shipping_cost}}</td>
                                    <td>{{ $regionPostcode->suburb}}</td>
                                    <td>{{ $regionPostcode->comments}}</td>
                                    <td>
                                        <a class="btn btn-primary" title="Edit Region"
                                           href="{{ route('postcodes.edit',$regionPostcode->id) }}">
                                            <i class="fa fa-edit"></i> </a>
                                        <a class="btn btn-danger" title="Delete Region" href="javascript:void(0);"
                                           onclick="checkfn({{$regionPostcode->id}});">
                                            <i class="fa fa-trash-o"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7">No postcode found.</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
            <div class="panel-footer clearfix">
                {!! $RegionPostCodesArr->render() !!}
            </div>
        </div>
        <div id="popupbs">
            <div class="modal fade" id="deleteConfirm" role="dialog">
                <div class="modal-dialog">

                    <!-- Modal content-->
                    <div class="modal-content">
                        <form method="POST" action="{{ url('/delete-postcode') }}" class="form-horizontal" role="form">
                            {{ csrf_field() }}
                            <input type="hidden" name="delProdid" value="" id="delProdid"/>
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Delete Postcode</h4>
                            </div>
                            <div class="modal-body">
                                <p>Are you sure you want to delete this postcode and its shipping cost?</p>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-danger">Yes</button>
                                <button type="button" class="btn btn-info" data-dismiss="modal">No</button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>

        </div>
    </section>
@endsection