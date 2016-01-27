<?php
/**
 * Mageplace Freshdesk extension
 *
 * @category    Mageplace_Freshdesk
 * @package     Mageplace_Freshdesk
 * @copyright   Copyright (c) 2014 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */

/**
 * Class Mageplace_Freshdesk_Helper_Const
 */
class Mageplace_Freshdesk_Helper_Const
{
    const NAME = 'freshdesk';

    const XML_ACCOUNT_DOMAIN   = 'freshdesk/account/domain';
    const XML_ACCOUNT_EMAIL    = 'freshdesk/account/email';
    const XML_ACCOUNT_API_KEY  = 'freshdesk/account/api_key';
    const XML_ACCOUNT_PASSWORD = 'freshdesk/account/password';

    const XML_SSO_ENABLED    = 'freshdesk/sso/enable';
    const XML_SSO_SECRET     = 'freshdesk/sso/secret';
    const XML_SSO_LOGIN_URL  = 'freshdesk/sso/login_url';
    const XML_SSO_LOGOUT_URL = 'freshdesk/sso/logout_url';

    const XML_ORDERS_ORDER_ID = 'freshdesk/orders/order_id';

    const XML_CHANNELS_CONTACT_US_ENABLED      = 'freshdesk/channels/enable_contact_us';
    const XML_CHANNELS_FEEDBACK_WIDGET_ENABLED = 'freshdesk/channels/enable_feedback_widget';
    const XML_CHANNELS_FEEDBACK_WIDGET_CODE    = 'freshdesk/channels/feedback_widget';
    const XML_CHANNELS_ENABLE_SUPPORT_LINK     = 'freshdesk/channels/enable_support_link';

    const XML_CUSTOMER_VIEW_ENABLE_CUSTOMER_VIEW = 'freshdesk/customer_view/enable_customer_view';
    const XML_CUSTOMER_VIEW_ENABLE_TICKET_TAB    = 'freshdesk/customer_view/enable_ticket_tab';
    const XML_CUSTOMER_VIEW_ENABLE_RECENT_TICKET = 'freshdesk/customer_view/enable_recent_ticket';

    const XML_TICKETS_PRIORITY     = 'freshdesk/tickets/priority';
    const XML_TICKETS_STATUS       = 'freshdesk/tickets/status';
    const XML_TICKETS_STATUS_CLOSE = 'freshdesk/tickets/status_close';

    const REGISTER_CURRENT_FRESHDESK_TICKET = 'current_freshdesk_ticket';
    const REGISTER_CURRENT_FRESHDESK_USER   = 'current_freshdesk_user';
}