@extends('layouts.dashboard')

@section('content')

<header id="topbar" class="alt">
    <div class="topbar-left">
        <ol class="breadcrumb">
            <li class="crumb-active">
                <a href="{{url('/pro_attributes')}}"><span class="glyphicon glyphicon-list"></span> Attributes</a>
            </li>
        </ol>
    </div>
</header>
<section id="content">
    <div class="panel">
        <div class="panel-heading">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <h2>Attributes</h2>
                </div>
                <div class="pull-right">
                    <a class="btn btn-success" href="{{url('/add_attribute')}}"><i class="fa fa-plus"></i> Add New Attribute</a>
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
                        <th width="65%">Attribute</th>
                        <th width="35%">Action</th>
                    </tr>
                    @if ($attributesAry->count() > 0)
                        @foreach ($attributesAry as $attributeData)
                            <tr>
                                <td>{{ ++$i }}.</td>
                                <td>{{ $attributeData->attrib_name}}</td>
                                <td>
                                    <a class="btn btn-info" href="/attribute_options/{{$attributeData->id}}" title="Attribute Options"><i class="fa fa-list"></i> Options</a>
                                    <a class="btn btn-primary" href="{{ route('attributes.edit',$attributeData->id) }}" title="Edit Attribute"><i class="fa fa-pencil"></i> Edit</a>
                                    <a class="btn btn-danger" href="javascript:void(0);" title="Delete Attribute" onclick="delProductAttribute({{$attributeData->id}});"><i class="fa fa-trash-o"></i> Delete</a>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="3">No Attribute found.</td>
                        </tr>
                    @endif
                </table>
            </div>
        </div>
        <div class="panel-footer clearfix">
            {!! $attributesAry->render() !!}
        </div>
    </div>
    <div id="popupbs">
        <div class="modal fade" id="deleteAttributeConfirm" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <form method="POST" action="{{ url('/delete_attribute') }}" class="form-horizontal" role="form" >
                        {{ csrf_field() }}
                        <input type="hidden" name="delAttributeId" value="" id="delAttributeId" />
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Delete Attribute</h4>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to delete this attribute?</p>
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