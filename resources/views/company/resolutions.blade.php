@extends('layouts.app')

@section('content')
<company-resolutions
  :company="{{ json_encode($company) }}"
  :resolutions-data="{{ json_encode($resolutions->items()) }}"
  :type-documents="{{ json_encode($typeDocuments) }}"
  @if(method_exists($resolutions, 'toArray'))
  :pagination-data="{{ json_encode(['total' => $resolutions->total(), 'per_page' => $resolutions->perPage(), 'current_page' => $resolutions->currentPage()]) }}"
  @endif
></company-resolutions>
@endsection
