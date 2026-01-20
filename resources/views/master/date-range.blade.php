<div class="{{ isset($col) ? $col : 'col-md-3' }} mb-2" id="dates_form">
  @if (isset($label))
    <label for="dates">{{ $label }}</label>
  @endif
  <input id="dates" type="text" class="form-control" name="dates" value="{{ old('dates') ?? request('dates') }}"
         placeholder="{{ isset($placeholder) ? $placeholder : 'Date Range Filter' }}"
         autocomplete="off" {{ isset($required) ? 'required' : null }}>
</div>
@push('assets')
  <link href="{{ asset('vendors/daterangepicker/daterangepicker.css') }}" rel="stylesheet">
@endpush

@push('js')
  <script src="{{ asset('vendors/moment/moment.js') }}"></script>
  <script src="{{ asset('vendors/daterangepicker/daterangepicker.js') }}"></script>
  <script>
    $('input[name="dates"]').daterangepicker({
      @isset($timePicker)
      timePicker: true,
      locale: {
        format: 'M/DD hh:mm A'
      },
      @endisset
      autoUpdateInput: false,
      "alwaysShowCalendars": true,
      dateLimit: {
        'months': 1,
        'days': -1
      }
    });

    $('input[name="dates"]').on('apply.daterangepicker', function (ev, picker) {
      @isset($timePicker)
      $(this).val(picker.startDate.format('MM/DD/YYYY HH:mm:ss') + ' - ' + picker.endDate.format('MM/DD/YYYY HH:mm:ss'));
      @else
      $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
      @endisset
    });

    $('input[name="dates"]').on('cancel.daterangepicker', function (ev, picker) {
      $(this).val('');
    });

    $('.open_picker').show();

  </script>
@endpush
