package notifications

import (
	"encoding/json"
	"fmt"
	"strings"

	"github.com/vesicash/notifications-ms/v2/internal/models"
	"github.com/vesicash/notifications-ms/v2/services/send"
)

func (n NotificationObject) SendPaymentReceipt() error {
	var (
		notificationData  = models.SendPaymentReceipt{}
		extraData         = map[string]interface{}{}
		transactionObject TransactionDataObject
	)

	err := json.Unmarshal([]byte(n.Notification.Data), &notificationData)
	if err != nil {
		return fmt.Errorf("error decoding saved notification data, %v", err)
	}

	subject := "Receipt from Vesicash Innovative Technologies " + notificationData.Reference
	buyerUser, _ := GetUserWithAccountID(n.ExtReq, notificationData.Buyer)
	sellerUser, _ := GetUserWithAccountID(n.ExtReq, notificationData.Seller)

	if notificationData.TransactionID != "" {
		transactionObject, _ = GetTransactionObject(n.ExtReq, notificationData.TransactionID)
	}

	extraData = AddTransactionDataToMap(transactionObject, extraData)
	extraData["buyer_user"] = buyerUser
	extraData["seller_user"] = sellerUser
	data, err := ConvertToMapAndAddExtraData(notificationData, extraData)
	if err != nil {
		return fmt.Errorf("error converting data to map, %v", err)
	}

	if transactionObject.Business.ID != 0 && strings.Contains(transactionObject.Business.BusinessGivenNotifications, "payment-receipt") {
		businessUser, _ := GetUserWithAccountID(n.ExtReq, transactionObject.Business.AccountID)
		err = send.SendEmail(n.ExtReq, businessUser.EmailAddress, subject, "payment/receipt.html", "", data)
		if err != nil {
			return err
		}
	}

	err = send.SendEmail(n.ExtReq, buyerUser.EmailAddress, subject, "payment/receipt.html", "", data)
	if err != nil {
		return err
	}

	return nil

}
