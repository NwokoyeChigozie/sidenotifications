package notifications

import (
	"encoding/json"
	"fmt"

	"github.com/vesicash/notifications-ms/internal/config"
	"github.com/vesicash/notifications-ms/internal/models"
	"github.com/vesicash/notifications-ms/services/send"
)

func (n NotificationObject) SendEmailVerificationMail() error {
	var (
		notificationData     = models.SendEmailVerificationMail{}
		subject              = "Please verify your email address"
		templateFileName     = "email_verification.html"
		baseTemplateFileName = ""
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

	verificationUrl := fmt.Sprintf("%v/email-verify/%v/%v", configData.App.SiteUrl, user.AccountID, notificationData.Token)
	data, err := ConvertToMapAndAddExtraData(notificationData, map[string]interface{}{"firstname": thisOrThatStr(user.Firstname, user.EmailAddress), "code": notificationData.Code, "verification_url": verificationUrl})
	if err != nil {
		return fmt.Errorf("error converting data to map, %v", err)
	}

	if notificationData.EmailAddress != "" {
		user.EmailAddress = notificationData.EmailAddress
	}

	return send.SendEmail(n.ExtReq, user.EmailAddress, subject, templateFileName, baseTemplateFileName, data)
}

func (n NotificationObject) SendEmailVerifiedMail() error {
	var (
		notificationData     = models.SendEmailVerifiedMail{}
		subject              = "Email Verified"
		templateFileName     = "email_verified.html"
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

	data, err := ConvertToMapAndAddExtraData(notificationData, map[string]interface{}{"firstname": thisOrThatStr(user.Firstname, user.EmailAddress)})
	if err != nil {
		return fmt.Errorf("error converting data to map, %v", err)
	}

	return send.SendEmail(n.ExtReq, user.EmailAddress, subject, templateFileName, baseTemplateFileName, data)
}

func (n NotificationObject) SendVerificationFailed() error {
	var (
		notificationData     = models.SendVerificationFailed{}
		subject              = "Your credentials could not be verified on Vesicash"
		templateFileName     = "id_not_verified.html"
		baseTemplateFileName = "default.html"
	)

	err := json.Unmarshal([]byte(n.Notification.Data), &notificationData)
	if err != nil {
		return fmt.Errorf("error decoding saved notification data, %v", err)
	}

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

func (n NotificationObject) SendVerificationSuccessful() error {
	var (
		notificationData     = models.SendVerificationSuccessful{}
		subject              = "Your credentials has been successfully verified on Vesicash"
		templateFileName     = "id_verified.html"
		baseTemplateFileName = "default.html"
	)

	err := json.Unmarshal([]byte(n.Notification.Data), &notificationData)
	if err != nil {
		return fmt.Errorf("error decoding saved notification data, %v", err)
	}

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
