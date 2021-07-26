@extends('layouts.app')

@section('other-styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row mt-3">
            <div class="col-md-12 col-sm-12">
                <a href="{{route('show-add-subscriber')}}" class="btn btn-sm btn-primary rounded">
                    <i class="fas fa-plus"></i> Add Subscriber
                </a>
                <span class="message-display"></span>
            </div>
            <div class="col-md-12 col-sm-12 mt-3 p-3">
                <table class="table table-borderless" id="subscribersTable">
                    <thead>
                    <tr>
                        <th>Email</th>
                        <th>Name</th>
                        <th>Country</th>
                        <th>Subscribe Date</th>
                        <th>Subscribe Time</th>
                        <th>Options</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('other-scripts')
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>
    <script src="{{asset('js/app.js')}}"></script>
@endsection
