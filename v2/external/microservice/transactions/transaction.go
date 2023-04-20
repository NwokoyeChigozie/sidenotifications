package transactions

import (
	"fmt"

	"github.com/vesicash/notifications-ms/v2/external/external_models"
	"github.com/vesicash/notifications-ms/v2/internal/config"
)

func (r *RequestObj) UpdateTransactionAmountPaid() (external_models.Transaction, error) {

	var (
		appKey           = config.GetConfig().App.Key
		outBoundResponse external_models.UpdateTransactionAmountPaidResponse
		logger           = r.Logger
		idata            = r.RequestData
	)

	data, ok := idata.(external_models.UpdateTransactionAmountPaidRequest)
	if !ok {
		logger.Error("update transaction amount paid", idata, "request data format error")
		return outBoundResponse.Data, fmt.Errorf("request data format error")
	}

	headers := map[string]string{
		"Content-Type": "application/json",
		"v-app":        appKey,
	}

	logger.Info("update transaction amount paid", data)
	err := r.getNewSendRequestObject(data, headers, "").SendRequest(&outBoundResponse)
	if err != nil {
		logger.Error("update wallet", outBoundResponse, err.Error())
		return outBoundResponse.Data, err
	}
	logger.Info("update transaction amount paid", outBoundResponse)

	return outBoundResponse.Data, nil
}

func (r *RequestObj) TransactionUpdateStatus() (interface{}, error) {
	var (
		outBoundResponse map[string]interface{}
		logger           = r.Logger
		idata            = r.RequestData
	)
	data, ok := idata.(external_models.UpdateTransactionStatusRequest)
	if !ok {
		logger.Error("transaction update status", idata, "request data format error")
		return nil, fmt.Errorf("request data format error")
	}
	accessToken, err := r.getAccessTokenObject().GetAccessToken()
	if err != nil {
		logger.Error("transaction update status", outBoundResponse, err.Error())
		return nil, err
	}

	headers := map[string]string{
		"Content-Type":  "application/json",
		"v-private-key": accessToken.PrivateKey,
		"v-public-key":  accessToken.PublicKey,
	}

	logger.Info("transaction update status", data)
	err = r.getNewSendRequestObject(data, headers, "").SendRequest(&outBoundResponse)
	if err != nil {
		logger.Error("transaction update status", outBoundResponse, err.Error())
		return nil, err
	}
	logger.Info("transaction update status", outBoundResponse)

	return nil, nil
}

func (r *RequestObj) BuyerSatisfied() (interface{}, error) {
	var (
		outBoundResponse map[string]interface{}
		logger           = r.Logger
		idata            = r.RequestData
	)
	data, ok := idata.(external_models.OnlyTransactionIDRequiredRequest)
	if !ok {
		logger.Error("buyer satisfied", idata, "request data format error")
		return nil, fmt.Errorf("request data format error")
	}
	accessToken, err := r.getAccessTokenObject().GetAccessToken()
	if err != nil {
		logger.Error("buyer satisfied", outBoundResponse, err.Error())
		return nil, err
	}

	headers := map[string]string{
		"Content-Type":  "application/json",
		"v-private-key": accessToken.PrivateKey,
		"v-public-key":  accessToken.PublicKey,
	}

	logger.Info("buyer satisfied", data)
	err = r.getNewSendRequestObject(data, headers, "").SendRequest(&outBoundResponse)
	if err != nil {
		logger.Error("buyer satisfied", outBoundResponse, err.Error())
		return nil, err
	}
	logger.Info("buyer satisfied", outBoundResponse)

	return nil, nil
}
