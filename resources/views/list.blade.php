@extends('layouts.app')
@section('content')
@if (Auth::check()) 
<h1>Welcome, {{auth()->user()->name}}!</h1>

<ul>
    <li><a href="all">All</a></li>
    <li><a href="all?status=APPROVED">Approved</a></li>
    <li><a href="all?status=PENDING">Pending</a></li>
    <li><a href="all?status=REJECTED">Rejected</a></li>

</ul>
@foreach ($vacations as $vacation)
ID: {{$vacation->id}}<br>
Employee Name: <a href="/user/{{$vacation->user->id}}">{{$vacation->user->name}}</a><br>
Start Date: {{$vacation->startDate}}<br>
End Date: {{$vacation->endDate}}<br>
Status: {{$vacation->status}}<br>
@if ($vacation->status == "APPROVED" || $vacation->status == "REJECTED")
    Resolved by: {{$vacation->resolved($vacation->resolved_by)->name}} <br>
@else

<form method="post" action="request/{{$vacation->id}}">
    {{ csrf_field() }}
    {{ method_field('PATCH') }}
    <input type="hidden" name="status" value= "APPROVE">
    <button type="submit" class="btn btn-primary btn-success">APPROVE</button>
</form>
<form method="post" action="request/{{$vacation->id}}">
    {{ csrf_field() }}
    {{ method_field('PATCH') }}
    <input type="hidden" name="status" value= "REJECT">
    <button type="submit" class="btn btn-primary btn-danger">REJECT</button>
</form>
@endif
<br>

@endforeach 
@else
<h1>You are not logged in</h1> 
@endif

@endsection