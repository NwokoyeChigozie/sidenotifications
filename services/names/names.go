package names

import (
	"fmt"
	"reflect"

	"github.com/vesicash/notifications-ms/utility"
)

type NotificationName string

const (
	SendWelcomeMail                     NotificationName = "send_welcome_mail"
	SendWelcomeSMS                      NotificationName = "send_welcome_sms"
	SendOTP                             NotificationName = "send_otp"
	SendWelcomePasswordMail             NotificationName = "send_welcome_password_mail"
	SendResetPasswordMail               NotificationName = "send_reset_password_mail"
	SendResetPasswordSMS                NotificationName = "send_reset_password_sms"
	SendResetPasswordDoneMail           NotificationName = "send_reset_password_done_mail"
	SendResetPasswordDoneSMS            NotificationName = "send_reset_password_done_sms"
	SendEmailVerificationMail           NotificationName = "send_email_verification_mail"
	SendEmailVerifiedMail               NotificationName = "send_email_verified_mail"
	SendSMSToPhone                      NotificationName = "send_sms_to_phone"
	SendVerificationFailed              NotificationName = "send_verification_failed"
	SendVerificationSuccessful          NotificationName = "send_verification_successful"
	SendAuthorized                      NotificationName = "send_authorized"
	SendAuthorization                   NotificationName = "send_authorization"
	SendNewTransaction                  NotificationName = "send_new_transaction"
	SendTransactionAccepted             NotificationName = "send_transaction_accepted"
	SendTransactionRejected             NotificationName = "send_transaction_rejected"
	SendTransactionDeliveredAndAccepted NotificationName = "send_transaction_delivered_and_accepted"
	SendTransactionDeliveredAndRejected NotificationName = "send_transaction_delivered_and_rejected"
	SendDisputeOpened                   NotificationName = "send_dispute_opened"
	SendTransactionDelivered            NotificationName = "send_transaction_delivered"
	SendDueDateProposal                 NotificationName = "send_due_date_proposal"
	SendDueDateExtended                 NotificationName = "send_due_date_extended"
	SendWalletFunded                    NotificationName = "send_wallet_funded"
	SendWalletDebited                   NotificationName = "send_wallet_debited"
	SendPaymentReceipt                  NotificationName = "send_payment_receipt"
	SendTransactionPaid                 NotificationName = "send_transaction_paid"
	SendSuccessfulRefund                NotificationName = "send_successful_refund"
	SendBuyerDisbursementSuccessful     NotificationName = "send_buyer_disbursement_successful"
	SendSellerDisbursementSuccessful    NotificationName = "send_seller_disbursement_successful"
	SendTransactionClosedBuyer          NotificationName = "send_transaction_closed_buyer"
	SendTransactionClosedSeller         NotificationName = "send_transaction_closed_seller"
)

func Check() {
	constantName := "SendWelcomeMail"
	constantValue := reflect.ValueOf(constantName).Interface().(string)
	fmt.Println("check", constantValue)
}

func GetNames(pkgImportPath string) ([]string, error) {
	// pkgImportPath example  ./services/names
	names := []string{}
	constants, err := utility.GetConstants(pkgImportPath)
	if err != nil {
		return names, err
	}

	for _, v := range constants {
		names = append(names, v)
	}

	return names, nil
}
