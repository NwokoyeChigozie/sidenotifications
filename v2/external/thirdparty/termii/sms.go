package termii

import (
	"bytes"
	"encoding/json"
	"fmt"
	"mime/multipart"

	"github.com/vesicash/notifications-ms/v2/external/external_models"
	"github.com/vesicash/notifications-ms/v2/internal/config"
)

func (r *RequestObj) TermiiSendSMS() (external_models.TermiiSendSMSReponse, error) {

	var (
		outBoundResponse external_models.TermiiSendSMSReponse
		logger           = r.Logger
		idata            = r.RequestData
		termiiConfig     = config.GetConfig().Termii
	)

	data, ok := idata.(external_models.TermiiSendSMSRequest)
	if !ok {
		logger.Error("termii send sms", idata, "request data format error")
		return outBoundResponse, fmt.Errorf("request data format error")
	}

	payload := &bytes.Buffer{}
	writer := multipart.NewWriter(payload)
	_ = writer.WriteField("api_key", termiiConfig.ApiKey)
	_ = writer.WriteField("from", data.From)
	_ = writer.WriteField("type", data.Type)
	_ = writer.WriteField("channel", data.Channel)
	_ = writer.WriteField("to", data.To)
	_ = writer.WriteField("sms", data.Sms)
	err := writer.Close()
	if err != nil {
		logger.Error("termii send sms", outBoundResponse, err.Error())
		return outBoundResponse, err
	}

	headers := map[string]string{
		"Content-Type": writer.FormDataContentType(),
	}

	r.RequestBody = payload

	dataByte, _ := json.Marshal(data)
	logger.Info("termii send sms", string(dataByte), payload)
	err = r.getNewSendRequestObject(nil, headers, "").SendRequest(&outBoundResponse)
	if err != nil {
		logger.Error("termii send sms", outBoundResponse, err.Error())
		return outBoundResponse, err
	}
	logger.Info("termii send sms", outBoundResponse)

	return outBoundResponse, nil
}
