@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Welcome</div>
                    @if($user = Auth::user())
                        <script type="text/javascript">
                            window.location = "{{ url('/index.php/dash/adminhome') }}";
                        </script>
                    @endif
                <div class="panel-body">
                    
                 
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
