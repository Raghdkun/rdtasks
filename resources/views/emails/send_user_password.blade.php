@component('mail::message')
    # Hello {{ $name }},

    Thank you for your interest in our portal.
    Your Login Password: {{ $password_confirmation }}

    For help, please contact us at:
    {{ getSettingValue('company_email') }}
    {{ getSettingValue('company_phone') }}

    Thanks & Regards,
    {{ config('app.name') }}
@endcomponent
