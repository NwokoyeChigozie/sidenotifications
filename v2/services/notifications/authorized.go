package notifications

import (
	"encoding/json"
	"fmt"

	"github.com/vesicash/notifications-ms/v2/internal/config"
	"github.com/vesicash/notifications-ms/v2/internal/models"
	"github.com/vesicash/notifications-ms/v2/services/send"
)

func (n NotificationObject) SendAuthorized() error {
	var (
		notificationData     = models.SendAuthorized{}
		templateFileName     = "authorized.html"
		baseTemplateFileName = "default.html"
	)

	err := json.Unmarshal([]byte(n.Notification.Data), &notificationData)
	if err != nil {
		return fmt.Errorf("error decoding saved notification data, %v", err)
	}

	subject := fmt.Sprintf("Your recently logged into your account from: %v", notificationData.Location)

	user, err := GetUserWithAccountID(n.ExtReq, notificationData.AccountID)
	if err != nil {
		return fmt.Errorf("error getting user with account id %v, %v", notificationData.AccountID, err)
	}

	data, err := ConvertToMapAndAddExtraData(notificationData, map[string]interface{}{"firstname": thisOrThatStr(user.Firstname, user.EmailAddress)})
	if err != nil {
		return fmt.Errorf("error converting data to map, %v", err)
	}

	return send.SendEmail(n.ExtReq, user.EmailAddress, subject, templateFileName, baseTemplateFileName, data)
}

func (n NotificationObject) SendAuthorization() error {
	var (
		notificationData     = models.SendAuthorization{}
		subject              = "Your account needs authorization"
		templateFileName     = "authorized.html"
		baseTemplateFileName = "default.html"
		configData           = config.GetConfig()
	)

	err := json.Unmarshal([]byte(n.Notification.Data), &notificationData)
	if err != nil {
		return fmt.Errorf("error decoding saved notification data, %v", err)
	}

	user, err := GetUserWithAccountID(n.ExtReq, notificationData.AccountID)
	if err != nil {
		return fmt.Errorf("error getting user with account id %v, %v", notificationData.AccountID, err)
	}

	authorizeUrl := fmt.Sprintf("%v/auth/authorize/%v", configData.App.SiteUrl, notificationData.Token)
	declineUrl := fmt.Sprintf("%v/auth/authorize/decline/%v", configData.App.SiteUrl, notificationData.Token)

	data, err := ConvertToMapAndAddExtraData(notificationData,
		map[string]interface{}{"firstname": thisOrThatStr(user.Firstname, user.EmailAddress),
			"authorize_url": authorizeUrl, "decline_url": declineUrl,
		},
	)
	if err != nil {
		return fmt.Errorf("error converting data to map, %v", err)
	}

	return send.SendEmail(n.ExtReq, user.EmailAddress, subject, templateFileName, baseTemplateFileName, data)
}
