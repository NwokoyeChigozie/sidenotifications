package actions

import (
	"fmt"

	"github.com/gin-gonic/gin"
	"github.com/vesicash/notifications-ms/v2/internal/models"
	"github.com/vesicash/notifications-ms/v2/services/names"
)

func Bind(c *gin.Context, name names.NotificationName) (interface{}, error) {
	switch name {
	case names.SendWelcomeMail:
		req := models.SendWelcomeMail{}
		err := c.ShouldBind(&req)
		return req, err
	case names.SendWelcomeSMS:
		req := models.SendWelcomeSMS{}
		err := c.ShouldBind(&req)
		return req, err
	case names.SendOTP:
		req := models.SendOTP{}
		err := c.ShouldBind(&req)
		return req, err
	case names.SendWelcomePasswordMail:
		req := models.SendWelcomePasswordMail{}
		err := c.ShouldBind(&req)
		return req, err
	case names.SendResetPasswordMail:
		req := models.SendResetPassword{}
		err := c.ShouldBind(&req)
		return req, err
	case names.SendResetPasswordSMS:
		req := models.SendResetPassword{}
		err := c.ShouldBind(&req)
		return req, err
	case names.SendResetPasswordDoneMail:
		req := models.SendResetPasswordDone{}
		err := c.ShouldBind(&req)
		return req, err
	case names.SendResetPasswordDoneSMS:
		req := models.SendResetPasswordDone{}
		err := c.ShouldBind(&req)
		return req, err
	default:
		return nil, fmt.Errorf("bind for %v, not implemented", name)
	}
}
