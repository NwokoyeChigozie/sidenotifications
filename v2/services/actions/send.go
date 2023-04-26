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
	default:
		return handleNotificationErr(extReq, db, notification, fmt.Errorf("send for %v, not implemented", notification.Name))
	}

	return handleNotificationErr(extReq, db, notification, err)
}
