@extends('layouts.app', [
    'activePage' => 'slider',
])

@section('content')
    <div class="page-header">
        <h3 class="page-title"> Page {{ $title }} </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('slider.index') }}">{{ $title }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detail Data</li>
            </ol>
        </nav>
    </div>

    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <table class="table table-nospace">
                    <tr>
                        <th>Name</th>
                        <td>{{ $slider->name }}</td>
                    </tr>
                    <tr>
                        <th>URL</th>
                        <td><a href="{{ $slider->url }}" target="_blank">{{ $slider->url }}</a></td>
                    </tr>
                    <tr>
                        <th>Periode</th>
                        <td>{{ parse_date($slider->start_date) }} - {{ parse_date($slider->end_date) }}</td>
                    </tr>
                    <tr>
                        <th>Slider</th>
                        <td>
                            <a href="{{ $slider->picture->url }}" target="_blank">
                                <img src="{{ $slider->picture->url }}" class="w-75">
                            </a>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
@endsection
