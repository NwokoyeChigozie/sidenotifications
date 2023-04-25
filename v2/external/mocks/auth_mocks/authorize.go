package auth_mocks

import (
	"fmt"
	"net/http"

	"github.com/vesicash/notifications-ms/v2/external/external_models"
	"github.com/vesicash/notifications-ms/v2/utility"
)

var (
	Authorize *external_models.Authorize
)

func GetAuthorize(logger *utility.Logger, idata interface{}) (external_models.GetAuthorizeResponse, error) {

	_, ok := idata.(external_models.GetAuthorizeModel)
	if !ok {
		logger.Error("get authorize", idata, "request data format error")
		return external_models.GetAuthorizeResponse{
			Status:  "error",
			Code:    http.StatusBadRequest,
			Message: "request data format error",
			Data:    external_models.Authorize{},
		}, fmt.Errorf("request data format error")
	}

	if Authorize == nil {
		logger.Error("get authorize", UsersCredential, "authorize not provided")
		return external_models.GetAuthorizeResponse{
			Status:  "error",
			Code:    http.StatusBadRequest,
			Message: "authorize not provided",
			Data:    external_models.Authorize{},
		}, fmt.Errorf("authorize not provided")
	}

	logger.Info("get authorize", UsersCredential, "authorize found")
	return external_models.GetAuthorizeResponse{
		Status:  "success",
		Code:    http.StatusOK,
		Message: "success",
		Data:    *Authorize,
	}, nil
}

func CreateAuthorize(logger *utility.Logger, idata interface{}) (external_models.GetAuthorizeResponse, error) {

	_, ok := idata.(external_models.CreateAuthorizeModel)
	if !ok {
		logger.Error("create authorize", idata, "request data format error")
		return external_models.GetAuthorizeResponse{
			Status:  "error",
			Code:    http.StatusBadRequest,
			Message: "request data format error",
			Data:    external_models.Authorize{},
		}, fmt.Errorf("request data format error")
	}

	if Authorize == nil {
		logger.Error("create authorize", UsersCredential, "authorize not provided")
		return external_models.GetAuthorizeResponse{
			Status:  "error",
			Code:    http.StatusBadRequest,
			Message: "authorize not provided",
			Data:    external_models.Authorize{},
		}, fmt.Errorf("authorize not provided")
	}

	logger.Info("create authorize", UsersCredential, "authorize found")
	return external_models.GetAuthorizeResponse{
		Status:  "success",
		Code:    http.StatusOK,
		Message: "success",
		Data:    *Authorize,
	}, nil

}

func UpdateAuthorize(logger *utility.Logger, idata interface{}) (external_models.GetAuthorizeResponse, error) {

	_, ok := idata.(external_models.UpdateAuthorizeModel)
	if !ok {
		logger.Error("update authorize", idata, "request data format error")
		return external_models.GetAuthorizeResponse{
			Status:  "error",
			Code:    http.StatusBadRequest,
			Message: "request data format error",
			Data:    external_models.Authorize{},
		}, fmt.Errorf("request data format error")
	}

	if Authorize == nil {
		logger.Error("update authorize", UsersCredential, "authorize not provided")
		return external_models.GetAuthorizeResponse{
			Status:  "error",
			Code:    http.StatusBadRequest,
			Message: "authorize not provided",
			Data:    external_models.Authorize{},
		}, fmt.Errorf("authorize not provided")
	}

	logger.Info("update authorize", UsersCredential, "authorize found")
	return external_models.GetAuthorizeResponse{
		Status:  "success",
		Code:    http.StatusOK,
		Message: "success",
		Data:    *Authorize,
	}, nil
}
