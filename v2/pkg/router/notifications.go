package router

import (
	"fmt"

	"github.com/gin-gonic/gin"
	"github.com/go-playground/validator/v10"
	"github.com/vesicash/notifications-ms/v2/external/request"
	"github.com/vesicash/notifications-ms/v2/pkg/controller/notifications"
	"github.com/vesicash/notifications-ms/v2/pkg/middleware"
	"github.com/vesicash/notifications-ms/v2/pkg/repository/storage/postgresql"
	"github.com/vesicash/notifications-ms/v2/utility"
)

func Notifications(r *gin.Engine, ApiVersion string, validator *validator.Validate, db postgresql.Databases, logger *utility.Logger) *gin.Engine {
	extReq := request.ExternalRequest{Logger: logger, Test: false}
	notifications := notifications.Controller{Db: db, Validator: validator, Logger: logger, ExtReq: extReq}

	notificationsApiUrl := r.Group(fmt.Sprintf("%v", ApiVersion), middleware.Authorize(db, extReq, middleware.ApiType))
	{
		notificationsApiUrl.POST("/send/:name", notifications.SendNotification)

	}
	return r
}
