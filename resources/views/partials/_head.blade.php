<link rel="shortcut icon" class="favicon_preview" href="{{ getSingleMedia(imageSession('get'),'favicon',null) }}" />

{{-- Consolidated vendor bundle (committed) --}}
<link rel="stylesheet" href="{{ asset('vendor.css') }}">

<link rel='stylesheet' href="{{ asset('vendor/fullcalendar/core/main.css')}}" />
<link rel='stylesheet' href="{{ asset('vendor/fullcalendar/daygrid/main.css')}}" />
<link rel='stylesheet' href="{{ asset('vendor/fullcalendar/timegrid/main.css')}}" />
<link rel='stylesheet' href="{{ asset('vendor/fullcalendar/list/main.css')}}" />
@if (file_exists(public_path('css/backend-plugin.min.css')))
<link rel="stylesheet" href="{{ asset('css/backend-plugin.min.css') }}">
@endif
<link rel="stylesheet" href="{{ asset('css/backend.css?v=1.0.0')}}">
@if (file_exists(public_path('css/swiper-bundle.min.css')))
<link rel="stylesheet" href="{{ asset('css/swiper-bundle.min.css') }}">
@endif
@if (file_exists(public_path('vendor/@fortawesome/fontawesome-free/css/all.min.css')))
<link rel="stylesheet" href="{{ asset('vendor/@fortawesome/fontawesome-free/css/all.min.css') }}">
@endif
@if (file_exists(public_path('vendor/line-awesome/dist/line-awesome/css/line-awesome.min.css')))
<link rel="stylesheet" href="{{ asset('vendor/line-awesome/dist/line-awesome/css/line-awesome.min.css') }}">
@endif
@if (file_exists(public_path('vendor/remixicon/fonts/remixicon.css')))
<link rel="stylesheet" href="{{ asset('vendor/remixicon/fonts/remixicon.css') }}">
@endif
@if (file_exists(public_path('vendor/confirmJs/jquery-confirm.css')))
<link rel="stylesheet" href="{{ asset('vendor/confirmJs/jquery-confirm.css') }}">
@endif
@if (file_exists(public_path('css/themes/select2.min.css')))
<link rel="stylesheet" href="{{ asset('css/themes/select2.min.css') }}">
@endif
@if (file_exists(public_path('vendor/select2/css/select2.min.css')))
<link rel="stylesheet" href="{{ asset('vendor/select2/css/select2.min.css') }}">
@endif
@if (file_exists(public_path('vendor/magnific-popup/magnific-popup.css')))
<link rel="stylesheet" href="{{ asset('vendor/magnific-popup/magnific-popup.css') }}">
@endif
@if (file_exists(public_path('css/sweetalert2.min.css')))
<link rel="stylesheet" href="{{ asset('css/sweetalert2.min.css') }}">
@endif
@if (file_exists(public_path('js/sweetalert2.min.js')))
<script src="{{ asset('js/sweetalert2.min.js') }}"></script>
@endif
 
<!-- @if(session()->get('dir') == 'rtl')
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
@endif -->