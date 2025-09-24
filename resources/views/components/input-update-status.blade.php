@php use App\Constants\StatusConst;use App\Models\PaymentMethod; @endphp
@props([
  'order' => $order
])

@if ($order->status == StatusConst::ON_PROCESS || ($order->paymentMethod?->vendor === PaymentMethod::MANUAL && $order->status == StatusConst::PENDING))
  <form action="{{ route('order.status') }}" method="post" class="mt-3">
    @csrf
    <input type="hidden" name="order_id" value="{{ $order->id }}">
    <button type="button" name="status" class="btn btn-sm btn-success"
            data-bs-toggle="modal" data-bs-target="#form-{{ $order->id }}"
            onclick="$('#status-{{ $order->id }}').val('{{ StatusConst::SUCCESS }}')">Success
    </button>
    <button type="button" name="status" class="btn btn-sm btn-danger"
            data-bs-toggle="modal" data-bs-target="#form-{{ $order->id }}"
            onclick="$('#status-{{ $order->id }}').val('{{ StatusConst::FAILED }}');return confirm('Are You Sure?');">
      Failed
    </button>

    <input type="hidden" id="status-{{ $order->id }}" name="status">

    <div class="modal fade" id="form-{{ $order->id }}" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="infoModalLabel">Update Order</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"
                    aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label class="form-label">Serial Number</label>
              <input type="text" class="form-control" name="serial_number"
                     placeholder="Enter serial number from provider or voucher code">
            </div>
            <div class="form-group">
              <label class="form-label">Note</label>
              <input type="text" class="form-control" name="note" placeholder="Enter order note">
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Submit</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

  </form>
@endif
