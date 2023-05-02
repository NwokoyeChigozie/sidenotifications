package notifications

import (
	"encoding/json"
	"fmt"

	"github.com/vesicash/notifications-ms/internal/models"
	"github.com/vesicash/notifications-ms/services/send"
)

func (n NotificationObject) SendBuyerDisbursementSuccessful() error {
	var (
		notificationData = models.SendBuyerDisbursementSuccessful{}
		extraData        = map[string]interface{}{}
		subject          = "Escrow fund has been disbursed!"
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
		err := send.SendEmail(n.ExtReq, transactionObject.Buyer.EmailAddress, subject, "transactions/escrow_disbursed_buyer.html", "", data)
		if err != nil {
			return err
		}
	}

	return nil

}

func (n NotificationObject) SendSellerDisbursementSuccessful() error {
	var (
		notificationData = models.SendSellerDisbursementSuccessful{}
		extraData        = map[string]interface{}{}
		subject          = "Disbursement Complete"
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
		err := send.SendEmail(n.ExtReq, transactionObject.Seller.EmailAddress, subject, "transactions/escrow_disbursed_seller.html", "", data)
		if err != nil {
			return err
		}
	}

	return nil

}
