package actions

import (
	"fmt"
	"time"

	"github.com/vesicash/notifications-ms/external/request"
	"github.com/vesicash/notifications-ms/internal/models"
	"github.com/vesicash/notifications-ms/pkg/repository/storage/postgresql"
	"github.com/vesicash/notifications-ms/services/names"
)

var (
	MaxAttempts   = 100
	RetryDuration = time.Minute * time.Duration(2)
)

func GetName(name string) names.NotificationName {
	return names.NotificationName(name)
}

func handleNotificationErr(extReq request.ExternalRequest, db postgresql.Databases, notification *models.NotificationRecord, err error) error {
	notification.Attempts += 1
	if err != nil {
		notification.Sent = false
		notification.AttemptAgain = int(time.Now().Add(RetryDuration).Unix())
		extReq.Logger.Error(fmt.Sprintf("sending %v failed, Error:%v", notification.Name, err.Error()))
	} else {
		notification.Sent = true
		extReq.Logger.Info(fmt.Sprintf("sending %v successful", notification.Name))
	}

	if notification.Attempts >= MaxAttempts {
		notification.Abandoned = true
		notification.AttemptAgain = 0
	}

	if updateErr := notification.UpdateAllFields(db.Notifications); updateErr != nil {
		return updateErr
	}

	return err
}
