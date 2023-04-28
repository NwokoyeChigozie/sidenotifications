package health

import (
	"net/http"

	"github.com/gin-gonic/gin"
	"github.com/go-playground/validator/v10"
	"github.com/vesicash/notifications-ms/external/request"
	"github.com/vesicash/notifications-ms/pkg/repository/storage/postgresql"
	"github.com/vesicash/notifications-ms/utility"
)

type Controller struct {
	Db        postgresql.Databases
	Validator *validator.Validate
	Logger    *utility.Logger
	ExtReq    request.ExternalRequest
}

func (base *Controller) Get(c *gin.Context) {
	base.Logger.Info("ping successfull")
	rd := utility.BuildSuccessResponse(http.StatusOK, "ping successful", gin.H{"payment": "payment object"})
	c.JSON(http.StatusOK, rd)

}
