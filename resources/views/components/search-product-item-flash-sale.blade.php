<div>
  @foreach (\App\Models\ProductItem::with('product')->get() as $index => $product_item)
    <div class="item">
      <x-input-product-item-id :product_item="$product_item" :index="$index" :flash-sale-product-items="$flashSaleProductItems"/>
    </div>
  @endforeach
</div>

@push('js')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    $('.product_item_ids').change(function() {
      let price = $(this).parent().next().children().first();
      let stock = price.parent().next().children().first();
      let index = $(this).data('index');
      if ($(this).is(':checked')) {
        price.attr('name', `product_item_ids[${index}][price]`);
        stock.attr('name', `product_item_ids[${index}][stock]`);
      } else {
        price.attr('name', '');
        stock.attr('name', '');
      }
    });
  });
</script>
@endpush
