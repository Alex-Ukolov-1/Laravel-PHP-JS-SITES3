<a
   @if(isset($item))
   href='/contract_from_profitability/{{$item->id}}' class="btn btn-warning mr-1" >
    @else
   class="btn btn-warning mr-1 without_profitability" >
    @endif
    Создать заказ на основе расчета
</a>
