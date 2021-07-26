@if(session()->get('message')!=null || $errors->any())
    <span class="alert p-1 @if(session()->get('status')){{'alert-success'}}@else{{'alert-danger'}}@endif">
        {{session()->get('message')!=null?session()->get('message'):$errors->first()}}
    </span>
@endif
