package payment_mocks

import (
	"fmt"

	"github.com/vesicash/notifications-ms/v2/external/external_models"
	"github.com/vesicash/notifications-ms/v2/utility"
)

var (
	Payment *external_models.Payment
)

func CreatePayment(logger *utility.Logger, idata interface{}) (external_models.Payment, error) {

	_, ok := idata.(external_models.CreatePaymentRequestWithToken)
	if !ok {
		logger.Error("create payment", idata, "request data format error")
		return external_models.Payment{}, fmt.Errorf("request data format error")
	}

	if Payment == nil {
		logger.Error("create payment", Payment, "payment not provided")
		return external_models.Payment{}, fmt.Errorf("payment not provided")
	}

	return *Payment, nil
}
