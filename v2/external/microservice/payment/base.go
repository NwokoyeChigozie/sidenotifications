package payment

import (
	"fmt"

	"github.com/vesicash/notifications-ms/v2/external"
	"github.com/vesicash/notifications-ms/v2/external/microservice/auth"
	"github.com/vesicash/notifications-ms/v2/internal/config"
	"github.com/vesicash/notifications-ms/v2/utility"
)

type RequestObj struct {
	Name         string
	Path         string
	Method       string
	SuccessCode  int
	RequestData  interface{}
	DecodeMethod string
	Logger       *utility.Logger
}

var (
	JsonDecodeMethod    string = "json"
	PhpSerializerMethod string = "phpserializer"
)

func (r *RequestObj) getNewSendRequestObject(data interface{}, headers map[string]string, urlprefix string) *external.SendRequestObject {
	return external.GetNewSendRequestObject(r.Logger, r.Name, r.Path, r.Method, urlprefix, r.DecodeMethod, headers, r.SuccessCode, data)
}

func (r *RequestObj) getAccessTokenObject() *auth.RequestObj {
	var (
		config = config.GetConfig()
	)
	return &auth.RequestObj{
		Name:         "get_access_token",
		Path:         fmt.Sprintf("%v/v2/get_access_token", config.Microservices.Auth),
		Method:       "GET",
		SuccessCode:  200,
		DecodeMethod: JsonDecodeMethod,
		RequestData:  nil,
		Logger:       r.Logger,
	}
}
