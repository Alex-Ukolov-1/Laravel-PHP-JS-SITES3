<div
  class="file_table"
  data-name="{{ $name }}"
  data-value="{{ $value }}"
></div>
<script>new FileTable('.file_table[data-name="{{ $name }}"]', {!! $list !!});</script>
