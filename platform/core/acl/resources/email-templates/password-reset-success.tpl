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
                                    <img src="{{ 'check-circle' | icon_url }}" class="bb-va-middle" width="40" height="40" alt="Icon">
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <h1 class="bb-text-center bb-m-0 bb-mt-md">Password Reset Successful</h1>
                </td>
            </tr>
            <tr>
                <td class="bb-content bb-text-center">
                    <p>Hello {{ user_name }},</p>
                    <p>Your password has been successfully reset for your {{ site_name }} account.</p>
                </td>
            </tr>
            <tr>
                <td class="bb-content bb-text-center">
                    <p>If you did not perform this action, please contact our support team immediately.</p>
                </td>
            </tr>
            <tr>
                <td class="bb-content bb-text-center bb-pt-0 bb-pb-xl">
                    <table cellspacing="0" cellpadding="0">
                        <tbody>
                            <tr>
                                <td align="center">
                                    <table cellpadding="0" cellspacing="0" border="0" class="bb-bg-blue bb-rounded bb-w-auto">
                                        <tbody>
                                            <tr>
                                                <td align="center" valign="top" class="lh-1">
                                                    <a href="{{ site_url }}" class="bb-btn bb-bg-blue bb-border-blue">
                                                        <span class="btn-span">Go to Website</span>
                                                    </a>
                                                </td>
                                            </tr>
                                        </tbody>
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
