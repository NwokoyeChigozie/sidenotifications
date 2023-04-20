package notifications

import (
	"fmt"
	"net/http"
	"strings"

	"github.com/gin-gonic/gin"
	"github.com/vesicash/notifications-ms/v2/services/notifications"
	"github.com/vesicash/notifications-ms/v2/utility"
)

func (base *Controller) SendNotification(c *gin.Context) {
	var (
		name = strings.ToLower(c.Param("name"))
	)

	req, err := notifications.ValidateNotificationRequest(c, base.ExtReq, base.Db, base.Validator, name)
	if err != nil {
		rd := utility.BuildErrorResponse(http.StatusBadRequest, "error", err.Error(), err, nil)
		c.JSON(http.StatusBadRequest, rd)
		return
	}

	fmt.Println(req)

	//TODO save notification to db
	//TODO send notification

	rd := utility.BuildSuccessResponse(http.StatusOK, "successful", nil)
	c.JSON(http.StatusOK, rd)

}
