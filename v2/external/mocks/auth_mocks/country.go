package auth_mocks

import (
	"fmt"

	"github.com/vesicash/notifications-ms/v2/external/external_models"
	"github.com/vesicash/notifications-ms/v2/utility"
)

var (
	Country *external_models.Country
)

func GetCountry(logger *utility.Logger, idata interface{}) (external_models.Country, error) {

	var (
		outBoundResponse external_models.GetCountryResponse
	)

	_, ok := idata.(external_models.GetCountryModel)
	if !ok {
		logger.Error("get Country", idata, "request data format error")
		return outBoundResponse.Data, fmt.Errorf("request data format error")
	}

	if Country == nil {
		logger.Error("get Country", Country, "Country not provided")
		return external_models.Country{}, fmt.Errorf("country not provided")
	}

	logger.Info("get Country", Country, "Country found")
	return *Country, nil
}
