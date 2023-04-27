package payment_mocks

import (
	"fmt"

	"github.com/vesicash/notifications-ms/v2/external/external_models"
	"github.com/vesicash/notifications-ms/v2/utility"
)

func WalletTransfer(logger *utility.Logger, idata interface{}) (interface{}, error) {

	var (
		outBoundResponse external_models.WalletTransferResponse
	)

	data, ok := idata.(external_models.WalletTransferRequest)
	if !ok {
		logger.Error("wallet transfer", idata, "request data format error")
		return outBoundResponse, fmt.Errorf("request data format error")
	}

	logger.Info("wallet transfer", data)

	return outBoundResponse, nil
}

func DebitWallet(logger *utility.Logger, idata interface{}) (external_models.WalletBalance, error) {

	var (
		outBoundResponse external_models.WalletBalanceResponse
	)

	data, ok := idata.(external_models.DebitWalletRequest)
	if !ok {
		logger.Error("debit wallet", idata, "request data format error")
		return outBoundResponse.Data, fmt.Errorf("request data format error")
	}

	logger.Info("debit wallet", data)

	return external_models.WalletBalance{
		ID:        100,
		AccountID: data.BusinessID,
		Available: 1000000,
		Currency:  data.Currency,
	}, nil
}

func CreditWallet(logger *utility.Logger, idata interface{}) (external_models.WalletBalance, error) {

	var (
		outBoundResponse external_models.WalletBalanceResponse
	)

	data, ok := idata.(external_models.CreditWalletRequest)
	if !ok {
		logger.Error("credit wallet", idata, "request data format error")
		return outBoundResponse.Data, fmt.Errorf("request data format error")
	}

	logger.Info("credit wallet", data)

	return external_models.WalletBalance{
		ID:        100,
		AccountID: data.BusinessID,
		Available: 1000000,
		Currency:  data.Currency,
	}, nil
}
