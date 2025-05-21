@component('mail::message')
# Hello {{ implode(', ', $clientNames) }}


### New Invoice Created on `{{ implode(', ', $projectNames) }}`  with invoice number #`{{$invoiceNumber}}`

## Please click <a href="{{$invoiceUrl}}" class="btn btn-primary">here</a> to download invoice.

Thanks & Regards,
<br>
{{ config('app.name') }}
@endcomponent
