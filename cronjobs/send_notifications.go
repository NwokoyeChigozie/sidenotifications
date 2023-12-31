package cronjobs

import (
	"fmt"

	"github.com/vesicash/notifications-ms/external/request"
	"github.com/vesicash/notifications-ms/internal/models"
	"github.com/vesicash/notifications-ms/pkg/repository/storage/postgresql"
	"github.com/vesicash/notifications-ms/services/actions"
)

func SendNotifications(extReq request.ExternalRequest, db postgresql.Databases) {
	notificationRecord := models.NotificationRecord{}
	notificationRecords, err := notificationRecord.GetSomeUnsentNotifications(db.Notifications, 200)
	if err != nil {
		extReq.Logger.Error("error getting notificatin records: ", err.Error())
		return
	}
	fmt.Println("number of records found", len(notificationRecords))

	for _, record := range notificationRecords {
		actions.Send(extReq, db, &record)
	}
}
