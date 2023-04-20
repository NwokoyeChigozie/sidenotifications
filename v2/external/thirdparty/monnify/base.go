package monnify

import (
	"encoding/base64"
	"fmt"

	"github.com/vesicash/notifications-ms/v2/external"
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
	IsLiveMust   bool
}

var (
	JsonDecodeMethod    string = "json"
	PhpSerializerMethod string = "phpserializer"
)

func (r *RequestObj) getNewSendRequestObject(data interface{}, headers map[string]string, urlprefix string) *external.SendRequestObject {
	return external.GetNewSendRequestObject(r.Logger, r.Name, r.Path, r.Method, urlprefix, r.DecodeMethod, headers, r.SuccessCode, data)
}

func (r *RequestObj) getMonnifyLoginObject(isLiveMust bool) *RequestObj {
	var (
		config = config.GetConfig()
	)
	return &RequestObj{
		Name:         "monnify_login",
		Path:         fmt.Sprintf("%v/api/v1/auth/login", config.Monnify.MonnifyApi),
		Method:       "POST",
		SuccessCode:  200,
		DecodeMethod: JsonDecodeMethod,
		RequestData:  nil,
		Logger:       r.Logger,
		IsLiveMust:   isLiveMust,
	}
}
func getBase64Token(isLiveMust bool) string {
	var (
		monnifyConfig = config.GetConfig().Monnify
		token         = base64.StdEncoding.EncodeToString([]byte(fmt.Sprintf("%v:%v", monnifyConfig.MonnifyApiKey, monnifyConfig.MonnifySecret)))
	)
	if isLiveMust {
		return config.GetConfig().Monnify.MonnifyBase64Key
	}
	return token
}
