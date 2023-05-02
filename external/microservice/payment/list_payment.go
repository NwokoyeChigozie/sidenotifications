package payment

import (
	"fmt"

	"github.com/vesicash/notifications-ms/external/external_models"
)

func (r *RequestObj) ListPayment() (external_models.ListPayment, error) {
	var (
		outBoundResponse external_models.ListPaymentsResponse
		logger           = r.Logger
		idata            = r.RequestData
	)
	data, ok := idata.(string)
	if !ok {
		logger.Error("list payment by transaction id", idata, "request data format error")
		return outBoundResponse.Data.Payment, fmt.Errorf("request data format error")
	}
	accessToken, err := r.getAccessTokenObject().GetAccessToken()
	if err != nil {
		logger.Error("list payment by transaction id", outBoundResponse, err.Error())
		return outBoundResponse.Data.Payment, err
	}

	headers := map[string]string{
		"Content-Type":  "application/json",
		"v-private-key": accessToken.PrivateKey,
		"v-public-key":  accessToken.PublicKey,
	}

	logger.Info("list payment by transaction id", data)
	err = r.getNewSendRequestObject(data, headers, "/"+data).SendRequest(&outBoundResponse)
	if err != nil {
		logger.Error("list payment by transaction id", outBoundResponse, err.Error())
		return outBoundResponse.Data.Payment, err
	}
	logger.Info("list payment by transaction id", outBoundResponse)

	return outBoundResponse.Data.Payment, nil
}
