package notifications

import (
	"fmt"
	"strings"

	"github.com/vesicash/notifications-ms/external/external_models"
	"github.com/vesicash/notifications-ms/external/request"
	"github.com/vesicash/notifications-ms/internal/models"
	"github.com/vesicash/notifications-ms/pkg/repository/storage/postgresql"
	"github.com/vesicash/notifications-ms/utility"
)

type NotificationObject struct {
	Notification *models.NotificationRecord
	ExtReq       request.ExternalRequest
	Db           postgresql.Databases
}

func NewNotificationObject(extReq request.ExternalRequest, db postgresql.Databases, notification *models.NotificationRecord) *NotificationObject {
	return &NotificationObject{
		ExtReq:       extReq,
		Db:           db,
		Notification: notification,
	}
}

func ConvertToMapAndAddExtraData(data interface{}, newData map[string]interface{}) (map[string]interface{}, error) {
	var (
		mapData map[string]interface{}
	)

	mapData, err := utility.StructToMap(data)
	if err != nil {
		return mapData, err
	}

	for key, value := range newData {
		mapData[key] = value
	}

	return mapData, nil
}

func GetUserWithAccountID(extReq request.ExternalRequest, accountID int) (external_models.User, error) {
	usItf, err := extReq.SendExternalRequest(request.GetUserReq, external_models.GetUserRequestModel{AccountID: uint(accountID)})
	if err != nil {
		return external_models.User{}, err
	}

	us, ok := usItf.(external_models.User)
	if !ok {
		return external_models.User{}, fmt.Errorf("response data format error")
	}

	if us.ID == 0 {
		return external_models.User{}, fmt.Errorf("user not found")
	}
	return us, nil
}

func GetCountryByNameOrCode(extReq request.ExternalRequest, logger *utility.Logger, NameOrCode string) (external_models.Country, error) {

	countryInterface, err := extReq.SendExternalRequest(request.GetCountry, external_models.GetCountryModel{
		Name: NameOrCode,
	})

	if err != nil {
		logger.Error(err.Error())
		return external_models.Country{}, fmt.Errorf("your country could not be resolved, please update your profile")
	}
	country, ok := countryInterface.(external_models.Country)
	if !ok {
		return external_models.Country{}, fmt.Errorf("response data format error")
	}
	if country.ID == 0 {
		return external_models.Country{}, fmt.Errorf("your country could not be resolved, please update your profile")
	}

	return country, nil
}

func GetUserProfileByAccountID(extReq request.ExternalRequest, logger *utility.Logger, accountID int) (external_models.UserProfile, error) {
	userProfileInterface, err := extReq.SendExternalRequest(request.GetUserProfile, external_models.GetUserProfileModel{
		AccountID: uint(accountID),
	})
	if err != nil {
		logger.Error(err.Error())
		return external_models.UserProfile{}, err
	}

	userProfile, ok := userProfileInterface.(external_models.UserProfile)
	if !ok {
		return external_models.UserProfile{}, fmt.Errorf("response data format error")
	}

	if userProfile.ID == 0 {
		return external_models.UserProfile{}, fmt.Errorf("user profile not found")
	}

	return userProfile, nil

}

func GetUserCountryWithAccountID(extReq request.ExternalRequest, user external_models.User) (external_models.Country, error) {

	var (
		countryNameOrCode string
		accountID         int = int(user.AccountID)
	)

	switch strings.ToLower(user.AccountType) {
	case "individual":
		profile, err := GetUserProfileByAccountID(extReq, extReq.Logger, accountID)
		if err != nil {
			return external_models.Country{}, fmt.Errorf("error getting user profile with account id %v, %v", accountID, err)
		}
		countryNameOrCode = profile.Country
	case "business":
		profile, err := GetBusinessProfileByAccountID(extReq, extReq.Logger, accountID)
		if err != nil {
			return external_models.Country{}, fmt.Errorf("error getting business profile with account id %v, %v", accountID, err)
		}
		countryNameOrCode = profile.Country
	default:
		profile, err := GetUserProfileByAccountID(extReq, extReq.Logger, accountID)
		if err != nil {
			return external_models.Country{}, fmt.Errorf("error getting user profile with account id %v, %v", accountID, err)
		}
		countryNameOrCode = profile.Country
	}

	countryNameOrCode = thisOrThatStr(countryNameOrCode, "NG")
	country, err := GetCountryByNameOrCode(extReq, extReq.Logger, countryNameOrCode)
	if err != nil {
		return external_models.Country{}, fmt.Errorf("error getting country with code or name %v, %v", countryNameOrCode, err)
	}
	return country, err
}

