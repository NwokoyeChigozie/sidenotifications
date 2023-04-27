package actions

import (
	"fmt"

	"github.com/vesicash/notifications-ms/v2/external/request"
	"github.com/vesicash/notifications-ms/v2/internal/models"
	"github.com/vesicash/notifications-ms/v2/pkg/repository/storage/postgresql"
	"github.com/vesicash/notifications-ms/v2/services/names"
	"github.com/vesicash/notifications-ms/v2/services/notifications"
)

func Send(extReq request.ExternalRequest, db postgresql.Databases, notification *models.NotificationRecord) error {
	var (
		err  error
		req  = notifications.NewNotificationObject(extReq, db, notification)
		name = GetName(notification.Name)
	)

	switch name {
	case names.SendWelcomeMail:
		err = req.SendWelcomeMail()
	case names.SendWelcomeSMS:
		err = req.SendWelcomeSMS()
	case names.SendOTP:
		err = req.SendOTP()
	case names.SendWelcomePasswordMail:
		err = req.SendWelcomePasswordMail()
	case names.SendResetPasswordMail:
		err = req.SendResetPasswordMail()
	case names.SendResetPasswordSMS:
		err = req.SendResetPasswordSMS()
	case names.SendResetPasswordDoneMail:
		err = req.SendResetPasswordDoneMail()
	case names.SendResetPasswordDoneSMS:
		err = req.SendResetPasswordDoneSMS()
	case names.SendEmailVerificationMail:
		err = req.SendEmailVerificationMail()
	case names.SendEmailVerifiedMail:
		err = req.SendEmailVerifiedMail()
	case names.SendSMSToPhone:
		err = req.SendSMSToPhone()
	case names.SendVerificationFailed:
		err = req.SendVerificationFailed()
	case names.SendVerificationSuccessful:
		err = req.SendVerificationSuccessful()
	case names.SendAuthorized:
		err = req.SendAuthorized()
	case names.SendAuthorization:
		err = req.SendAuthorization()
	case names.SendNewTransaction:
		err = req.SendNewTransaction()
	default:
		return handleNotificationErr(extReq, db, notification, fmt.Errorf("send for %v, not implemented", notification.Name))
	}

	return handleNotificationErr(extReq, db, notification, err)
}
