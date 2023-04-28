package payment

import (
	"fmt"

	"github.com/vesicash/notifications-ms/external/external_models"
	"github.com/vesicash/notifications-ms/internal/config"
)

func (r *RequestObj) WalletTransfer() (interface{}, error) {

	var (
		appKey           = config.GetConfig().App.Key
		outBoundResponse external_models.WalletTransferResponse
		logger           = r.Logger
		idata            = r.RequestData
	)

	data, ok := idata.(external_models.WalletTransferRequest)
	if !ok {
		logger.Error("wallet transfer", idata, "request data format error")
		return outBoundResponse, fmt.Errorf("request data format error")
	}

	headers := map[string]string{
		"Content-Type": "application/json",
		"v-app":        appKey,
	}

	logger.Info("wallet transfer", data)
	err := r.getNewSendRequestObject(data, headers, "").SendRequest(&outBoundResponse)
	if err != nil {
		logger.Error("wallet transfer", outBoundResponse, err.Error())
		return outBoundResponse, err
	}
	logger.Info("wallet transfer", outBoundResponse)

	return outBoundResponse, nil
}

func (r *RequestObj) DebitWallet() (external_models.WalletBalance, error) {

	var (
		appKey           = config.GetConfig().App.Key
		outBoundResponse external_models.WalletBalanceResponse
		logger           = r.Logger
		idata            = r.RequestData
	)

	data, ok := idata.(external_models.DebitWalletRequest)
	if !ok {
		logger.Error("debit wallet", idata, "request data format error")
		return outBoundResponse.Data, fmt.Errorf("request data format error")
	}

	headers := map[string]string{
		"Content-Type": "application/json",
		"v-app":        appKey,
	}

	logger.Info("debit wallet", data)
	err := r.getNewSendRequestObject(data, headers, "").SendRequest(&outBoundResponse)
	if err != nil {
		logger.Error("debit wallet", outBoundResponse, err.Error())
		return outBoundResponse.Data, err
	}
	logger.Info("debit wallet", outBoundResponse)

	return outBoundResponse.Data, nil
}

func (r *RequestObj) CreditWallet() (external_models.WalletBalance, error) {

	var (
		appKey           = config.GetConfig().App.Key
		outBoundResponse external_models.WalletBalanceResponse
		logger           = r.Logger
		idata            = r.RequestData
	)

	data, ok := idata.(external_models.CreditWalletRequest)
	if !ok {
		logger.Error("credit wallet", idata, "request data format error")
		return outBoundResponse.Data, fmt.Errorf("request data format error")
	}

	headers := map[string]string{
		"Content-Type": "application/json",
		"v-app":        appKey,
	}

	logger.Info("credit wallet", data)
	err := r.getNewSendRequestObject(data, headers, "").SendRequest(&outBoundResponse)
	if err != nil {
		logger.Error("credit wallet", outBoundResponse, err.Error())
		return outBoundResponse.Data, err
	}
	logger.Info("credit wallet", outBoundResponse)

	return outBoundResponse.Data, nil
}
