package notifications

import (
	"fmt"

	"github.com/gin-gonic/gin"
	"github.com/vesicash/notifications-ms/v2/internal/models"
)

type NotificationName string

var (
	SendWelcomeMail NotificationName = "send_welcome_mail"
)

func (name NotificationName) Model() (interface{}, error) {
	switch name {
	case SendWelcomeMail:
		return models.SendWelcomeMail{}, nil
	default:
		return nil, fmt.Errorf("model for %v, not implemented", name)
	}
}

func (name NotificationName) Bind(c *gin.Context) (interface{}, error) {
	switch name {
	case SendWelcomeMail:
		req := models.SendWelcomeMail{}
		err := c.ShouldBind(&req)
		return req, err
	default:
		return nil, fmt.Errorf("bind for %v, not implemented", name)
	}
}
