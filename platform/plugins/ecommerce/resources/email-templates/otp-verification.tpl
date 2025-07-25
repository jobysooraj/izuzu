{{ header }}

<div class="bb-main-content">
    <table class="bb-box" cellpadding="0" cellspacing="0">
        <tbody>
            <tr>
                <td class="bb-content bb-pb-0" align="center">
                    <table class="bb-icon bb-icon-lg bb-bg-blue" cellspacing="0" cellpadding="0">
                        <tbody>
                            <tr>
                                <td valign="middle" align="center">
                                    <img src="{{ 'check' | icon_url }}" class="bb-va-middle" width="40" height="40" alt="Icon">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <h1 class="bb-text-center bb-m-0 bb-mt-md">Verify Email</h1>
                </td>
            </tr>
            <tr>
                <td class="bb-content bb-text-center">
                    <p class="h1">Welcome, {{ customer_name }}!</p>
                    <p>Your One-Time Password (OTP) is:</p>
                    <p style="font-size: 24px; font-weight: bold; color: #007bff;">{{ otp_code }}</p>
                    <p>This OTP is valid for 10 minutes.</p>
                    <p>Please enter it on the verification page to confirm your email address.</p>
                </td>
            </tr>
            <tr>
                <td class="bb-content bb-text-center bb-pt-0 bb-pb-xl">
                    <table cellspacing="0" cellpadding="0">
                        <tbody>
                            <tr>
                                <td align="center">
                                    <table cellpadding="0" cellspacing="0" border="0" class="bb-bg-blue bb-rounded bb-w-auto">
                                        <tr>
                                            <td align="center" valign="top" class="lh-1">
                                                <a href="{{ verify_link }}" class="bb-btn bb-bg-blue bb-border-blue">
                                                    <span class="btn-span">Enter OTP Here</span>
                                                </a>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="bb-content bb-text-muted bb-pt-0 bb-text-center">
                    If the button doesn't work, copy and paste this link into your browser: <a href="{{ verify_link }}">{{ verify_link }}</a>
                </td>
            </tr>
        </tbody>
    </table>
</div>

{{ footer }}
