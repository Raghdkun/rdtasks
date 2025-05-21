@component('mail::message')
# Hello {{ $userName }}

<a href="{{$projectUrl}}" class="btn btn-primary">Check your Project here</a>

    Thanks & Regards,
<br>
    {{ config('app.name') }}
@endcomponent
