@extends('layouts.app')
@section('content')
@if (Auth::check()) 
<h1>Welcome, {{$user->name}}!</h1>

<ul>
    <li><a href="/check">All</a></li>
    <li><a href="/check?status=APPROVED">Approved</a></li>
    <li><a href="/check?status=PENDING">Pending</a></li>
    <li><a href="/check?status=REJECTED">Rejected</a></li>

</ul>
@foreach ($vacations as $vacation)
ID: {{$vacation->id}}<br> 
Start Date: {{$vacation->startDate}}<br>
End Date: {{$vacation->endDate}}<br>
Status: {{$vacation->status}}<br>
@if ($vacation->status == "APPROVED" || $vacation->status == "REJECTED")
    Resolved by: {{$vacation->resolved($vacation->resolved_by)->name}} <br>
@endif
<br>

@endforeach 
@else
<h1>You are not logged in</h1> 
@endif

@endsection