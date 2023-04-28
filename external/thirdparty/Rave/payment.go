package rave

import (
	"fmt"

	"github.com/vesicash/notifications-ms/external/external_models"
	"github.com/vesicash/notifications-ms/internal/config"
)

func (r *RequestObj) RaveInitPayment() (external_models.RaveInitPaymentResponse, error) {

	var (
		outBoundResponse external_models.RaveInitPaymentResponse
		logger           = r.Logger
		idata            = r.RequestData
	)

	headers := map[string]string{
		"Content-Type":  "application/json",
		"Authorization": "Bearer " + config.GetConfig().Rave.SecretKey,
	}

	data, ok := idata.(external_models.RaveInitPaymentRequest)
	if !ok {
		logger.Error("init payment rave", idata, "request data format error")
		return external_models.RaveInitPaymentResponse{}, fmt.Errorf("request data format error")
	}

	err := r.getNewSendRequestObject(data, headers, "").SendRequest(&outBoundResponse)
	if err != nil {
		logger.Error("init payment rave", outBoundResponse, err.Error())
		return external_models.RaveInitPaymentResponse{}, err
	}
	logger.Info("init payment rave", outBoundResponse)

	return outBoundResponse, nil
}

func (r *RequestObj) RaveReserveAccount() (external_models.RaveReserveAccountResponseData, error) {

	var (
		outBoundResponse external_models.RaveReserveAccountResponse
		logger           = r.Logger
		idata            = r.RequestData
	)

	headers := map[string]string{
		"Content-Type":  "application/json",
		"Authorization": "Bearer " + config.GetConfig().Rave.SecretKey,
	}

	data, ok := idata.(external_models.RaveReserveAccountRequest)
	if !ok {
		logger.Error("rave reserve account", idata, "request data format error")
		return outBoundResponse.Data, fmt.Errorf("request data format error")
	}

	err := r.getNewSendRequestObject(data, headers, "").SendRequest(&outBoundResponse)
	if err != nil {
		logger.Error("rave reserve account", outBoundResponse, err.Error())
		return outBoundResponse.Data, err
	}
	logger.Info("rave reserve account", outBoundResponse)

	return outBoundResponse.Data, nil
}

func (r *RequestObj) RaveVerifyTransactionByTxRef() (external_models.RaveVerifyTransactionResponseData, error) {

	var (
		outBoundResponse external_models.RaveVerifyTransactionResponse
		logger           = r.Logger
		idata            = r.RequestData
	)

	headers := map[string]string{
		"Content-Type":  "application/json",
		"Authorization": "Bearer " + config.GetConfig().Rave.SecretKey,
	}

	data, ok := idata.(string)
	if !ok {
		logger.Error("rave verify transaction by tx_ref", idata, "request data format error")
		return outBoundResponse.Data, fmt.Errorf("request data format error")
	}

	err := r.getNewSendRequestObject(data, headers, data).SendRequest(&outBoundResponse)
	if err != nil {
		logger.Error("rave verify transaction by tx_ref", outBoundResponse, err.Error())
		return outBoundResponse.Data, err
	}
	logger.Info("rave verify transaction by tx_ref", outBoundResponse)

	return outBoundResponse.Data, nil
}

func (r *RequestObj) RaveChargeCard() (external_models.RaveVerifyTransactionResponseData, error) {

	var (
		outBoundResponse external_models.RaveVerifyTransactionResponse
		logger           = r.Logger
		idata            = r.RequestData
	)

	headers := map[string]string{
		"Content-Type":  "application/json",
		"Authorization": "Bearer " + config.GetConfig().Rave.SecretKey,
	}

	data, ok := idata.(external_models.RaveChargeCardRequest)
	if !ok {
		logger.Error("rave charge card", idata, "request data format error")
		return outBoundResponse.Data, fmt.Errorf("request data format error")
	}

	err := r.getNewSendRequestObject(data, headers, "").SendRequest(&outBoundResponse)
	if err != nil {
		logger.Error("rave charge card", outBoundResponse, err.Error())
		return outBoundResponse.Data, err
	}
	logger.Info("rave charge card", outBoundResponse)

	return outBoundResponse.Data, nil
}
