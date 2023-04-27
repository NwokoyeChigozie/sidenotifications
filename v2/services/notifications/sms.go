package notifications

import (
	"encoding/json"
	"fmt"

	"github.com/vesicash/notifications-ms/v2/internal/models"
	"github.com/vesicash/notifications-ms/v2/services/send"
)

func (n NotificationObject) SendSMSToPhone() error {
	var (
		notificationData = models.SendSMSToPhone{}
		phone            string
	)

	err := json.Unmarshal([]byte(n.Notification.Data), &notificationData)
	if err != nil {
		return fmt.Errorf("error decoding saved notification data, %v", err)
	}

	user, err := GetUserWithAccountID(n.ExtReq, notificationData.AccountID)
	if err != nil {
		return fmt.Errorf("error getting user with account id %v, %v", notificationData.AccountID, err)
	}

	if notificationData.PhoneNumber != "" {
		user.PhoneNumber = notificationData.PhoneNumber
	}

	phone, err = GetInternationalNumber(n.ExtReq, user)
	if err != nil {
		return err
	}

	return send.SendSimpleSMS(n.ExtReq, phone, notificationData.Message)
}
