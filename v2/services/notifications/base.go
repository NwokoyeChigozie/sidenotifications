package notifications

import (
	"github.com/vesicash/notifications-ms/v2/external/request"
	"github.com/vesicash/notifications-ms/v2/internal/models"
	"github.com/vesicash/notifications-ms/v2/pkg/repository/storage/postgresql"
)

type NotificationObject struct {
	Notification *models.NotificationRecord
	ExtReq       request.ExternalRequest
	Db           postgresql.Databases
}

func NewNotificationObject(extReq request.ExternalRequest, db postgresql.Databases, notification *models.NotificationRecord) *NotificationObject {
	return &NotificationObject{
		ExtReq:       extReq,
		Db:           db,
		Notification: notification,
	}
}
