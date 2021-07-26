@extends('layouts.app')

@section('other-styles')

@endsection

@section('content')
    <div class="container-fluid">
        <div class="row mt-3 d-flex justify-content-center">
            <form action="{{route('update-subscriber',['email'=>$response['data']->email])}}" method="post">
                <div class="col-md-4 m-auto">
                    <div class="col-md-12 text-center text-black-50 h2">Add a subscriber</div>
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control" value="{{$response['data']->name}}" required>
                    </div>
                    <div class="form-group">
                        <label>Country</label>
                        <input type="text" name="country" class="form-control"
                               value="{{array_values(array_filter($response['data']->fields,function($field)
                                        {return $field->key=='country';}))[0]->value}}" required>
                    </div>
                    <div class="form-group mt-2">
                        <input type="submit" value="Update" class="btn btn-success btn-sm">&nbsp;
                        @include('layouts.messages')
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('other-scripts')

@endsection
