package notifications

import (
	"encoding/json"
	"fmt"

	"github.com/vesicash/notifications-ms/internal/models"
	"github.com/vesicash/notifications-ms/services/send"
)

func (n NotificationObject) SendTransactionClosedBuyer() error {
	var (
		notificationData = models.SendTransactionClosedBuyer{}
		extraData        = map[string]interface{}{}
		subject          = "Your transaction has been successfully completed and the window closed"
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
		err := send.SendEmail(n.ExtReq, transactionObject.Buyer.EmailAddress, subject, "transactions/transaction_closed_buyer.html", "default.html", data)
		if err != nil {
			return err
		}
	}

	message := getTransactionMessage("transaction-closed-buyer", transactionObject)
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

func (n NotificationObject) SendTransactionClosedSeller() error {
	var (
		notificationData = models.SendTransactionClosedSeller{}
		extraData        = map[string]interface{}{}
		subject          = "Your transaction has been successfully completed and the window closed"
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

	if transactionObject.Seller.ID != 0 {
		data["account_id"] = transactionObject.Seller.AccountID
		err := send.SendEmail(n.ExtReq, transactionObject.Seller.EmailAddress, subject, "transactions/transaction_closed_seller.html", "default.html", data)
		if err != nil {
			return err
		}
	}

	message := getTransactionMessage("transaction-closed-seller", transactionObject)
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
