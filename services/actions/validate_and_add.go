package actions

import (
	"encoding/json"
	"fmt"

	"github.com/gin-gonic/gin"
	"github.com/go-playground/validator/v10"
	"github.com/vesicash/notifications-ms/external/request"
	"github.com/vesicash/notifications-ms/internal/models"
	"github.com/vesicash/notifications-ms/pkg/repository/storage/postgresql"
	"github.com/vesicash/notifications-ms/services/names"
	"github.com/vesicash/notifications-ms/utility"
)

func ValidateNotificationRequest(c *gin.Context, extReq request.ExternalRequest, db postgresql.Databases, v *validator.Validate, name string) (interface{}, error) {
	var (
		actionName = names.NotificationName(name)
	)

	req, err := Bind(c, actionName)
	if err != nil {
		return req, err
	}

	fmt.Println(req)

	err = v.Struct(req)
	if err != nil {
		return req, fmt.Errorf("%v", utility.ValidationResponse(err, v))
	}

	vr := postgresql.ValidateRequestM{Logger: extReq.Logger, Test: extReq.Test}
	err = vr.ValidateRequest(req)
	if err != nil {
		return req, err
	}

	return req, nil
}

func AddNotificationToDB(extReq request.ExternalRequest, db postgresql.Databases, name string, data interface{}) (models.NotificationRecord, error) {
	dataByte, err := json.Marshal(data)
	if err != nil {
		return models.NotificationRecord{}, err
	}

	notificationRecord := models.NotificationRecord{
		Name:      name,
		Data:      string(dataByte),
		Attempts:  0,
		Sent:      false,
		Abandoned: false,
	}
	err = notificationRecord.CreateNotificationRecord(db.Notifications)
	if err != nil {
		return models.NotificationRecord{}, err
	}

	return notificationRecord, nil
}
