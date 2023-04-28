package notifications

import (
	"encoding/json"
	"fmt"

	"github.com/vesicash/notifications-ms/v2/internal/models"
	"github.com/vesicash/notifications-ms/v2/services/send"
)

func (n NotificationObject) SendWalletFunded() error {
	var (
		notificationData  = models.SendWalletFunded{}
		extraData         = map[string]interface{}{}
		transactionObject TransactionDataObject
	)

	err := json.Unmarshal([]byte(n.Notification.Data), &notificationData)
	if err != nil {
		return fmt.Errorf("error decoding saved notification data, %v", err)
	}

	user, err := GetUserWithAccountID(n.ExtReq, notificationData.AccountID)
	if err != nil {
		return fmt.Errorf("error getting user with account id %v, %v", notificationData.AccountID, err)
	}

	if notificationData.TransactionID != "" {
		transactionObject, _ = GetTransactionObject(n.ExtReq, notificationData.TransactionID)
	}

	extraData = AddTransactionDataToMap(transactionObject, extraData)
	extraData["firstname"] = thisOrThatStr(user.Firstname, user.EmailAddress)
	data, err := ConvertToMapAndAddExtraData(notificationData, extraData)
	if err != nil {
		return fmt.Errorf("error converting data to map, %v", err)
	}

	err = send.SendEmail(n.ExtReq, user.EmailAddress, "You’ve funded your Wallet successfully", "wallet-funded.html", "default.html", data)
	if err != nil {
		return err
	}

	message := "your wallet has been successfully funded"
	if user.PhoneNumber != "" {
		phone, err := GetInternationalNumber(n.ExtReq, user)
		if err == nil {
			err := send.SendSimpleSMS(n.ExtReq, phone, message)
			if err != nil {
				return err
			}
		}
	}

	return nil

}

func (n NotificationObject) SendWalletDebited() error {
	var (
		notificationData  = models.SendWalletDebited{}
		extraData         = map[string]interface{}{}
		transactionObject TransactionDataObject
	)

	err := json.Unmarshal([]byte(n.Notification.Data), &notificationData)
	if err != nil {
		return fmt.Errorf("error decoding saved notification data, %v", err)
	}

	user, err := GetUserWithAccountID(n.ExtReq, notificationData.AccountID)
	if err != nil {
		return fmt.Errorf("error getting user with account id %v, %v", notificationData.AccountID, err)
	}

	if notificationData.TransactionID != "" {
		transactionObject, _ = GetTransactionObject(n.ExtReq, notificationData.TransactionID)
	}

	extraData = AddTransactionDataToMap(transactionObject, extraData)
	extraData["firstname"] = thisOrThatStr(user.Firstname, user.EmailAddress)
	data, err := ConvertToMapAndAddExtraData(notificationData, extraData)
	if err != nil {
		return fmt.Errorf("error converting data to map, %v", err)
	}

	err = send.SendEmail(n.ExtReq, user.EmailAddress, "Wallet Debit successful", "wallet-debited.html", "default.html", data)
	if err != nil {
		return err
	}

	message := "your wallet has been successfully debited"
	if user.PhoneNumber != "" {
		phone, err := GetInternationalNumber(n.ExtReq, user)
		if err == nil {
			err := send.SendSimpleSMS(n.ExtReq, phone, message)
			if err != nil {
				return err
			}
		}
	}

	return nil

}
