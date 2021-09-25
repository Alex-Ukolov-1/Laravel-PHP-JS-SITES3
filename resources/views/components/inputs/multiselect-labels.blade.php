<div
  class="multiselect-labels"
  data-name="{{ $name }}"
  data-value="{{ $value }}"
></div>
<script>new MultiSelectLabels('.multiselect-labels[data-name="{{ $name }}"]', {!! $list !!});</script>