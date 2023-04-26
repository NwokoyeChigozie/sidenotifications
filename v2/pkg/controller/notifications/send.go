package notifications

import (
	"net/http"
	"strings"

	"github.com/gin-gonic/gin"
	"github.com/vesicash/notifications-ms/v2/services/actions"
	"github.com/vesicash/notifications-ms/v2/utility"
)

func (base *Controller) SendNotification(c *gin.Context) {
	var (
		name = strings.ToLower(c.Param("name"))
	)

	req, err := actions.ValidateNotificationRequest(c, base.ExtReq, base.Db, base.Validator, name)
	if err != nil {
		rd := utility.BuildErrorResponse(http.StatusBadRequest, "error", err.Error(), err, nil)
		c.JSON(http.StatusBadRequest, rd)
		return
	}

	notificationRecord, err := actions.AddNotificationToDB(base.ExtReq, base.Db, name, req)
	if err != nil {
		rd := utility.BuildErrorResponse(http.StatusBadRequest, "error", err.Error(), err, nil)
		c.JSON(http.StatusBadRequest, rd)
		return
	}

	err = actions.Send(base.ExtReq, base.Db, &notificationRecord)
	if err != nil {
		rd := utility.BuildErrorResponse(http.StatusBadRequest, "error", err.Error(), err, nil)
		c.JSON(http.StatusBadRequest, rd)
		return
	}

	rd := utility.BuildSuccessResponse(http.StatusOK, "successful", nil)
	c.JSON(http.StatusOK, rd)

}
