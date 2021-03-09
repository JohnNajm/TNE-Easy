@extends('layouts.app')
@section('content')
<h1>This is the Apply Page</h1>

@if (Auth::check()) 
    <form method="post" action="{{ route('vacation.store')}}">
        {{ csrf_field() }}
        <div class="form-group">
            <label for="startDate">Start Date:</label>
            <input type="date" name="startDate" required/>
        </div>
        <div class="form-group">
            <label for="endDate">End Date:</label>
            <input type="date" name="endDate" required/>
        </div>
        <input type="hidden" name="remainder" value= {{json_encode($remainder)}}>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>

    <hr>
    <label for="status">Vacation status:</label> <br>
    @php
    if($remainder){

        //echo json_encode($remainder);
        foreach($remainder as $key => $value) {  
            echo "Year: " . $key . "<br>" . 
            "Days Remaining: " . $remainder[$key] . "<br><br>";
        }
    }else{
        echo "You still have all your vacation days remaining";
    }    
    @endphp
    <hr>

    @else
    <h2>You are not logged in, please log in to apply for a vacation</h2>
@endif

@endsection
