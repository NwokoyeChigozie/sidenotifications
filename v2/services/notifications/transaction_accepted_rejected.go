package notifications

import (
	"encoding/json"
	"fmt"

	"github.com/vesicash/notifications-ms/v2/internal/models"
	"github.com/vesicash/notifications-ms/v2/services/send"
)

func (n NotificationObject) SendTransactionAccepted() error {
	var (
		notificationData = models.SendNewTransaction{}
		extraData        = map[string]interface{}{}
		subject          = "Transaction Accepted"
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
		err := send.SendEmail(n.ExtReq, transactionObject.Buyer.EmailAddress, subject, "transactions/transaction_accepted_buyer.html", "default.html", data)
		if err != nil {
			return err
		}
	}

	if transactionObject.Seller.ID != 0 {
		data["account_id"] = transactionObject.Seller.AccountID
		err := send.SendEmail(n.ExtReq, transactionObject.Seller.EmailAddress, subject, "transactions/transaction_accepted_seller.html", "default.html", data)
		if err != nil {
			return err
		}
	}

	message := getTransactionMessage("transaction-accepted", transactionObject)
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

func (n NotificationObject) SendTransactionRejected() error {
	var (
		notificationData = models.SendNewTransaction{}
		extraData        = map[string]interface{}{}
		subject          = "Transaction Rejected"
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
		data["firstname"] = thisOrThatStr(transactionObject.Buyer.Firstname, transactionObject.Buyer.EmailAddress)
		data["account_id"] = transactionObject.Buyer.AccountID
		err := send.SendEmail(n.ExtReq, transactionObject.Buyer.EmailAddress, subject, "transactions/transaction_delivered_rejected.html", "default.html", data)
		if err != nil {
			return err
		}
	}

	if transactionObject.Seller.ID != 0 {
		data["firstname"] = thisOrThatStr(transactionObject.Seller.Firstname, transactionObject.Seller.EmailAddress)
		data["account_id"] = transactionObject.Seller.AccountID
		err := send.SendEmail(n.ExtReq, transactionObject.Seller.EmailAddress, subject, "transactions/transaction_delivered_rejected.html", "default.html", data)
		if err != nil {
			return err
		}
	}

	message := getTransactionMessage("transaction-rejected", transactionObject)
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
