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
                                    <img src="{{ 'lock' | icon_url }}" class="bb-va-middle" width="40" height="40" alt="OTP Icon">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <h1 class="bb-text-center bb-m-0 bb-mt-md">OTP Verification</h1>
                </td>
            </tr>

            <tr>
                <td class="bb-content bb-text-center">
                    <p class="h1">Hello {{ customer_name }},</p>
                    <p>Your one-time password (OTP) is:</p>
                    <p class="h1" style="font-size: 32px; font-weight: bold; color: #2f855a;">
                        {{ customer_otp }}
                    </p>
                    <p>This code will expire in <strong>5 minutes</strong>.</p>
                    <p>If you did not request this OTP, you can safely ignore this message.</p>
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
                                                <a href="{{ site_url }}/login" class="bb-btn bb-bg-blue bb-border-blue">
                                                    <span class="btn-span">Log In</span>
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
        </tbody>
    </table>
</div>

{{ footer }}
