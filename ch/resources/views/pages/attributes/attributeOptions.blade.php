@extends('layouts.dashboard')

@section('content')

<header id="topbar" class="alt">
    <div class="topbar-left">
        <ol class="breadcrumb">
            <li class="crumb-active">
                <a href="{{url('/pro_attributes')}}"><span class="glyphicon glyphicon-list"></span> Attributes</a>
           </li>
            <li class="crumb-active">Attributes Options</li>
        </ol>
    </div>
</header>
<section id="content">
    <div class="panel">
        <div class="panel-heading">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <h2>Attributes Options</h2>
                </div>
                <div class="pull-right">
                    <a class="btn btn-success" href="/option_add/{{$idAttribute}}"><i class="fa fa-plus"></i> Add New Option</a>
                </div>
            </div>
        </div>
        <div class="panel-body">
            @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
            @endif
            <div class="table-responsive"> 
                <table class="table table-striped">
                    <tr>
                        <th width="5%">Sr.</th>
                        <th width="65%">Option Name</th>
                        <th width="35%">Action</th>
                    </tr>
                    @if ($attributesOptions->count() > 0)
                        @foreach ($attributesOptions as $optionsData)
                            <tr>
                                <td>{{ ++$i }}.</td>
                                <td>{{ $optionsData->value}}</td>
                                <td>
                                    <a class="btn btn-primary" href="/option_edit/{{$idAttribute}}/{{$optionsData->id}}" title="Edit Option"><i class="fa fa-pencil"></i> Edit</a>
                                    <a class="btn btn-danger" href="javascript:void(0);" title="Delete Option" onclick="delAttributeOption({{$idAttribute}},{{$optionsData->id}});"><i class="fa fa-trash-o"></i> Delete</a>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="3">No attribute option found.</td>
                        </tr>
                    @endif
                </table>
            </div>
        </div>
        <div class="panel-footer clearfix">
            {!! $attributesOptions->render() !!}
        </div>
    </div>
    <div id="popupbs">
        <div class="modal fade" id="deleteOptionConfirm" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <form method="POST" action="{{ url('/delete_attribute_option') }}" class="form-horizontal" role="form" >
                        {{ csrf_field() }}
                        <input type="hidden" name="attributeId" value="" id="attributeId" />
                        <input type="hidden" name="OptionId" value="" id="OptionId" />
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Delete Attribute Option</h4>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to delete this attribute option?</p>
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