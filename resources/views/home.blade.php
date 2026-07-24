@extends('layouts.app')
@section('content')
<companies-index
  :companies-data="{{ json_encode($companies) }}"
  :is-admin="{{ auth()->user() && method_exists(auth()->user(), 'isPlatformAdmin') && auth()->user()->isPlatformAdmin() ? 'true' : 'false' }}"
></companies-index>
@endsection
