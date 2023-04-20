package ip_api_mocks

import (
	"fmt"

	"github.com/vesicash/notifications-ms/v2/external/external_models"
	"github.com/vesicash/notifications-ms/v2/utility"
)

func ResolveIp(logger *utility.Logger, idata interface{}) (external_models.ResolveIpResponse, error) {

	var (
		outBoundResponse external_models.ResolveIpResponse
	)

	ip, ok := idata.(string)
	if !ok {
		logger.Error("ip-api resolve ip", idata, "request data format error")
		return outBoundResponse, fmt.Errorf("request data format error")
	}

	logger.Info("ip-api resolve ip", ip)

	return external_models.ResolveIpResponse{
		Status:      "success",
		Country:     "nigeria",
		CountryCode: "NGN",
		City:        "owerri",
	}, nil
}
