<company-documents
  :company="{{ json_encode($company) }}"
  :documents-data="{{ json_encode($documents->items()) }}"
  :resolutions-data="{{ json_encode($resolution_credit_notes) }}"
  type="{{ $type ?? 'invoice' }}"
  token="{{ $token_company ?? '' }}"
  @if(isset($documents) && method_exists($documents, 'toArray'))
  :pagination-data="{{ json_encode(['total' => $documents->total(), 'per_page' => $documents->perPage(), 'current_page' => $documents->currentPage()]) }}"
  @endif
></company-documents>
