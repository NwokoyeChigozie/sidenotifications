package transactions_mocks

import (
	"fmt"

	"github.com/vesicash/notifications-ms/v2/external/external_models"
	"github.com/vesicash/notifications-ms/v2/utility"
)

func CreateActivityLog(logger *utility.Logger, idata interface{}) (interface{}, error) {

	var (
		outBoundResponse external_models.CreateActivityLogRequest
	)

	data, ok := idata.(external_models.UpdateTransactionAmountPaidRequest)
	if !ok {
		logger.Error("create activity log", idata, "request data format error")
		return nil, fmt.Errorf("request data format error")
	}

	logger.Info("create activity log", outBoundResponse, data)

	return nil, nil
}
