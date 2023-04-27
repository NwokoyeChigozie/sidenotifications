package notifications

import (
	"encoding/json"
	"fmt"

	"github.com/vesicash/notifications-ms/v2/internal/models"
	"github.com/vesicash/notifications-ms/v2/services/send"
)

func (n NotificationObject) SendNewTransaction() error {
	var (
		notificationData = models.SendNewTransaction{}
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
			InstantescrowSource: TransactionNotificationType1Data{
				Subject:          "You just received an Escrow transaction",
				PdfTemplatePath:  "instantescrow/transaction_received_buyer.html",
				PdfTemplateName:  "transaction_received.pdf",
				TemplateFileName: "instantescrow/transaction_received_buyer.html",
			},
			Marketplace: TransactionNotificationType1Data{
				Subject:              "You have a new escrow transaction",
				PdfTemplatePath:      "marketplace/transaction_received_buyer.html",
				BasePdfTemplatePath:  "default.html",
				PdfTemplateName:      "transaction_received.pdf",
				TemplateFileName:     "marketplace/transaction_received_buyer.html",
				BaseTemplateFileName: "default.html",
			},
			SocialCommerce: TransactionNotificationType1Data{
				Subject:          "You have a new escrow transaction",
				PdfTemplatePath:  "transactions/transaction_received_buyer.html",
				PdfTemplateName:  "transaction_received.pdf",
				TemplateFileName: "transactions/transaction_received_buyer.html",
			},
			Default: TransactionNotificationType1Data{
				Subject:          "You have a new escrow transaction",
				PdfTemplatePath:  "transactions/transaction_received.html",
				PdfTemplateName:  "transaction_received.pdf",
				TemplateFileName: "transactions/transaction_received_buyer.html",
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
			InstantescrowSource: TransactionNotificationType1Data{
				Subject:          "You just received an Escrow transaction",
				PdfTemplatePath:  "instantescrow/transaction_received_seller.html",
				PdfTemplateName:  "transaction_received.pdf",
				TemplateFileName: "instantescrow/transaction_received_seller.html",
			},
			Marketplace: TransactionNotificationType1Data{
				Subject:              "You have a new escrow transaction",
				PdfTemplatePath:      "marketplace/transaction_received_seller.html",
				BasePdfTemplatePath:  "default.html",
				PdfTemplateName:      "transaction_received.pdf",
				TemplateFileName:     "marketplace/transaction_received_seller.html",
				BaseTemplateFileName: "default.html",
			},
			SocialCommerce: TransactionNotificationType1Data{
				Subject:          "You have a new escrow transaction",
				PdfTemplatePath:  "transactions/transaction_received.html",
				PdfTemplateName:  "transaction_received.pdf",
				TemplateFileName: "transactions/transaction_received_seller.html",
			},
			Default: TransactionNotificationType1Data{
				Subject:          "You have a new escrow transaction",
				PdfTemplatePath:  "transactions/transaction_received.html",
				PdfTemplateName:  "transaction_received.pdf",
				TemplateFileName: "transactions/transaction_received_seller.html",
			},
		}

		err := transactionNotification.sendTransactionNotificationType1Data()
		if err != nil {
			return err
		}
	}

	message := getTransactionMessage("transaction-received", transactionObject)
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
