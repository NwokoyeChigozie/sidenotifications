package notifications

import (
	"encoding/json"
	"fmt"
	"strings"

	"github.com/vesicash/notifications-ms/v2/internal/models"
	"github.com/vesicash/notifications-ms/v2/services/send"
	"github.com/vesicash/notifications-ms/v2/utility"
)

func (n NotificationObject) SendWelcomeMail() error {
	var (
		notificationData     = models.SendWelcomeMail{}
		subject              = "Welcome on board!🎉"
		templateFileName     = ""
		baseTemplateFileName = ""
	)

	err := json.Unmarshal([]byte(n.Notification.Data), &notificationData)
	if err != nil {
		return fmt.Errorf("error decoding saved notification data, %v", err)
	}

	user, err := GetUserWithAccountID(n.ExtReq, notificationData.AccountID)
	if err != nil {
		return fmt.Errorf("error getting user with account id %v, %v", notificationData.AccountID, err)
	}

	businessProfile, _ := GetBusinessProfileByAccountID(n.ExtReq, n.ExtReq.Logger, notificationData.AccountID)
	switch strings.ToLower(businessProfile.BusinessType) {
	case "marketplace":
		templateFileName, baseTemplateFileName = "marketplace/welcome.html", "default.html"
	case "social_commerce":
		templateFileName, baseTemplateFileName = "social_commerce/welcome.html", "default.html"
	default:
		templateFileName = "welcome-email.html"
	}

	data, err := ConvertToMapAndAddExtraData(notificationData, map[string]interface{}{"firstname": thisOrThatStr(user.Firstname, user.EmailAddress)})
	if err != nil {
		return fmt.Errorf("error converting data to map, %v", err)
	}

	return send.SendEmail(n.ExtReq, user.EmailAddress, subject, templateFileName, baseTemplateFileName, data)
}

func (n NotificationObject) SendWelcomeSMS() error {
	var (
		notificationData = models.SendWelcomeSMS{}
		name             string
	)

	err := json.Unmarshal([]byte(n.Notification.Data), &notificationData)
	if err != nil {
		return fmt.Errorf("error decoding saved notification data, %v", err)
	}

	user, err := GetUserWithAccountID(n.ExtReq, notificationData.AccountID)
	if err != nil {
		return fmt.Errorf("error getting user with account id %v, %v", notificationData.AccountID, err)
	}
	name = thisOrThatStr(user.Firstname, user.PhoneNumber)

	country, err := GetUserCountryWithAccountID(n.ExtReq, int(user.AccountID))
	if err != nil {
		return err
	}

	phone, err := utility.MakeInternationalPhoneNumber(n.ExtReq.Test, user.PhoneNumber, country.CountryCode)
	if err != nil {
		return fmt.Errorf("error getting international number for %v, country %v, %v", user.PhoneNumber, country.CountryCode, err)
	}

	message := fmt.Sprintf("Hello %v, Welcome To Vesicash, Your account has been registered on our platform and you can access it at any time.", name)
	return send.SendSimpleSMS(n.ExtReq, phone, message)
}
