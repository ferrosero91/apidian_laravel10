@extends('layouts.app')
@section('title', 'Contact')
@section('content')
<customers-index
  :documents-data="{{ json_encode($documents) }}"
  company-idnumber="{{ $company_idnumber }}"
  customer-idnumber="{{ $customer_idnumber }}"
  :allow-public-downloads="{{ env('ALLOW_PUBLIC_DOWNLOAD', true) ? 'true' : 'false' }}"
></customers-index>
@endsection
