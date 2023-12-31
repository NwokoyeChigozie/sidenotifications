package notifications

import (
	"encoding/json"
	"fmt"
	"strconv"

	"github.com/vesicash/notifications-ms/internal/models"
	"github.com/vesicash/notifications-ms/services/send"
	"github.com/vesicash/notifications-ms/utility"
)

func (n NotificationObject) SendTransactionPaid() error {
	var (
		notificationData = models.SendTransactionPaid{}
		extraData        = map[string]interface{}{}
	)

	err := json.Unmarshal([]byte(n.Notification.Data), &notificationData)
	if err != nil {
		return fmt.Errorf("error decoding saved notification data, %v", err)
	}

	transactionObject, err := GetTransactionObject(n.ExtReq, notificationData.TransactionID)
	if err != nil {
		return fmt.Errorf("error getting transaction object, %v", err)
	}

	extraData = AddTransactionDataToMap(transactionObject, extraData)
	extraData["buyer_user"] = transactionObject.Buyer
	extraData["seller_user"] = transactionObject.Seller
	extraData["broker_charge"], _ = strconv.ParseFloat(transactionObject.Transaction.Broker.BrokerCharge, 64)
	extraData["shipping_fee"] = transactionObject.Transaction.ShippingFee
	extraData["currency"] = transactionObject.Transaction.Currency
	extraData["amount"] = transactionObject.Transaction.TotalAmount
	extraData["escrow_charge"] = transactionObject.Transaction.EscrowCharge
	extraData["expected_delivery"] = transactionObject.Transaction.DueDate
	extraData["inspection_period_formatted"] = utility.FormatInspectionPeriod(transactionObject.Transaction.InspectionPeriod)
	data, err := ConvertToMapAndAddExtraData(notificationData, extraData)
	if err != nil {
		return fmt.Errorf("error converting data to map, %v", err)
	}

	if transactionObject.Buyer.ID != 0 {
		data["account_id"] = transactionObject.Buyer.AccountID
		transactionNotification := TransactionNotificationType1{
			ExtReq:            n.ExtReq,
			Db:                n.Db,
			EmailAddress:      transactionObject.Buyer.EmailAddress,
			TransactionObject: transactionObject,
			Data:              data,
			InstantescrowSource: TransactionNotificationType1Data{
				Subject:          "You have made payment for your transaction",
				TemplateFileName: "transactions/transaction_paid.html",
			},
			Transfer: TransactionNotificationType1Data{
				Subject:          "You have made payment for your transaction",
				TemplateFileName: "transactions/transaction_paid.html",
			},
			Marketplace: TransactionNotificationType1Data{
				Subject:              "You have made payment for your transaction",
				TemplateFileName:     "social_commerce/successful_payment_made.html",
				BaseTemplateFileName: "default.html",
			},
			SocialCommerce: TransactionNotificationType1Data{
				Subject:              "Trizact payment receipt " + notificationData.TransactionID,
				PdfTemplatePath:      "payment/receipt.html",
				PdfTemplateName:      "transaction_paid.pdf",
				TemplateFileName:     "social_commerce/successful_payment_made.html",
				BaseTemplateFileName: "default.html",
			},
			Default: TransactionNotificationType1Data{
				Subject:          "You have made payment for your transaction",
				TemplateFileName: "transactions/transaction_paid.html",
			},
		}

		err := transactionNotification.sendTransactionNotificationType1Data()
		if err != nil {
			return err
		}
	}

	if transactionObject.Seller.ID != 0 {
		data["account_id"] = transactionObject.Seller.AccountID
		transactionNotification := TransactionNotificationType1{
			ExtReq:            n.ExtReq,
			Db:                n.Db,
			EmailAddress:      transactionObject.Seller.EmailAddress,
			TransactionObject: transactionObject,
			Data:              data,
			Transfer: TransactionNotificationType1Data{
				Subject:          transactionObject.Buyer.EmailAddress + " just sent you a payment.",
				TemplateFileName: "transactions/transaction_paid.html",
			},
			Marketplace: TransactionNotificationType1Data{
				Subject:              "Your funds have been safely deposited into our trust account",
				TemplateFileName:     "marketplace/payment_made.html",
				BaseTemplateFileName: "default.html",
			},
			SocialCommerce: TransactionNotificationType1Data{
				Subject:              fmt.Sprintf("%v just paid %v%v via your payment link.", transactionObject.Buyer.Firstname, transactionObject.Transaction.Currency, transactionObject.Transaction.Amount),
				PdfTemplatePath:      "payment/receipt.html",
				PdfTemplateName:      "transaction_paid.pdf",
				TemplateFileName:     "social_commerce/payment_made.html",
				BaseTemplateFileName: "default.html",
			},
			Default: TransactionNotificationType1Data{
				Subject:              "Your funds have been safely deposited into our trust account.",
				TemplateFileName:     "marketplace/payment_made.html",
				BaseTemplateFileName: "default.html",
			},
		}

		err := transactionNotification.sendTransactionNotificationType1Data()
		if err != nil {
			return err
		}
	}

	message := getTransactionMessage("transaction-paid", transactionObject)
	if transactionObject.Buyer.PhoneNumber != "" {
		phone, err := GetInternationalNumber(n.ExtReq, transactionObject.Buyer)
		if err == nil {
			err := send.SendSimpleSMS(n.ExtReq, phone, message)
			if err != nil {
				return err
			}
		}
	}

	if transactionObject.Seller.PhoneNumber != "" {
		phone, err := GetInternationalNumber(n.ExtReq, transactionObject.Seller)
		if err == nil {
			err := send.SendSimpleSMS(n.ExtReq, phone, message)
			if err != nil {
				return err
			}
		}
	}

	return nil

}

func (n NotificationObject) SendSuccessfulRefund() error {
	var (
		notificationData = models.SendSuccessfulRefund{}
		extraData        = map[string]interface{}{}
		subject          = "You have been refunded"
	)

	err := json.Unmarshal([]byte(n.Notification.Data), &notificationData)
	if err != nil {
		return fmt.Errorf("error decoding saved notification data, %v", err)
	}

	transactionObject, err := GetTransactionObject(n.ExtReq, notificationData.TransactionID)
	if err != nil {
		return fmt.Errorf("error getting transaction object, %v", err)
	}

	extraData = AddTransactionDataToMap(transactionObject, extraData)
	extraData["payment"] = transactionObject.payment
	data, err := ConvertToMapAndAddExtraData(notificationData, extraData)
	if err != nil {
		return fmt.Errorf("error converting data to map, %v", err)
	}

	if transactionObject.Buyer.ID != 0 {
		data["account_id"] = transactionObject.Buyer.AccountID
		err := send.SendEmail(n.ExtReq, transactionObject.Buyer.EmailAddress, subject, "transactions/successful_refund.html", "", data)
		if err != nil {
			return err
		}
	}

	message := getTransactionMessage("successful-refund", transactionObject)
	if transactionObject.Buyer.PhoneNumber != "" {
		phone, err := GetInternationalNumber(n.ExtReq, transactionObject.Buyer)
		if err == nil {
			err := send.SendSimpleSMS(n.ExtReq, phone, message)
			if err != nil {
				return err
			}
		}
	}

	return nil

}
