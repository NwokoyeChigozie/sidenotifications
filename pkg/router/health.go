package router

import (
	"fmt"

	"github.com/gin-gonic/gin"
	"github.com/go-playground/validator/v10"
	"github.com/vesicash/notifications-ms/external/request"
	"github.com/vesicash/notifications-ms/pkg/controller/health"
	"github.com/vesicash/notifications-ms/pkg/repository/storage/postgresql"
	"github.com/vesicash/notifications-ms/utility"
)

func Health(r *gin.Engine, ApiVersion string, validator *validator.Validate, db postgresql.Databases, logger *utility.Logger) *gin.Engine {
	extReq := request.ExternalRequest{Logger: logger, Test: false}
	health := health.Controller{Db: db, Validator: validator, Logger: logger, ExtReq: extReq}

	healthUrl := r.Group(fmt.Sprintf("%v", ApiVersion))
	{
		healthUrl.GET("/health", health.Get)
	}
	return r
}
