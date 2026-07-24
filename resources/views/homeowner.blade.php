@extends('layouts.app', ['is_owner' => true])
@section('title', 'Contact')
@section('content')
<owner-documents
  title="Documentos enviados por todas las empresas."
  :documents-data="{{ json_encode($documents->items()) }}"
  :allow-public-downloads="{{ env('ALLOW_PUBLIC_DOWNLOAD', true) ? 'true' : 'false' }}"
  search-url="{{ url('/okownersearch') }}"
  @if(method_exists($documents, 'toArray'))
  :pagination-data="{{ json_encode(['total' => $documents->total(), 'per_page' => $documents->perPage(), 'current_page' => $documents->currentPage()]) }}"
  @endif
></owner-documents>
@endsection
