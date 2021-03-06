@extends('main')

@section('main')
<div class="row">
    <div class="col-md-6 offset-md-3">
        <br/>
        <h1>Successful Approval</h1>
        <div class="alert alert-success">
            <div class="row"><div class="col-12">
            Contact records approved.
            </div></div>
        </div><p>
            You have approved the records. They will now be combined with your 
            existing list of contacts. Please return in several minutes after the 
            system has had time to process your request.
        </p>
        <p><strong>If this is your first contact upload...</strong> close your browser and return <strong>after</strong>
            you get the email noting that processing is complete.</p>
        
    </div>
</div>
@endsection