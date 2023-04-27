package notifications

import (
	"encoding/json"
	"fmt"
	"strings"

	"github.com/vesicash/notifications-ms/v2/internal/models"
	"github.com/vesicash/notifications-ms/v2/services/send"
	"github.com/vesicash/notifications-ms/v2/utility"
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
		country, err := GetUserCountryWithAccountID(n.ExtReq, int(user.AccountID))
		if err != nil {
			errs = append(errs, err.Error())
		}

		message := fmt.Sprintf("'Hello %v, Your One-Time Password is: %v", user.Firstname, notificationData.OtpToken)
		phone, err := utility.MakeInternationalPhoneNumber(n.ExtReq.Test, user.PhoneNumber, country.CountryCode)
		if err != nil {
			errs = append(errs, fmt.Sprintf("error getting international number for %v, country %v, %v", user.PhoneNumber, country.CountryCode, err.Error()))
		} else {
			err := send.SendSimpleSMS(n.ExtReq, phone, message)
			if err != nil {
				errs = append(errs, err.Error())
			}

		}
	}

	data, err := ConvertToMapAndAddExtraData(notificationData, map[string]interface{}{"firstname": thisOrThatStr(user.Firstname, user.EmailAddress), "business_name": thisOrThatStr(businessProfile.BusinessName, "Vesicash"), "otp_token": notificationData.OtpToken})
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
