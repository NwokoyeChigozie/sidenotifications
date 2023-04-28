package payment

import (
	"fmt"

	"github.com/vesicash/notifications-ms/external/external_models"
)

func (r *RequestObj) RequestManualRefund() (map[string]interface{}, error) {
	var (
		outBoundResponse map[string]interface{}
		logger           = r.Logger
		idata            = r.RequestData
	)
	data, ok := idata.(string)
	if !ok {
		logger.Error("request manual refund", idata, "request data format error")
		return outBoundResponse, fmt.Errorf("request data format error")
	}
	accessToken, err := r.getAccessTokenObject().GetAccessToken()
	if err != nil {
		logger.Error("request manual refund", outBoundResponse, err.Error())
		return outBoundResponse, err
	}

	headers := map[string]string{
		"Content-Type":  "application/json",
		"v-private-key": accessToken.PrivateKey,
		"v-public-key":  accessToken.PublicKey,
	}

	reqData := external_models.TransactionIDRequestModel{
		TransactionId: data,
	}

	logger.Info("request manual refund", data)
	err = r.getNewSendRequestObject(reqData, headers, "").SendRequest(&outBoundResponse)
	if err != nil {
		logger.Error("request manual refund", outBoundResponse, err.Error())
		return outBoundResponse, err
	}
	logger.Info("request manual refund", outBoundResponse)

	return outBoundResponse, nil
}
