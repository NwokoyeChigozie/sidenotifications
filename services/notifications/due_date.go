package notifications

import (
	"encoding/json"
	"fmt"

	"github.com/vesicash/notifications-ms/internal/models"
	"github.com/vesicash/notifications-ms/services/send"
)

func (n NotificationObject) SendDueDateProposal() error {
	var (
		notificationData = models.SendDueDateProposal{}
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
		err := send.SendEmail(n.ExtReq, transactionObject.Buyer.EmailAddress, "Proposal for Due Date Extension", "transactions/due_date_proposal.html", "", data)
		if err != nil {
			return err
		}
	}

	message := getTransactionMessage("due-date-proposal", transactionObject)
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

func (n NotificationObject) SendDueDateExtended() error {
	var (
		notificationData = models.SendDueDateExtended{}
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

	if transactionObject.Seller.ID != 0 {
		data["account_id"] = transactionObject.Seller.AccountID
		err := send.SendEmail(n.ExtReq, transactionObject.Seller.EmailAddress, "Due date has been Extended", "transactions/due_date_extension.html", "", data)
		if err != nil {
			return err
		}
	}

	message := getTransactionMessage("due-date-extension", transactionObject)
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
