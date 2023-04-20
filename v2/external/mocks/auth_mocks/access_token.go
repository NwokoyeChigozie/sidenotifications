package auth_mocks

import (
	"fmt"
	"strconv"

	"github.com/vesicash/notifications-ms/v2/external/external_models"
	"github.com/vesicash/notifications-ms/v2/utility"
)

var (
	AccessToken external_models.AccessToken
)

func GetAccessToken(logger *utility.Logger) (external_models.AccessToken, error) {
	logger.Info("get access tokens", "get access tokens called")
	return AccessToken, nil
}

func GetAccessTokenByKey(logger *utility.Logger, idata interface{}) (external_models.AccessToken, error) {
	var (
		outBoundResponse external_models.GetAccessTokenModel
	)

	data, ok := idata.(string)
	if !ok {
		logger.Error("get access token by key", idata, "request data format error")
		return outBoundResponse.Data, fmt.Errorf("request data format error")
	}

	logger.Info("get access token by key", outBoundResponse)

	return external_models.AccessToken{
		ID:            0,
		AccountID:     AccessToken.AccountID,
		PublicKey:     data,
		PrivateKey:    data,
		IsLive:        true,
		IsTermsAgreed: true,
	}, nil
}

func GetAccessTokenByBusinessID(logger *utility.Logger, idata interface{}) (external_models.AccessToken, error) {
	var (
		outBoundResponse external_models.GetAccessTokenModel
	)

	data, ok := idata.(string)
	if !ok {
		logger.Error("get access token by business id", idata, "request data format error")
		return outBoundResponse.Data, fmt.Errorf("request data format error")
	}

	logger.Info("get access token by business id", outBoundResponse, data)
	accountID, _ := strconv.Atoi(data)
	AccessToken.AccountID = accountID
	return AccessToken, nil
}
