@extends('layouts.dashboard')

@section('content')

    <header id="topbar" class="alt">
        <div class="topbar-left">
            <ol class="breadcrumb">
                <li class="crumb-active">
                    <a href="{{url('/regions')}}"><span class="glyphicon glyphicon-book"></span> Regions</a>
                </li>
            </ol>
        </div>
    </header>
    <section id="content">
        <div class="panel">
            <div class="panel-heading">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-left">
                        <h2>Regions</h2>
                    </div>
                    <div class="pull-right">
                        <a class="btn btn-success" href="{{url('/regions/create')}}"> Add New Region</a>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                @if ($message = Session::get('success'))
                    <div class="alert alert-success">
                        <p>{{ $message }}</p>
                    </div>
                @endif
                <form method="POST" action="{{url('/search-region')}}" class="form-horizontal" role="form" id="account2">
                    {{ csrf_field() }}
                    @if ($StatesArr->count() > 0)
                        <div class="panel panel-widget calendar-widget calendar-alt" id="p02">
                            <div class="panel-heading ui-sortable-handle">
                        <span class="panel-icon">
                            <i class="fa fa-search"></i>
                        </span>
                                <span class="panel-title"> Search</span>
                            </div>
                            <div class="panel-body bg-white p15">
                                <div class="row">

                                    <div class="col-lg-3  control-label">
                                        <select class='select2-single form-control pull-left' name="searchState" required="required">
                                            <option value="">State</option>
                                            @foreach($StatesArr as $val)
                                                <option value="{{$val->id}}" {{ (old("searchState") == $val->id ? "selected=selected":(isset($searchState) && !empty($searchState) && ($val->id == $searchState)?"selected=selected":"")) }}>{{$val->name}}</option>
                                            @endforeach

                                        </select>
                                    </div>
                                    <div class="col-md-1 control-label">
                                        <a class="btn btn-danger" href="/regions" title="Reset Search"><i
                                                    class="fa fa-refresh" aria-hidden="true"></i> Reset</a>
                                    </div>
                                    <div class="col-md-2 control-label">
                                        <button type="submit" class="button form-control btn-info pull-right"><i
                                                    class="fa fa-search" aria-hidden="true"></i> Search
                                        </button>
                                        <!--<a class="btn btn-info" href="javascript:void(0)"><i class="fa fa-search"></i> Search</a>-->
                                    </div>
                                </div>
                                <div class="clmr"></div>
                                <div class="row">
                                    <div class="col-md-9"></div>


                                </div>

                            </div>
                        </div>
                    @endif
                </form>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <tr>
                            <th>Sr.</th>
                            <th>State</th>
                            <th>Region</th>
                            <th>Action</th>
                        </tr>
                        @if ($RegionsArr->count() > 0)
                            <?php $i = 0;?>
                            @foreach ($RegionsArr as $region)
                                <tr>
                                    <td>{{ ++$i + ((isset($_GET['page'])?$_GET['page']*$rowsPerPage - $rowsPerPage:0)) }}.</td>
                                    <td>{{ $region->name}}</td>
                                    <td>{{ $region->region}}</td>
                                    <td>
                                    <a class="btn btn-info" title="Manage Postcodes" href="{{ route('postcodes.show',$region->id) }}">
                                    <i class="fa fa-eye"></i> </a>
                                        <a class="btn btn-primary" title="Edit Region"
                                           href="{{url('/regions/'.$region->id.'/edit')}}">
                                            <i class="fa fa-edit"></i> </a>
                                        <a class="btn btn-danger" title="Delete Region" href="javascript:void(0);"
                                           onclick="checkfn({{$region->id}});">
                                            <i class="fa fa-trash-o"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7">No Region found.</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
            <div class="panel-footer clearfix">
                {!! $RegionsArr->render() !!}
            </div>
        </div>
        <div id="popupbs">
            <div class="modal fade" id="deleteConfirm" role="dialog">
                <div class="modal-dialog">

                    <!-- Modal content-->
                    <div class="modal-content">
                        <form method="POST" action="{{ url('/delete-region') }}" class="form-horizontal" role="form">
                            {{ csrf_field() }}
                            <input type="hidden" name="delProdid" value="" id="delProdid"/>
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Delete Region</h4>
                            </div>
                            <div class="modal-body">
                                <p>Are you sure you want to delete this region?</p>
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