func thisOrThatStr(this, that string) string {
	if this == "" {
		return that
	}
	return this
}

func GetBusinessProfileByAccountID(extReq request.ExternalRequest, logger *utility.Logger, accountID int) (external_models.BusinessProfile, error) {
	businessProfileInterface, err := extReq.SendExternalRequest(request.GetBusinessProfile, external_models.GetBusinessProfileModel{
		AccountID: uint(accountID),
	})
	if err != nil {
		logger.Error(err.Error())
		return external_models.BusinessProfile{}, fmt.Errorf("business lacks a profile")
	}

	businessProfile, ok := businessProfileInterface.(external_models.BusinessProfile)
	if !ok {
		return external_models.BusinessProfile{}, fmt.Errorf("response data format error")
	}

	if businessProfile.ID == 0 {
		return external_models.BusinessProfile{}, fmt.Errorf("business lacks a profile")
	}
	return businessProfile, nil
}

func GetInternationalNumber(extReq request.ExternalRequest, user external_models.User) (string, error) {

	country, err := GetUserCountryWithAccountID(extReq, user)
	if err != nil {
		return "", err
	}

	phone, err := utility.MakeInternationalPhoneNumber(extReq.Test, user.PhoneNumber, country.CountryCode)
	if err != nil {
		return phone, fmt.Errorf("error getting international number for %v, country %v, %v", user.PhoneNumber, country.CountryCode, err)
	}

	return phone, err
}

func ListTransactionsByID(extReq request.ExternalRequest, transactionID string) (external_models.TransactionByID, error) {

	transactionInterface, err := extReq.SendExternalRequest(request.ListTransactionsByID, transactionID)

	if err != nil {
		extReq.Logger.Error(err.Error())
		return external_models.TransactionByID{}, fmt.Errorf("transaction could not be retrieved")
	}
	transaction, ok := transactionInterface.(external_models.TransactionByID)
	if !ok {
		return external_models.TransactionByID{}, fmt.Errorf("response data format error")
	}
	if transaction.ID == 0 {
		return external_models.TransactionByID{}, fmt.Errorf("transaction not found")
	}

	return transaction, nil
}

func ListPayment(extReq request.ExternalRequest, transactionID string) (external_models.ListPayment, error) {
	paymentInterface, err := extReq.SendExternalRequest(request.ListPayment, transactionID)
	if err != nil {
		return external_models.ListPayment{}, err
	}

	payment, ok := paymentInterface.(external_models.ListPayment)
	if !ok {
		return external_models.ListPayment{}, fmt.Errorf("response data format error")
	}

	if payment.ID == 0 {
		return external_models.ListPayment{}, fmt.Errorf("payment listing failed")
	}
	return payment, nil
}

func GetBankDetail(extReq request.ExternalRequest, id, accountID int, country, currency string, isMobileMoneyOperator ...bool) (external_models.BankDetail, error) {

	data := external_models.GetBankDetailModel{}
	isMobileMoneyOperatorValue := false
	if len(isMobileMoneyOperator) > 0 {
		isMobileMoneyOperatorValue = isMobileMoneyOperator[0]
	}
	if id != 0 {
		data = external_models.GetBankDetailModel{
			ID: uint(id),
		}
	} else {
		data = external_models.GetBankDetailModel{
			AccountID:             uint(accountID),
			Country:               country,
			Currency:              currency,
			IsMobileMoneyOperator: isMobileMoneyOperatorValue,
		}
	}
	userBankInterface, err := extReq.SendExternalRequest(request.GetBankDetails, data)

	if err != nil {
		extReq.Logger.Error(err.Error())
		return external_models.BankDetail{}, err
	}

	bankDetail, ok := userBankInterface.(external_models.BankDetail)
	if !ok {
		return bankDetail, fmt.Errorf("response data format error")
	}
	if bankDetail.ID == 0 {
		return bankDetail, fmt.Errorf("bank detail not found")
	}

	return bankDetail, nil
}
