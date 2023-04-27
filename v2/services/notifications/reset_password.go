package notifications

import (
	"encoding/json"
	"fmt"

	"github.com/vesicash/notifications-ms/v2/internal/config"
	"github.com/vesicash/notifications-ms/v2/internal/models"
	"github.com/vesicash/notifications-ms/v2/services/send"
)

func (n NotificationObject) SendResetPasswordMail() error {
	var (
		notificationData     = models.SendResetPassword{}
		subject              = "Password Reset"
		templateFileName     = "password_reset_mail.html"
		baseTemplateFileName = ""
		configData           = config.GetConfig()
	)

	err := json.Unmarshal([]byte(n.Notification.Data), &notificationData)
	if err != nil {
		return fmt.Errorf("error decoding saved notification data, %v", err)
	}

	passwordResetUrl := fmt.Sprintf("%v/reset-password/%v", configData.App.SiteUrl, notificationData.Token)

	user, err := GetUserWithAccountID(n.ExtReq, notificationData.AccountID)
	if err != nil {
		return fmt.Errorf("error getting user with account id %v, %v", notificationData.AccountID, err)
	}

	data, err := ConvertToMapAndAddExtraData(notificationData, map[string]interface{}{"firstname": thisOrThatStr(user.Firstname, user.EmailAddress), "password_reset_url": passwordResetUrl})
	if err != nil {
		return fmt.Errorf("error converting data to map, %v", err)
	}

	return send.SendEmail(n.ExtReq, user.EmailAddress, subject, templateFileName, baseTemplateFileName, data)
}

func (n NotificationObject) SendResetPasswordSMS() error {
	var (
		notificationData = models.SendResetPassword{}
		configData       = config.GetConfig()
	)

	err := json.Unmarshal([]byte(n.Notification.Data), &notificationData)
	if err != nil {
		return fmt.Errorf("error decoding saved notification data, %v", err)
	}

	user, err := GetUserWithAccountID(n.ExtReq, notificationData.AccountID)
	if err != nil {
		return fmt.Errorf("error getting user with account id %v, %v", notificationData.AccountID, err)
	}

	passwordResetUrl := fmt.Sprintf("%v/reset-password/%v", configData.App.SiteUrl, notificationData.Token)
	phone, err := GetInternationalNumber(n.ExtReq, user)
	if err != nil {
		return err
	}

	message := fmt.Sprintf("Hi %v, Your password reset code is: %v. Update Password Link: -  %v", user.Firstname, notificationData.Token, passwordResetUrl)
	return send.SendSimpleSMS(n.ExtReq, phone, message)
}

func (n NotificationObject) SendResetPasswordDoneMail() error {
	var (
		notificationData     = models.SendResetPasswordDone{}
		subject              = "Password Changed"
		templateFileName     = "password_reset_done_mail.html"
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

func (n NotificationObject) SendResetPasswordDoneSMS() error {
	var (
		notificationData = models.SendResetPasswordDone{}
	)

	err := json.Unmarshal([]byte(n.Notification.Data), &notificationData)
	if err != nil {
		return fmt.Errorf("error decoding saved notification data, %v", err)
	}

	user, err := GetUserWithAccountID(n.ExtReq, notificationData.AccountID)
	if err != nil {
		return fmt.Errorf("error getting user with account id %v, %v", notificationData.AccountID, err)
	}

	phone, err := GetInternationalNumber(n.ExtReq, user)
	if err != nil {
		return err
	}

	message := fmt.Sprintf("Hi %v, your password has been updated securely.", thisOrThatStr(user.Firstname, user.EmailAddress))
	return send.SendSimpleSMS(n.ExtReq, phone, message)
}
