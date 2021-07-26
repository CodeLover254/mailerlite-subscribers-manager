@extends('layouts.app')

@section('other-styles')

@endsection

@section('content')
    <div class="container-fluid">
        <div class="row mt-3 d-flex justify-content-center">
            <form action="{{route('store-group')}}" method="post">
                <div class="col-md-4 m-auto">
                    <div class="col-md-12 text-center text-black-50 h2">Create a group</div>
                    <div class="form-group">
                        <label for="group_name">Name</label>
                        <input type="text" name="group_name" class="form-control" required>
                    </div>
                    <div class="form-group mt-2">
                        <input type="submit" value="Create" class="btn btn-success btn-sm">&nbsp;
                        @include('layouts.messages')
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('other-scripts')

@endsection
