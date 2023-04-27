package payment

import (
	"fmt"

	"github.com/vesicash/notifications-ms/v2/external/external_models"
)

func (r *RequestObj) CreatePayment() (external_models.Payment, error) {

	var (
		outBoundResponse external_models.CreatePaymentResponse
		logger           = r.Logger
		idata            = r.RequestData
	)

	fdata, ok := idata.(external_models.CreatePaymentRequestWithToken)
	if !ok {
		logger.Error("create payment", idata, "request data format error")
		return outBoundResponse.Data, fmt.Errorf("request data format error")
	}

	headers := map[string]string{
		"Content-Type":  "application/json",
		"Authorization": "Bearer " + fdata.Token,
	}

	data := external_models.CreatePaymentRequest{
		TransactionID: fdata.TransactionID,
		TotalAmount:   fdata.TotalAmount,
		ShippingFee:   fdata.ShippingFee,
		BrokerCharge:  fdata.BrokerCharge,
		EscrowCharge:  fdata.EscrowCharge,
		Currency:      fdata.Currency,
	}

	logger.Info("create payment", data)
	err := r.getNewSendRequestObject(data, headers, "").SendRequest(&outBoundResponse)
	if err != nil {
		logger.Error("create payment", outBoundResponse, err.Error())
		return outBoundResponse.Data, err
	}
	logger.Info("create payment", outBoundResponse)

	return outBoundResponse.Data, nil
}
