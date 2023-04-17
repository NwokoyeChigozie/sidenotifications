<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get("/status-check", "Status\StatusCheckController@statusCheck");

$router->get('/docs', function () {
    include __DIR__ . '/../../swagger-ui.html';
});

$router->group(['namespace' => '\Rap2hpoutre\LaravelLogViewer'], function () use ($router) {
    $router->get('logs', 'LogViewerController@index');
});

$router->group(['middleware' => ['api']], function ($router) {
    $router->post('/list', 'Notifications\NotificationsController@list');
    $router->post('/read', 'Notifications\NotificationsController@read');
    $router->post('/all', 'Notifications\NotificationsController@all');

    // Email routes
    $router->post('/email/send/email_verification', 'Notifications\EmailsNotificationsController@sendEmailVerificationMail');
    $router->post('/email/send/email_verification2', 'Notifications\EmailsNotificationsController@sendEmailVerificationMail2');

    $router->post('/email/send/email_verified', 'Notifications\EmailsNotificationsController@sendEmailVerifiedMail');

    $router->post('/email/send/welcome', 'Notifications\EmailsNotificationsController@sendWelcomeMail');
    $router->post('/email/send/welcome_password', 'Notifications\EmailsNotificationsController@sendWelcomePasswordMail');

    // Sms Routes
    $router->post('/phone/send/welcome', 'Notifications\SmsNotificationsController@sendWelcomeSms');
    $router->post('/phone/send/sms_verification', 'Notifications\SmsNotificationsController@sendPhoneVerificationSms');
    $router->post('/phone/send/sms_to_phone', 'Notifications\SmsNotificationsController@sendSmsToPhone');

    $router->post('/phone/send/sms_verified', 'Notifications\SmsNotificationsController@sendPhoneNumberVerified');

    // Reset Password routes
    $router->post('/email/send/reset_password', 'Notifications\EmailsNotificationsController@sendResetPasswordEmail');

    $router->post('/email/send/reset_password/done', 'Notifications\EmailsNotificationsController@sendResetPasswordEmailDone');

    $router->post('/phone/send/reset_password', 'Notifications\SmsNotificationsController@sendResetPassword');

    $router->post('/phone/send/reset_password/done', 'Notifications\SmsNotificationsController@sendResetPasswordDone');

    // Reminders Route
    $router->post('/email/send/reminder/complete_profile', 'Notifications\ReminderNotificationsController@sendCompleteProfileReminderEmail');
    $router->post('/email/send/reminder/verify_email', 'Notifications\ReminderNotificationsController@sendVerifyEmailReminderEmail');

    $router->post('/email/send/reminder/verify_phone', 'Notifications\ReminderNotificationsController@sendVerifyPhoneReminderSms');
    $router->post('/email/send/reminder/transaction_send_draft', 'Notifications\ReminderNotificationsController@sendTransactionDraftReminderEmail');

    $router->post('/email/send/reminder/transaction_send_payment', 'Notifications\ReminderNotificationsController@sendTransactionPaymentReminderEmail');

    $router->post('/email/send/reminder/transaction_send_make_amendment', 'Notifications\ReminderNotificationsController@sendTransactionMakeAmendmentReminderEmail');

    $router->post('/email/send/reminder/transaction_confirm_delivery', 'Notifications\ReminderNotificationsController@sendTransactionConfirmDelivery');

    $router->post('/email/send/reminder/transaction_confirm_satisfactory_of_delivery', 'Notifications\ReminderNotificationsController@sendTransactionConfirmSatisfactoryOfDelivery');

    $router->post('/email/send/reminder/transaction_accept_delivery', 'Notifications\ReminderNotificationsController@sendTransactionAcceptDelivery');

    $router->post('/email/send/reminder/transaction_accept', 'Notifications\ReminderNotificationsController@sendTransactionAcceptTransaction');

    $router->post('/email/send/reminder/transaction_deliver', 'Notifications\ReminderNotificationsController@sendTransactionDeliverTransaction');

    $router->post('/email/send/reminder/upload_bank_details', 'Notifications\ReminderNotificationsController@sendUploadBankDetails');

    $router->post('/email/send/reminder/upload_business_documents', 'Notifications\ReminderNotificationsController@sendUploadBusinessDetails');

    $router->post('/email/send/reminder/upload_bvn_details', 'Notifications\ReminderNotificationsController@sendUploadBVNDetails');

    $router->post('/email/send/reminder/upload_idcard', 'Notifications\ReminderNotificationsController@sendUploadIdCard');

    // Transaction Notifications
    $router->post('/email/send/new_transaction', 'Notifications\NewTransactionNotificationController@sendNewTransactionNotification');
    $router->post('/email/send/invite_to_view_transaction', 'Notifications\TransactionInviteController@sendInviteToViewTransaction');
    $router->post('/email/send/invite_to_manage_transaction', 'Notifications\TransactionInviteController@sendInviteToManageTransaction');

    $router->post('/email/send/transaction_accepted', 'Notifications\TransactionAcceptedNotificationController@sendTransactionAcceptedNotification');

    $router->post('/email/send/transaction_rejected', 'Notifications\TransactionRejectedNotificationController@sendTransactionRejectedNotification');

    $router->post('/email/send/transaction_paid', 'Notifications\TransactionPaidNotificationController@sendTransactionPaidNotification');

    $router->post('/email/send/transaction_confirm_delivery', 'Notifications\TransactionConfirmDeliveryController@sendTransactionConfirmDeliveryNotification');

    $router->post('/email/send/transaction_delivered', 'Notifications\TransactionDeliveredNotificationController@sendTransactionDeliveredNotification');
    $router->post('/email/send/milestone_transaction_delivered', 'Notifications\MilestoneNotificationController@sendMarkedAsDone');
    $router->post('/email/send/milestone_transaction_completed', 'Notifications\MilestoneNotificationController@sendMilestoneCompleted');

    $router->post('/email/send/transaction_delivered_accepted', 'Notifications\TransactionDeliveredAndAcceptedNotificationController@sendNotification');

    $router->post('/email/send/transaction_delivered_rejected', 'Notifications\TransactionDeliveredAndRejectedNotificationController@sendNotification');

    $router->post('/email/send/escrow_disbursed_seller', 'Notifications\SellerDisbursementNotificationController@sendDisbursementNotification');

    $router->post('/email/send/escrow_disbursed_buyer', 'Notifications\BuyerDisbursementNotificationController@sendDisbursementNotification');

    $router->post('/email/send/escrow_disbursed_failed', 'Notifications\BuyerDisbursementNotificationController@sendDisbursementFailedNotification');

    $router->post('/email/send/transaction_closed_buyer', 'Notifications\TransactionClosedBuyerNotificationController@sendNotification');

    $router->post('/email/send/transaction_closed_seller', 'Notifications\TransactionClosedSellerNotificationController@sendNotification');

    $router->post('/email/send/dispute_opened', 'Notifications\DisputeOpenedNotificationController@sendNotification');

    $router->post('/email/send/due_date_extended', 'Notifications\DueDateExtensionNotificationController@sendNotification');

    $router->post('/email/send/due_date_proposal', 'Notifications\DueDateProposalNotificationController@sendNotification');

    $router->post('/email/send/successful_refund', 'Notifications\SuccessfulRefundNotificationController@sendTransactionRefundNotification');

    $router->post('/email/send/verification_successful', 'Notifications\EmailsNotificationsController@sendIDVerificationSuccessful');

    $router->post('/email/send/verification_failed', 'Notifications\EmailsNotificationsController@sendIDVerificationFailed');

    //end

    // otp notification
    $router->post('/send_otp', 'Login\OtpNotificationController@sendNotification');

    $router->post('/send_authorization', 'Login\AuthorizationNotificationController@sendNotification');
    $router->post('/send_authorized', 'Login\AuthorizationNotificationController@sendAuthorizedNotification');

    // payment notification
    $router->post('/email/payment/receipt', 'Payment\ReceiptNotificationController@sendReceipt');

    $router->post('/email/disbursement/failed', 'Notifications\EmailsNotificationsController@sendDisbursementFailed');
    $router->post('/email/send/wallet_funded', 'Notifications\EmailsNotificationsController@sendWalletFunded');
    $router->post('/email/send/withdrawal_successful', 'Notifications\EmailsNotificationsController@sendWithdrawalSuccessful');
    $router->post('/email/send/bank_account_added', 'Notifications\EmailsNotificationsController@sendBankAccountAdded');

    // Custom
    $router->post('/email/send/custom', 'Notifications\HeadlessNotification@sendNow');

    $router->post('/email/send/headless', 'Notifications\HeadlessNotification@headless');
});

//Plugin Notification Event
$router->post('/plugins/plugin_event', 'Plugins\EventNotificationController@sendPluginEventNotification');
$router->post('/contact/send', 'Contact\ContactFormNotificationController@sendContactFormMessage');
