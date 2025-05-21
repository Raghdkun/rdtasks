<html>
<head>
    <link href="{{ asset('assets/css/@fortawesome/fontawesome-free/css/all.css') }}" rel="stylesheet" type="text/css">
    <!-- Styles -->
    <link rel="stylesheet" type="text/css" href="{{asset('vendors/css/password-reset.css')}}">
</head>
<body>

<div class="account-verification">
    <table class="account-verification__table">
        <tr>
            <td class="text-center">
                <img class="logo" src="{{!empty(getLogoUrl()) ? getLogoUrl() : asset('assets/img/logo-red-black.png')}}"
                     alt="InfyOm Logo" height="50" width="50">
            </td>
        </tr>
        <tr>
            <td>
                <hr class="divider"/>
            </td>
        </tr>
        <tr>
            <td>
                <p>Dear {{ucfirst($username)}},</p>
                <p>You are receiving this email because we received a password reset request for your account.</p>
            </td>
        </tr>
        <tr>
            <td class="text-center">
                <p>
                    <a href="{{$link}}" class="verification-btn">
                        <strong>Reset Password</strong>
                    </a>
                </p>
            </td>
        </tr>
        <tr>
            <td>
                <p>This password reset link will expire in 60 minutes.</p>
                <p>If you did not request a password reset, no further action is required.</p>
            </td>
        </tr>
        <tr>
            <td>
                <p class="regards-mb-4">Regards,</p>
                <p class="regards-mt-4">InfyTracker</p>
            </td>
        </tr>
        <tr>
            <td>
                <p class="bottom-text">
                    If you’re having trouble clicking the "Reset Password" button, copy and paste the URL below into your web browser: <a href="{{$link}}">{{$link}}</a>
                </p>
            </td>
        </tr>
        <tr>
            <td>
                <hr class="divider"/>
            </td>
        </tr>
    </table>
</div>
</body>
</html>
