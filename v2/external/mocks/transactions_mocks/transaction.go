package transactions_mocks

import (
	"fmt"

	"github.com/vesicash/notifications-ms/v2/external/external_models"
	"github.com/vesicash/notifications-ms/v2/utility"
)

func UpdateTransactionAmountPaid(logger *utility.Logger, idata interface{}) (external_models.Transaction, error) {

	var (
		outBoundResponse external_models.UpdateTransactionAmountPaidResponse
	)

	data, ok := idata.(external_models.UpdateTransactionAmountPaidRequest)
	if !ok {
		logger.Error("update transaction amount paid", idata, "request data format error")
		return outBoundResponse.Data, fmt.Errorf("request data format error")
	}
	logger.Info("update transaction amount paid", outBoundResponse)

	return external_models.Transaction{
		TransactionID: data.TransactionID,
		Amount:        data.Amount,
	}, nil
}

func TransactionUpdateStatus(logger *utility.Logger, idata interface{}) (interface{}, error) {
	var (
		outBoundResponse map[string]interface{}
	)
	data, ok := idata.(external_models.UpdateTransactionStatusRequest)
	if !ok {
		logger.Error("transaction update status", idata, "request data format error")
		return nil, fmt.Errorf("request data format error")
	}

	logger.Info("transaction update status", outBoundResponse, data)

	return nil, nil
}

func BuyerSatisfied(logger *utility.Logger, idata interface{}) (interface{}, error) {
	var (
		outBoundResponse map[string]interface{}
	)
	data, ok := idata.(external_models.OnlyTransactionIDRequiredRequest)
	if !ok {
		logger.Error("buyer satisfied", idata, "request data format error")
		return nil, fmt.Errorf("request data format error")
	}

	logger.Info("buyer satisfied", outBoundResponse, data)

	return nil, nil
}
