@extends('layouts.app', ['is_seller' => true])
@section('title', 'Contact')
@section('content')
<seller-documents
  title="Documentos emitidos por la empresa - {{ $company_idnumber }}."
  :documents-data="{{ json_encode($documents->items()) }}"
  company-idnumber="{{ $company_idnumber }}"
  :allow-public-downloads="{{ env('ALLOW_PUBLIC_DOWNLOAD', true) ? 'true' : 'false' }}"
  search-url="{{ url('/oksellerssearch/' . $company_idnumber) }}"
  @if(method_exists($documents, 'toArray'))
  :pagination-data="{{ json_encode(['total' => $documents->total(), 'per_page' => $documents->perPage(), 'current_page' => $documents->currentPage()]) }}"
  @endif
></seller-documents>
@endsection
