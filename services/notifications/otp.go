package notifications

import (
	"encoding/json"
	"fmt"
	"strings"

	"github.com/vesicash/notifications-ms/internal/models"
	"github.com/vesicash/notifications-ms/services/send"
)

func (n NotificationObject) SendOTP() error {
	var (
		notificationData     = models.SendOTP{}
		templateFileName     = "send_otp.html"
		baseTemplateFileName = ""
		errs                 []string
	)

	err := json.Unmarshal([]byte(n.Notification.Data), &notificationData)
	if err != nil {
		return fmt.Errorf("error decoding saved notification data, %v", err)
	}

	subject := fmt.Sprintf("Secure Login: Your OTP Code Is: %v", notificationData.OtpToken)

	user, err := GetUserWithAccountID(n.ExtReq, notificationData.AccountID)
	if err != nil {
		return fmt.Errorf("error getting user with account id %v, %v", notificationData.AccountID, err)
	}

	businessProfile, _ := GetBusinessProfileByAccountID(n.ExtReq, n.ExtReq.Logger, notificationData.AccountID)

	if user.PhoneNumber != "" {
		message := fmt.Sprintf("Hello %v, Your One-Time Password is: %v", user.Firstname, notificationData.OtpToken)
		phone, err := GetInternationalNumber(n.ExtReq, user)
		if err != nil {
			return err
		} else {
			err := send.SendSimpleSMS(n.ExtReq, phone, message)
			if err != nil {
				errs = append(errs, err.Error())
			}
		}

	}

	data, err := ConvertToMapAndAddExtraData(notificationData, map[string]interface{}{"firstname": thisOrThatStr(user.Firstname, user.EmailAddress), "business_name": thisOrThatStr(businessProfile.BusinessName, "Vesicash")})
	if err != nil {
		return fmt.Errorf("error converting data to map, %v, %v", err, strings.Join(errs, ", "))
	}

	err = send.SendEmail(n.ExtReq, user.EmailAddress, subject, templateFileName, baseTemplateFileName, data)
	if err != nil {
		errs = append(errs, err.Error())
	}

	if len(errs) > 0 {
		return fmt.Errorf(strings.Join(errs, ", "))
	}
	return nil
}
