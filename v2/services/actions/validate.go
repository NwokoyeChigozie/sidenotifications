package actions

import (
	"encoding/json"
	"fmt"

	"github.com/gin-gonic/gin"
	"github.com/go-playground/validator/v10"
	"github.com/vesicash/notifications-ms/v2/external/request"
	"github.com/vesicash/notifications-ms/v2/pkg/repository/storage/postgresql"
	"github.com/vesicash/notifications-ms/v2/services/names"
	"github.com/vesicash/notifications-ms/v2/utility"
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

func ValidateNotificationRequestMap(c *gin.Context, extReq request.ExternalRequest, db postgresql.Databases, v *validator.Validate, name string) (interface{}, error) {
	var (
		actionName = names.NotificationName(name)
		request    = map[string]interface{}{}
	)

	req, err := Bind(c, actionName)
	if err != nil {
		return req, err
	}

	fmt.Println(req)
	reqBytes, err := json.Marshal(req)
	if err != nil {
		return req, err
	}

	err = json.Unmarshal(reqBytes, &request)
	if err != nil {
		return req, err
	}

	fmt.Println("first", request)

	currentStruct, _ := Model(actionName)
	validateRules, err := postgresql.GetValidationRules(currentStruct, "validate")
	if err != nil {
		return req, err
	}
	pgValidateRules, err := postgresql.GetValidationRules(currentStruct, "pgvalidate")
	if err != nil {
		return req, err
	}

	fmt.Println("rules", validateRules)

	errs := v.ValidateMap(request, validateRules)
	if len(errs) > 0 {
		return req, fmt.Errorf("%v", errs)
	}

	vr := postgresql.ValidateRequestM{Logger: extReq.Logger, Test: extReq.Test}
	err = vr.ValidateRequestMap(request, pgValidateRules)
	if err != nil {
		return req, err
	}

	fmt.Println("complete")
	return req, nil
}
