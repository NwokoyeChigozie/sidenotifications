package auth_mocks

import (
	"fmt"

	"github.com/vesicash/notifications-ms/v2/external/external_models"
	"github.com/vesicash/notifications-ms/v2/utility"
)

func GetBank(logger *utility.Logger, idata interface{}) (external_models.Bank, error) {

	var (
		outBoundResponse external_models.GetBankResponse
	)

	data, ok := idata.(external_models.GetBankRequest)
	if !ok {
		logger.Error("get bank", idata, "request data format error")
		return outBoundResponse.Data, fmt.Errorf("request data format error")
	}
	logger.Info("get bank", outBoundResponse)

	return external_models.Bank{
		ID:      data.ID,
		Code:    "221",
		Name:    "vesicash bank",
		Country: "NG",
	}, nil
}
