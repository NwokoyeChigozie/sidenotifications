package transactions_mocks

import (
	"fmt"

	"github.com/vesicash/notifications-ms/v2/external/external_models"
	"github.com/vesicash/notifications-ms/v2/utility"
)

func CreateExchangeTransaction(logger *utility.Logger, idata interface{}) (interface{}, error) {
	var (
		outBoundResponse map[string]interface{}
	)
	data, ok := idata.(external_models.CreateExchangeTransactionRequest)
	if !ok {
		logger.Error("create exchange transaction", idata, "request data format error")
		return nil, fmt.Errorf("request data format error")
	}

	logger.Info("create exchange transaction", outBoundResponse, data)

	return nil, nil
}

func GetRateByID(logger *utility.Logger, idata interface{}) (external_models.Rate, error) {
	var (
		outBoundResponse external_models.RateResponse
	)
	data, ok := idata.(int)
	if !ok {
		logger.Error("get rate by id", idata, "request data format error")
		return outBoundResponse.Data, fmt.Errorf("request data format error")
	}

	logger.Info("get rate by id", outBoundResponse)

	return external_models.Rate{
		ID:           int64(data),
		FromCurrency: "USD",
		ToCurrency:   "NGN",
		Amount:       0.7,
	}, nil
}
