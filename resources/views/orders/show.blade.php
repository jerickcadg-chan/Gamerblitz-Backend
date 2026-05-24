@php use App\Constants\StatusConst; @endphp
@extends('layouts.app', [
    'activePage' => 'order',
])

@section('content')
  <div class="page-header">
    <h3 class="page-title"> Page {{ $title }} </h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('order.index') }}">{{ $title }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">Detail Data</li>
      </ol>
    </nav>
  </div>

  <div class="row">
    <div class="col-lg-7">
      <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
          <div class="card-body table-responsive">
            <table class="table table-nospace table-hover">
              <tr>
                <th>Invoice Code</th>
                <td>{{ $order->code }}</td>
              </tr>
              @if($order->partner_ref)
                <tr>
                  <th>Partner Ref</th>
                  <td>{{ $order->partner_ref }}</td>
                </tr>
              @endif
              <tr>
                <th>Created At</th>
                <td>{{ parse_date_time_full($order->created_at) }}</td>
              </tr>
              <tr>
                <th>Order</th>
                <td>
                  <p>{{ $order->productItem->name }} ({{ $order->qty }} Item)</p>
                  <span class="text-muted">{{ $order->productItem->code }} - {{ $order->productItem->product->name }} ({{ ucfirst($order->productItem->product->productCategory->name) }})</span>
                </td>
              </tr>
              <tr>
                <th>Customer Account</th>
                <td>{!! $order->cust_account_format !!}</td>
              </tr>
              <tr>
                <th>Buyer</th>
                <td>
                  @if ($order->user)
                    <p><a href="{{ route('user.show', $order->user->id) }}" target="_blank">{{ $order->user->name }}</a>
                    </p>
                  @endif
                  <span class="text-muted">Email = {{ $order->cust_email }}</span><br>
                  <span class="text-muted">Whatsapp = <a
                      href="https://web.whatsapp.com/send?phone={{ $order->cust_phone_number }}&text=Hai Kak"
                      target="_blank">{{ $order->cust_phone_number }}</a></span>
                </td>
              </tr>
              <tr>
                <th>Status Order</th>
                <td>{!! $order->order_status_raw !!}</td>
              </tr>
              <tr>
                <th>Price</th>
                <td>{{ currency_format($order->price) }}</td>
              </tr>
              <tr>
                <th>Quantity</th>
                <td>{{ $order->qty }}</td>
              </tr>
              <tr>
                <th>Discount</th>
                <td>
                  @php
                    // Calculate actual discount from price difference if discount_price is 0
                    $actualDiscount = $order->discount_price > 0 
                        ? $order->discount_price 
                        : round(($order->price * $order->qty) - $order->turnover - $order->admin_fee, 2);
                    $actualDiscount = max(0, $actualDiscount); // Ensure non-negative
                    
                    // Get streamer code - try relationship first, then direct query
                    $streamerCode = null;
                    if ($order->streamer_id) {
                        if ($order->streamer) {
                            $streamerCode = $order->streamer->code;
                        } else {
                            // Query directly without soft delete scope
                            $streamerRecord = \DB::table('streamers')->where('id', $order->streamer_id)->first();
                            $streamerCode = $streamerRecord?->code;
                        }
                    }
                    
                    // Get voucher code - try relationship first, then direct query
                    $voucherCode = null;
                    if ($order->discount_id) {
                        if ($order->discount) {
                            $voucherCode = $order->discount->code;
                        } else {
                            // Query directly without soft delete scope
                            $discountRecord = \DB::table('discounts')->where('id', $order->discount_id)->first();
                            $voucherCode = $discountRecord?->code;
                        }
                    }
                  @endphp
                  {{ currency_format($actualDiscount) }}
                  @if($order->streamer_id)
                    @if($streamerCode)
                      <span class="badge bg-info ms-2">Streamercode ({{ $streamerCode }})</span>
                    @else
                      <span class="badge bg-secondary ms-2">Streamercode (ID: {{ $order->streamer_id }})</span>
                    @endif
                  @elseif($order->discount_id)
                    @if($voucherCode)
                      <span class="badge bg-primary ms-2">Voucher ({{ $voucherCode }})</span>
                    @else
                      <span class="badge bg-secondary ms-2">Voucher (ID: {{ $order->discount_id }})</span>
                    @endif
                  @endif
                </td>
              </tr>
              <tr>
                <th>Turnover</th>
                <td>{{ currency_format($order->turnover) }}</td>
              </tr>
              <tr>
                <th>Admin Fee</th>
                <td>{{ currency_format($order->admin_fee) }}</td>
              </tr>
              <tr>
                <th>Total Price</th>
                <td>{{ currency_format($order->total_price) }}</td>
              </tr>
              <tr>
                <th>Capital</th>
                <td>{{ currency_format($order->capital) }}</td>
              </tr>
              <tr>
                <th>Gross Profit</th>
                <td>{{ currency_format($order->total_income) }}</td>
              </tr>
              {{-- Gateway Fee, VAT, Net Profit --}}
              <tr class="table-warning">
                <th>Gateway Fee</th>
                <td class="text-danger">-{{ currency_format($profitBreakdown['gateway_fee']) }}</td>
              </tr>
              <tr class="table-warning">
                <th>VAT on Fee (12%)</th>
                <td class="text-danger">-{{ currency_format($profitBreakdown['vat_on_fee']) }}</td>
              </tr>
              <tr class="table-warning">
                <th>
                  Affiliate Bonus
                  @if($order->affiliate)
                    ({{ $order->affiliate->code }} - {{ $order->affiliate->user?->name ?? '-' }})
                  @endif
                </th>
                <td class="text-danger">-{{ currency_format($profitBreakdown['affiliate_bonus'] ?? 0) }}</td>
              </tr>
              <tr class="table-success">
                <th><strong>Net Profit</strong></th>
                <td><strong class="text-success">{{ currency_format($profitBreakdown['net_profit']) }}</strong></td>
              </tr>
              {{-- END --}}
              <tr>
                <th>Payment Method</th>
                <td>{{ strtoupper($order->paymentMethod?->name) }}</td>
              </tr>
              <tr>
                <th>Platform</th>
                <td>{{ $order->platform }}</td>
              </tr>
              <tr>
                <th>Provider</th>
                <td>
                  {{ $order->provider == 'whitelabel' ? env('PROVIDER_WHITELABEL') : $order->provider }}
                </td>
              </tr>
              <tr>
                <th>Serial Number</th>
                <td>{{ $order->serial_number ?? "-" }}</td>
              </tr>
              @if($order->note)
                <tr>
                  <th>Note</th>
                  <td>{{ $order->note }}</td>
                </tr>
              @endif
              <tr>
                <th>External Payment ID</th>
                <td>{{ $order->payment_id }}</td>
              </tr>
              <tr>
                <th>Provider Trx Ref</th>
                <td>{{ $order->provider_ref }}</td>
              </tr>
              <tr>
                <th>Additional Information</th>
                <td>{!! $order->additional_information_html !!}</td>
              </tr>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-5">
      <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
          <div class="card-header">
            <b>Status Histories</b>
          </div>
          <div class="p-3 table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
              <tr>
                <th>Date</th>
                <th>Status</th>
                <th>Note</th>
              </tr>
              </thead>
              <tbody>
              @forelse ($order->histories as $history)
                <tr>
                  <td>{{ parse_date_time($history->created_at) }}</td>
                  <td>{{ $history->status }}</td>
                  <td>
                    @if($history->note)
                      <button type="button" class="btn btn-primary btn-xs"
                              data-bs-toggle="modal" data-bs-target="#history-{{ $history->id }}">
                        <i class="mdi mdi-information"></i>
                      </button>
                      <div class="modal fade" id="history-{{ $history->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title" id="infoModalLabel">Notes</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                              <pre>
                                @php
                                  $note = $history->note ?? '';
                                  $decoded = json_decode($note, true);
                                @endphp

                                @if (json_last_error() === JSON_ERROR_NONE && is_array($decoded))
                                  <pre style="background: #f8f9fa; padding: 15px; border-radius: 5px; font-size: 13px; overflow-x: auto;">{{ json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                @else
                                  <p>{{ $note }}</p>
                                @endif
                              </pre>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                          </div>
                        </div>
                      </div>
                    @else
                      -
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="100%">No Data</td>
                </tr>
              @endforelse
              </tbody>
            </table>
            @can('Process Order')
            @if ($order->status == StatusConst::SUCCESS)
              <form action="{{ route('order.status') }}" onsubmit="return confirm('Are you sure?')" method="post" class="mt-3">
                @csrf
                <input type="hidden" name="order_id" value="{{ $order->id }}">
                <button type="submit" name="status" class="btn btn-sm btn-primary"
                        onclick="$('#status').val('{{ StatusConst::REFUNDED }}')">Refund
                </button>
                <input type="hidden" id="status" name="status">
              </form>
            @endif
            @endcan
            @can('Process Order')
            <x-input-update-status :order="$order" />
            @endcan
          </div>
        </div>
      </div>
      @if($mutations->count() > 0)
        <div class="col-lg-12 grid-margin stretch-card mt-3">
          <div class="card">
            <div class="card-header">
              <b>Balance Mutations</b>
            </div>
            <div class="p-3 table-responsive">
              <table class="table table-bordered table-hover">
                <thead>
                <tr>
                  <th>Date</th>
                  <th>Amount</th>
                  <th>Before</th>
                  <th>After</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($mutations as $mutation)
                  <tr>
                    <td>{{ parse_date_time($mutation->created_at) }}</td>
                    <td>{{ currency_format($mutation->amount) }}</td>
                    <td>{{ currency_format($mutation->latest_balance - $mutation->amount) }}</td>
                    <td>{{ currency_format($mutation->latest_balance) }}</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="100%">No Data</td>
                  </tr>
                @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      @endif
    </div>
  </div>
@endsection