<events-table
  :documents-data="{{ json_encode($documents->items()) }}"
  company-idnumber="{{ $company_idnumber }}"
  :is-company-route="{{ Request::is('company*') || Request::is('companies*') ? 'true' : 'false' }}"
  search-url="{{ url('/oksellersradiansearch/' . $company_idnumber) }}"
  @if(method_exists($documents, 'toArray'))
  :pagination-data="{{ json_encode(['total' => $documents->total(), 'per_page' => $documents->perPage(), 'current_page' => $documents->currentPage()]) }}"
  @endif
></events-table>
