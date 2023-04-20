package auth_mocks

import (
	"fmt"

	"github.com/vesicash/notifications-ms/v2/external/external_models"
	"github.com/vesicash/notifications-ms/v2/utility"
)

var (
	BusinessProfile *external_models.BusinessProfile
)

func GetBusinessProfile(logger *utility.Logger, idata interface{}) (external_models.BusinessProfile, error) {

	var (
		outBoundResponse external_models.GetBusinessProfileResponse
	)

	_, ok := idata.(external_models.GetBusinessProfileModel)
	if !ok {
		logger.Error("get business profile", idata, "request data format error")
		return outBoundResponse.Data, fmt.Errorf("request data format error")
	}

	if BusinessProfile == nil {
		logger.Error("get BusinessProfile", BusinessProfile, "BusinessProfile not provided")
		return external_models.BusinessProfile{}, fmt.Errorf("BusinessProfile not provided")
	}

	logger.Info("get BusinessProfile", BusinessProfile, "BusinessProfile found")
	return *BusinessProfile, nil
}
