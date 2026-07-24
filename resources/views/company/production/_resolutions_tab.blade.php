<resolutions-tab
  :company="{{ json_encode($company) }}"
  :resolutions-hab-data="{{ json_encode($resolutionsHab ?? collect()) }}"
  :resolutions-prod-data="{{ json_encode($resolutionsProd ?? collect()) }}"
  :type-documents="{{ json_encode($resolutionTypeDocuments ?? collect()) }}"
  type="{{ $type }}"
></resolutions-tab>
