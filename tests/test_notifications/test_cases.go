package test_notifications

import (
	"fmt"

	"github.com/vesicash/notifications-ms/external/external_models"
	"github.com/vesicash/notifications-ms/services/names"
)

func GetTestCases(user external_models.User, transaction external_models.TransactionByID, payment external_models.Payment) ([]TestRequest, error) {
	var (
		requestObj = NewTestObj()
		tests      = []TestRequest{}
	)

	notificationNames, err := names.GetNames("../../services/names")
	if err != nil {
		return []TestRequest{}, err
	}

	for _, name := range notificationNames {
		requestObj.Name = name
		actionName := names.NotificationName(name)
		switch actionName {
		case names.SendWelcomeMail:
			requestObj.RequestBody = map[string]interface{}{"account_id": user.AccountID}
			requestObj.RequiredFields = []string{"account_id"}
		case names.SendWelcomeSMS:
			requestObj.RequestBody = map[string]interface{}{"account_id": user.AccountID}
			requestObj.RequiredFields = []string{"account_id"}
		case names.SendOTP:
			requestObj.RequestBody = map[string]interface{}{"account_id": user.AccountID, "otp_token": 683727362}
			requestObj.RequiredFields = []string{"account_id", "otp_token"}
		case names.SendWelcomePasswordMail:
			requestObj.RequestBody = map[string]interface{}{"account_id": user.AccountID, "token": 683727362}
			requestObj.RequiredFields = []string{"account_id", "token"}
		case names.SendResetPasswordMail:
			requestObj.RequestBody = map[string]interface{}{"account_id": user.AccountID, "token": 683727362}
			requestObj.RequiredFields = []string{"account_id", "token"}
		case names.SendResetPasswordSMS:
			requestObj.RequestBody = map[string]interface{}{"account_id": user.AccountID, "token": 683727362}
			requestObj.RequiredFields = []string{"account_id", "token"}
		case names.SendResetPasswordDoneMail:
			requestObj.RequestBody = map[string]interface{}{"account_id": user.AccountID}
			requestObj.RequiredFields = []string{"account_id"}
		case names.SendResetPasswordDoneSMS:
			requestObj.RequestBody = map[string]interface{}{"account_id": user.AccountID}
			requestObj.RequiredFields = []string{"account_id"}
		case names.SendEmailVerificationMail:
			requestObj.RequestBody = map[string]interface{}{
				"email_address": user.EmailAddress,
				"account_id":    user.AccountID,
				"code":          7799722,
				"token":         "779YH9722",
			}
			requestObj.RequiredFields = []string{"account_id", "code"}
		case names.SendEmailVerifiedMail:
			requestObj.RequestBody = map[string]interface{}{"account_id": user.AccountID}
			requestObj.RequiredFields = []string{"account_id"}
		case names.SendSMSToPhone:
			requestObj.RequestBody = map[string]interface{}{
				"account_id":   user.AccountID,
				"phone_number": user.PhoneNumber,
				"message":      "Test Message",
			}
			requestObj.RequiredFields = []string{"account_id", "message"}
		case names.SendVerificationFailed:
			requestObj.RequestBody = map[string]interface{}{
				"account_id": user.AccountID,
				"type":       "bvn",
				"message":    "Test Message",
			}
			requestObj.RequiredFields = []string{"account_id"}
		case names.SendVerificationSuccessful:
			requestObj.RequestBody = map[string]interface{}{
				"account_id": user.AccountID,
				"type":       "bvn",
			}
			requestObj.RequiredFields = []string{"account_id"}
		case names.SendAuthorized:
			requestObj.RequestBody = map[string]interface{}{
				"account_id": user.AccountID,
				"ip":         "ip-address",
				"location":   "address",
				"device":     "device",
			}
			requestObj.RequiredFields = []string{"account_id", "ip", "location", "device"}
		case names.SendAuthorization:
			requestObj.RequestBody = map[string]interface{}{
				"account_id": user.AccountID,
				"ip":         "ip-address",
				"location":   "address",
				"device":     "device",
				"token":      "UYJBB788",
			}
			requestObj.RequiredFields = []string{"account_id", "ip", "location", "device", "token"}
		case names.SendNewTransaction:
			requestObj.RequestBody = map[string]interface{}{"transaction_id": transaction.TransactionID}
			requestObj.RequiredFields = []string{"transaction_id"}
		case names.SendTransactionAccepted:
			requestObj.RequestBody = map[string]interface{}{"transaction_id": transaction.TransactionID}
			requestObj.RequiredFields = []string{"transaction_id"}
		case names.SendTransactionRejected:
			requestObj.RequestBody = map[string]interface{}{"transaction_id": transaction.TransactionID}
			requestObj.RequiredFields = []string{"transaction_id"}
		case names.SendTransactionDeliveredAndAccepted:
			requestObj.RequestBody = map[string]interface{}{"transaction_id": transaction.TransactionID}
			requestObj.RequiredFields = []string{"transaction_id"}
		case names.SendTransactionDeliveredAndRejected:
			requestObj.RequestBody = map[string]interface{}{"transaction_id": transaction.TransactionID}
			requestObj.RequiredFields = []string{"transaction_id"}
		case names.SendDisputeOpened:
			requestObj.RequestBody = map[string]interface{}{"transaction_id": transaction.TransactionID}
			requestObj.RequiredFields = []string{"transaction_id"}
		case names.SendTransactionDelivered:
			requestObj.RequestBody = map[string]interface{}{"transaction_id": transaction.TransactionID}
			requestObj.RequiredFields = []string{"transaction_id"}
		case names.SendDueDateProposal:
			requestObj.RequestBody = map[string]interface{}{"transaction_id": transaction.TransactionID}
			requestObj.RequiredFields = []string{"transaction_id"}
		case names.SendDueDateExtended:
			requestObj.RequestBody = map[string]interface{}{"transaction_id": transaction.TransactionID}
			requestObj.RequiredFields = []string{"transaction_id"}
		case names.SendWalletFunded:
			requestObj.RequestBody = map[string]interface{}{
				"account_id":     user.AccountID,
				"amount":         transaction.TotalAmount,
				"currency":       transaction.Currency,
				"transaction_id": transaction.TransactionID,
			}
			requestObj.RequiredFields = []string{"account_id"}
		case names.SendWalletDebited:
			requestObj.RequestBody = map[string]interface{}{
				"account_id":     user.AccountID,
				"amount":         transaction.TotalAmount,
				"currency":       transaction.Currency,
				"transaction_id": transaction.TransactionID,
			}
			requestObj.RequiredFields = []string{"account_id"}
		case names.SendPaymentReceipt:
			requestObj.RequestBody = map[string]interface{}{
				"reference":                   "ubuebebuasb9777b",
				"payment_id":                  payment.PaymentID,
				"transaction_type":            transaction.Type,
				"transaction_id":              transaction.TransactionID,
				"buyer":                       user.AccountID,
				"seller":                      user.AccountID,
				"inspection_period_formatted": "2023-12-12",
				"expected_delivery":           "2023-12-12",
				"title":                       transaction.Title,
				"amount":                      transaction.TotalAmount,
				"currency":                    transaction.Currency,
				"escrow_charge":               10,
				"broker_charge":               10,
			}
			requestObj.RequiredFields = []string{"reference"}
		case names.SendTransactionPaid:
			requestObj.RequestBody = map[string]interface{}{"transaction_id": transaction.TransactionID}
			requestObj.RequiredFields = []string{"transaction_id"}
		case names.SendSuccessfulRefund:
			requestObj.RequestBody = map[string]interface{}{"transaction_id": transaction.TransactionID}
			requestObj.RequiredFields = []string{"transaction_id"}
		case names.SendBuyerDisbursementSuccessful:
			requestObj.RequestBody = map[string]interface{}{"transaction_id": transaction.TransactionID}
			requestObj.RequiredFields = []string{"transaction_id"}
		case names.SendSellerDisbursementSuccessful:
			requestObj.RequestBody = map[string]interface{}{"transaction_id": transaction.TransactionID}
			requestObj.RequiredFields = []string{"transaction_id"}
		case names.SendTransactionClosedBuyer:
			requestObj.RequestBody = map[string]interface{}{"transaction_id": transaction.TransactionID}
			requestObj.RequiredFields = []string{"transaction_id"}
		case names.SendTransactionClosedSeller:
			requestObj.RequestBody = map[string]interface{}{"transaction_id": transaction.TransactionID}
			requestObj.RequiredFields = []string{"transaction_id"}
		default:
			// continue
			return tests, fmt.Errorf("testcase for %v, not implemented", name)
		}

		tests = append(tests, GetSideCases(requestObj)...)
	}

	return tests, nil
}
