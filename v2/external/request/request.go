package request

import (
	"fmt"

	"github.com/vesicash/notifications-ms/v2/external/microservice/auth"
	"github.com/vesicash/notifications-ms/v2/external/microservice/transactions"
	"github.com/vesicash/notifications-ms/v2/external/microservice/upload"
	"github.com/vesicash/notifications-ms/v2/external/microservice/verification"
	"github.com/vesicash/notifications-ms/v2/external/mocks"
	rave "github.com/vesicash/notifications-ms/v2/external/thirdparty/Rave"
	"github.com/vesicash/notifications-ms/v2/external/thirdparty/appruve"
	"github.com/vesicash/notifications-ms/v2/external/thirdparty/ip_api"
	"github.com/vesicash/notifications-ms/v2/external/thirdparty/ipstack"
	"github.com/vesicash/notifications-ms/v2/external/thirdparty/monnify"
	"github.com/vesicash/notifications-ms/v2/external/thirdparty/termii"
	"github.com/vesicash/notifications-ms/v2/internal/config"
	"github.com/vesicash/notifications-ms/v2/utility"
)

type ExternalRequest struct {
	Logger *utility.Logger
	Test   bool
}

var (
	JsonDecodeMethod    string = "json"
	PhpSerializerMethod string = "phpserializer"

	// microservice
	GetUserReq           string = "get_user"
	GetUserCredential    string = "get_user_credential"
	CreateUserCredential string = "create_user_credential"
	UpdateUserCredential string = "update_user_credential"

	GetUserProfile     string = "get_user_profile"
	GetBusinessProfile string = "get_business_profile"
	GetCountry         string = "get_country"
	GetBankDetails     string = "get_bank_details"

	GetAccessTokenReq     string = "get_access_token"
	ValidateOnAuth        string = "validate_on_auth"
	ValidateAuthorization string = "validate_authorization"
	SendSmsToPhone        string = "send_sms_to_phone"

	// third party
	MonnifyLogin           string = "monnify_login"
	MonnifyMatchBvnDetails string = "monnify_match_bvn_details"

	AppruveVerifyId string = "appruve_verify_id"

	RaveResolveBankAccount string = "rave_resolve_bank_account"

	IpstackResolveIp                   string = "ipstack_resolve_ip"
	GetAuthorize                       string = "get_authorize"
	CreateAuthorize                    string = "create_authorize"
	UpdateAuthorize                    string = "update_authorize"
	SetUserAuthorizationRequiredStatus string = "set_user_authorization_required_status"

	ValidateOnTransactions string = "validate_on_transactions"
	ListTransactionsByID   string = "list_transactions_by_id"

	GetUsersByBusinessID                string = "get_users_by_business_id"
	ListBanksWithRave                   string = "list_banks_with_rave"
	ConvertCurrencyWithRave             string = "convert_currency_with_rave"
	ResolveIP                           string = "resolve_ip"
	GetBusinessCharge                   string = "get_business_charge"
	InitBusinessCharge                  string = "init_business_charge"
	RaveInitPayment                     string = "rave_init_payment"
	MonnifyInitPayment                  string = "monnify_init_payment"
	GetAccessTokenByKey                 string = "get_access_token_by_key"
	GetEscrowCharge                     string = "get_escrow_charge"
	RaveReserveAccount                  string = "rave_reserve_account"
	RaveVerifyTransactionByTxRef        string = "rave_verify_transaction_by_tx_ref"
	MonnifyVerifyTransactionByReference string = "monnify_verify_transaction_by_reference"

	CreateWalletBalance                    string = "create_wallet_balance"
	GetWalletBalanceByAccountIDAndCurrency string = "get_wallet_balance_by_account_id_and_currency"
	UpdateWalletBalance                    string = "update_wallet_balance"
	UpdateTransactionAmountPaid            string = "update_transaction_amount_paid"

	CreateActivityLog       string = "create_activity_log"
	TransactionUpdateStatus string = "transaction_update_status"
	BuyerSatisfied          string = "buyer_satisfied"

	RaveChargeCard                       string = "rave_charge_card"
	MonnifyReserveAccount                string = "monnify_reserve_account"
	GetMonnifyReserveAccountTransactions string = "get_monnify_reserve_account_transactions"
	UploadFile                           string = "upload_file"

	CreateWalletHistory       string = "create_wallet_history"
	CreateWalletTransaction   string = "create_wallet_transaction"
	CreateExchangeTransaction string = "create_exchange_transaction"
	GetRateByID               string = "get_rate_by_id"
	GetBank                   string = "get_bank"

	RaveInitTransfer    string = "rave_init_transfer"
	MonnifyInitTransfer string = "monnify_init_transfer"

	GetAccessTokenByBusinessID string = "get_access_token_by_busines_id"
	CheckVerification          string = "check_verification"
	ListTransactions           string = "list_transactions"

	TermiiSendSMS string = "termii_send_sms"
)

func (er ExternalRequest) SendExternalRequest(name string, data interface{}) (interface{}, error) {
	var (
		config = config.GetConfig()
	)
	if !er.Test {
		switch name {
		case "get_user":
			obj := auth.RequestObj{
				Name:         name,
				Path:         fmt.Sprintf("%v/v2/get_user", config.Microservices.Auth),
				Method:       "POST",
				SuccessCode:  200,
				DecodeMethod: JsonDecodeMethod,
				RequestData:  data,
				Logger:       er.Logger,
			}
			return obj.GetUser()
		case "get_user_credential":
			obj := auth.RequestObj{
				Name:         name,
				Path:         fmt.Sprintf("%v/v2/get_user_credentials", config.Microservices.Auth),
				Method:       "POST",
				SuccessCode:  200,
				DecodeMethod: JsonDecodeMethod,
				RequestData:  data,
				Logger:       er.Logger,
			}
			return obj.GetUserCredential()
		case "create_user_credential":
			obj := auth.RequestObj{
				Name:         name,
				Path:         fmt.Sprintf("%v/v2/create_user_credentials", config.Microservices.Auth),
				Method:       "POST",
				SuccessCode:  200,
				DecodeMethod: JsonDecodeMethod,
				RequestData:  data,
				Logger:       er.Logger,
			}
			return obj.CreateUserCredential()
		case "update_user_credential":
			obj := auth.RequestObj{
				Name:         name,
				Path:         fmt.Sprintf("%v/v2/update_user_credentials", config.Microservices.Auth),
				Method:       "POST",
				SuccessCode:  200,
				DecodeMethod: JsonDecodeMethod,
				RequestData:  data,
				Logger:       er.Logger,
			}
			return obj.UpdateUserCredential()
		case "get_user_profile":
			obj := auth.RequestObj{
				Name:         name,
				Path:         fmt.Sprintf("%v/v2/get_user_profile", config.Microservices.Auth),
				Method:       "POST",
				SuccessCode:  200,
				DecodeMethod: JsonDecodeMethod,
				RequestData:  data,
				Logger:       er.Logger,
			}
			return obj.GetUserProfile()
		case "get_business_profile":
			obj := auth.RequestObj{
				Name:         name,
				Path:         fmt.Sprintf("%v/v2/get_business_profile", config.Microservices.Auth),
				Method:       "POST",
				SuccessCode:  200,
				DecodeMethod: JsonDecodeMethod,
				RequestData:  data,
				Logger:       er.Logger,
			}
			return obj.GetBusinessProfile()
		case "get_country":
			obj := auth.RequestObj{
				Name:         name,
				Path:         fmt.Sprintf("%v/v2/get_country", config.Microservices.Auth),
				Method:       "POST",
				SuccessCode:  200,
				DecodeMethod: JsonDecodeMethod,
				RequestData:  data,
				Logger:       er.Logger,
			}
			return obj.GetCountry()
		case "get_bank_details":
			obj := auth.RequestObj{
				Name:         name,
				Path:         fmt.Sprintf("%v/v2/get_bank_detail", config.Microservices.Auth),
				Method:       "POST",
				SuccessCode:  200,
				DecodeMethod: JsonDecodeMethod,
				RequestData:  data,
				Logger:       er.Logger,
			}
			return obj.GetBankDetails()
		case "get_access_token":
			obj := auth.RequestObj{
				Name:         name,
				Path:         fmt.Sprintf("%v/v2/get_access_token", config.Microservices.Auth),
				Method:       "GET",
				SuccessCode:  200,
				DecodeMethod: JsonDecodeMethod,
				RequestData:  data,
				Logger:       er.Logger,
			}
			return obj.GetAccessToken()
		case "validate_on_auth":
			obj := auth.RequestObj{
				Name:         name,
				Path:         fmt.Sprintf("%v/v2/validate_on_db", config.Microservices.Auth),
				Method:       "POST",
				SuccessCode:  200,
				DecodeMethod: JsonDecodeMethod,
				RequestData:  data,
				Logger:       er.Logger,
			}
			return obj.ValidateOnAuth()
		case "validate_authorization":
			obj := auth.RequestObj{
				Name:         name,
				Path:         fmt.Sprintf("%v/v2/validate_authorization", config.Microservices.Auth),
				Method:       "POST",
				SuccessCode:  200,
				DecodeMethod: JsonDecodeMethod,
				RequestData:  data,
				Logger:       er.Logger,
			}
			return obj.ValidateAuthorization()
		case "monnify_login":
			obj := monnify.RequestObj{
				Name:         name,
				Path:         fmt.Sprintf("%v/v1/auth/login", config.Monnify.MonnifyEndpoint),
				Method:       "POST",
				SuccessCode:  200,
				DecodeMethod: JsonDecodeMethod,
				RequestData:  data,
				Logger:       er.Logger,
			}
			return obj.MonnifyLogin()
		case "monnify_match_bvn_details":
			obj := monnify.RequestObj{
				Name:         name,
				Path:         fmt.Sprintf("%v/api/v1/vas/bvn-details-match", config.Monnify.MonnifyApi),
				Method:       "POST",
				SuccessCode:  200,
				DecodeMethod: JsonDecodeMethod,
				RequestData:  data,
				Logger:       er.Logger,
			}
			return obj.MonnifyMatchBvnDetails()
		case "appruve_verify_id":
			obj := appruve.RequestObj{
				Name:         name,
				Path:         fmt.Sprintf("%v/v1/verifications", config.Appruve.BaseUrl),
				Method:       "POST",
				SuccessCode:  200,
				DecodeMethod: JsonDecodeMethod,
				RequestData:  data,
				Logger:       er.Logger,
			}
			return obj.AppruveVerifyID()
		case "rave_resolve_bank_account":
			obj := rave.RequestObj{
				Name:         name,
				Path:         fmt.Sprintf("%v/v3/accounts/resolve", config.Rave.BaseUrl),
				Method:       "POST",
				SuccessCode:  200,
				DecodeMethod: JsonDecodeMethod,
				RequestData:  data,
				Logger:       er.Logger,
			}
			return obj.RaveResolveBankAccount()
		case "ipstack_resolve_ip":
			obj := ipstack.RequestObj{
				Name:         name,
				Path:         fmt.Sprintf("%v", config.IPStack.BaseUrl),
				Method:       "GET",
				SuccessCode:  200,
				DecodeMethod: JsonDecodeMethod,
				RequestData:  data,
				Logger:       er.Logger,
			}
			return obj.IpstackResolveIp()
		case "get_authorize":
			obj := auth.RequestObj{
				Name:         name,
				Path:         fmt.Sprintf("%v/v2/get_authorize", config.Microservices.Auth),
				Method:       "POST",
				SuccessCode:  200,
				DecodeMethod: JsonDecodeMethod,
				RequestData:  data,
				Logger:       er.Logger,
			}
			return obj.GetAuthorize()
		case "create_authorize":
			obj := auth.RequestObj{
				Name:         name,
				Path:         fmt.Sprintf("%v/v2/create_authorize", config.Microservices.Auth),
				Method:       "POST",
				SuccessCode:  200,
				DecodeMethod: JsonDecodeMethod,
				RequestData:  data,
				Logger:       er.Logger,
			}
			return obj.CreateAuthorize()
		case "update_authorize":
			obj := auth.RequestObj{
				Name:         name,
				Path:         fmt.Sprintf("%v/v2/update_authorize", config.Microservices.Auth),
				Method:       "POST",
				SuccessCode:  200,
				DecodeMethod: JsonDecodeMethod,
				RequestData:  data,
				Logger:       er.Logger,
			}
			return obj.UpdateAuthorize()
		case "set_user_authorization_required_status":
			obj := auth.RequestObj{
				Name:         name,
				Path:         fmt.Sprintf("%v/v2/set_authorization_required", config.Microservices.Auth),
				Method:       "POST",
				SuccessCode:  200,
				DecodeMethod: JsonDecodeMethod,
				RequestData:  data,
				Logger:       er.Logger,
			}
			return obj.SetUserAuthorizationRequiredStatus()
		case "validate_on_transactions":
			obj := transactions.RequestObj{
				Name:         name,
				Path:         fmt.Sprintf("%v/v2/validate_on_db", config.Microservices.Transactions),
				Method:       "POST",
				SuccessCode:  200,
				DecodeMethod: JsonDecodeMethod,
				RequestData:  data,
				Logger:       er.Logger,
			}
			return obj.ValidateOnTransactions()
		case "list_transactions_by_id":
			obj := transactions.RequestObj{
				Name:         name,
				Path:         fmt.Sprintf("%v/v2/listById", config.Microservices.Transactions),
				Method:       "GET",
				SuccessCode:  200,
				DecodeMethod: JsonDecodeMethod,
				RequestData:  data,
				Logger:       er.Logger,
			}
			return obj.ListTransactionsByID()
		case "get_users_by_business_id":
			obj := auth.RequestObj{
				Name:         name,
				Path:         fmt.Sprintf("%v/v2/get_users_by_business_id", config.Microservices.Auth),
				Method:       "GET",
				SuccessCode:  200,
				DecodeMethod: JsonDecodeMethod,
				RequestData:  data,
				Logger:       er.Logger,
			}
			return obj.GetUsersByBusinessID()
		case "list_banks_with_rave":
			obj := rave.RequestObj{
				Name:         name,
				Path:         fmt.Sprintf("%v/v3/banks", config.Rave.BaseUrl),
				Method:       "GET",
				SuccessCode:  200,
				DecodeMethod: JsonDecodeMethod,
				RequestData:  data,
				Logger:       er.Logger,
			}
			return obj.ListBanksWithRave()
		case "convert_currency_with_rave":
			obj := rave.RequestObj{
				Name:         name,
				Path:         fmt.Sprintf("%v/v3/transfers/rates", config.Rave.BaseUrl),
				Method:       "GET",
				SuccessCode:  200,
				DecodeMethod: JsonDecodeMethod,
				RequestData:  data,
				Logger:       er.Logger,
			}
			return obj.ConvertCurrencyWithRave()
		case "resolve_ip":
			obj := ip_api.RequestObj{
				Name:         name,
				Path:         "http://ip-api.com/json",
				Method:       "POST",
				SuccessCode:  200,
				DecodeMethod: JsonDecodeMethod,
				RequestData:  data,
				Logger:       er.Logger,
			}
			return obj.ResolveIp()
		case "get_business_charge":
			obj := auth.RequestObj{
				Name:         name,
				Path:         fmt.Sprintf("%v/v2/get_business_charge", config.Microservices.Auth),
				Method:       "POST",
				SuccessCode:  201,
				DecodeMethod: JsonDecodeMethod,
				RequestData:  data,
				Logger:       er.Logger,
			}
			return obj.GetBusinessCharge()
		case "init_business_charge":
			obj := auth.RequestObj{
				Name:         name,
				Path:         fmt.Sprintf("%v/v2/init_business_charge", config.Microservices.Auth),
				Method:       "POST",
				SuccessCode:  200,
				DecodeMethod: JsonDecodeMethod,
				RequestData:  data,
				Logger:       er.Logger,
			}
			return obj.InitBusinessCharge()
		case "rave_init_payment":
			obj := rave.RequestObj{
				Name:         name,
				Path:         fmt.Sprintf("%v/v3/payments", config.Rave.BaseUrl),
				Method:       "POST",
				SuccessCode:  200,
				DecodeMethod: JsonDecodeMethod,
				RequestData:  data,
				Logger:       er.Logger,
			}
			return obj.RaveInitPayment()
		case "monnify_init_payment":
			obj := monnify.RequestObj{
				Name:         name,
				Path:         fmt.Sprintf("%v/v1/merchant/transactions/init-transaction", config.Monnify.MonnifyEndpoint),
				Method:       "POST",
				SuccessCode:  200,
				DecodeMethod: JsonDecodeMethod,
				RequestData:  data,
				Logger:       er.Logger,
			}
			return obj.MonnifyInitPayment()
		case "get_access_token_by_key":
			obj := auth.RequestObj{
				Name:         name,
				Path:         fmt.Sprintf("%v/v2/get_access_token_by_key", config.Microservices.Auth),
				Method:       "GET",
				SuccessCode:  200,
				DecodeMethod: JsonDecodeMethod,
				RequestData:  data,
				Logger:       er.Logger,
			}
			return obj.GetAccessTokenByKey()
		case "get_escrow_charge":
			obj := transactions.RequestObj{
				Name:         name,
				Path:         fmt.Sprintf("%v/v2/escrowcharge", config.Microservices.Transactions),
				Method:       "POST",
				SuccessCode:  200,
				DecodeMethod: JsonDecodeMethod,
				RequestData:  data,
				Logger:       er.Logger,
			}
			return obj.GetEscrowCharge()
		case "rave_reserve_account":
			obj := rave.RequestObj{
				Name:         name,
				Path:         fmt.Sprintf("%v/v3/virtual-account-numbers", config.Rave.BaseUrl),
				Method:       "POST",
				SuccessCode:  200,
				DecodeMethod: JsonDecodeMethod,
				RequestData:  data,
				Logger:       er.Logger,
			}
			return obj.RaveReserveAccount()
		case "rave_verify_transaction_by_tx_ref":
			obj := rave.RequestObj{
				Name:         name,
				Path:         fmt.Sprintf("%v/v3/transactions/verify_by_reference?tx_ref=", config.Rave.BaseUrl),
				Method:       "POST",
				SuccessCode:  200,
				DecodeMethod: JsonDecodeMethod,
				RequestData:  data,
				Logger:       er.Logger,
			}
			return obj.RaveVerifyTransactionByTxRef()
		case "monnify_verify_transaction_by_reference":
			obj := monnify.RequestObj{
				Name:         name,
				Path:         fmt.Sprintf("%v/v1/merchant/transactions/query?paymentReference=", config.Monnify.MonnifyEndpoint),
				Method:       "GET",
				SuccessCode:  200,
				DecodeMethod: JsonDecodeMethod,
				RequestData:  data,
				Logger:       er.Logger,
			}
			return obj.MonnifyVerifyTransactionByReference()
		case "create_wallet_balance":
			obj := auth.RequestObj{
				Name:         name,
				Path:         fmt.Sprintf("%v/v2/create_wallet", config.Microservices.Auth),
				Method:       "POST",
				SuccessCode:  200,
				DecodeMethod: JsonDecodeMethod,
				RequestData:  data,
				Logger:       er.Logger,
			}
			return obj.CreateWalletBalance()
		case "get_wallet_balance_by_account_id_and_currency":
			obj := auth.RequestObj{
				Name:         name,
				Path:         fmt.Sprintf("%v/v2/get_wallet", config.Microservices.Auth),
				Method:       "GET",
				SuccessCode:  200,
				DecodeMethod: JsonDecodeMethod,
				RequestData:  data,
				Logger:       er.Logger,
			}
			return obj.GetWalletBalanceByAccountIDAndCurrency()
		case "update_wallet_balance":
			obj := auth.RequestObj{
				Name:         name,
				Path:         fmt.Sprintf("%v/v2/update_wallet_balance", config.Microservices.Auth),
				Method:       "PATCH",
				SuccessCode:  200,
				DecodeMethod: JsonDecodeMethod,
				RequestData:  data,
				Logger:       er.Logger,
			}
			return obj.UpdateWalletBalance()
		case "update_transaction_amount_paid":
			obj := transactions.RequestObj{
				Name:         name,
				Path:         fmt.Sprintf("%v/v2/update_transaction_amount_paid", config.Microservices.Transactions),
				Method:       "PATCH",
				SuccessCode:  200,
				DecodeMethod: JsonDecodeMethod,
				RequestData:  data,
				Logger:       er.Logger,
			}
			return obj.UpdateTransactionAmountPaid()
		case "create_activity_log":
			obj := transactions.RequestObj{
				Name:         name,
				Path:         fmt.Sprintf("%v/v2/create_activity_log", config.Microservices.Transactions),
				Method:       "POST",
				SuccessCode:  200,
				DecodeMethod: JsonDecodeMethod,
				RequestData:  data,
				Logger:       er.Logger,
			}
			return obj.CreateActivityLog()
		case "transaction_update_status":
			obj := transactions.RequestObj{
				Name:         name,
				Path:         fmt.Sprintf("%v/v2/api/updateStatus", config.Microservices.Transactions),
				Method:       "PATCH",
				SuccessCode:  200,
				DecodeMethod: JsonDecodeMethod,
				RequestData:  data,
				Logger:       er.Logger,
			}
			return obj.TransactionUpdateStatus()
		case "buyer_satisfied":
			obj := transactions.RequestObj{
				Name:         name,
				Path:         fmt.Sprintf("%v/v2/api/satisfied", config.Microservices.Transactions),
				Method:       "POST",
				SuccessCode:  200,
				DecodeMethod: JsonDecodeMethod,
				RequestData:  data,
				Logger:       er.Logger,
			}
			return obj.BuyerSatisfied()
		case "rave_charge_card":
			obj := rave.RequestObj{
				Name:         name,
				Path:         fmt.Sprintf("%v/v3/tokenized-charges", config.Rave.BaseUrl),
				Method:       "POST",
				SuccessCode:  200,
				DecodeMethod: JsonDecodeMethod,
				RequestData:  data,
				Logger:       er.Logger,
			}
			return obj.RaveChargeCard()
		case "monnify_reserve_account":
			obj := monnify.RequestObj{
				Name:         name,
				Path:         fmt.Sprintf("%v/v1/bank-transfer/reserved-accounts", config.Monnify.MonnifyEndpoint),
				Method:       "POST",
				SuccessCode:  200,
				DecodeMethod: JsonDecodeMethod,
				RequestData:  data,
				Logger:       er.Logger,
			}
			return obj.MonnifyReserveAccount()
		case "get_monnify_reserve_account_transactions":
			obj := monnify.RequestObj{
				Name:         name,
				Path:         fmt.Sprintf("%v/v1/bank-transfer/reserved-accounts/transactions", config.Monnify.MonnifyEndpoint),
				Method:       "GET",
				SuccessCode:  200,
				DecodeMethod: JsonDecodeMethod,
				RequestData:  data,
				Logger:       er.Logger,
			}
			return obj.GetMonnifyReserveAccountTransactions()
		case "upload_file":
			obj := upload.RequestObj{
				Name:         name,
				Path:         fmt.Sprintf("%v/v2/files", config.Microservices.Upload),
				Method:       "POST",
				SuccessCode:  201,
				DecodeMethod: JsonDecodeMethod,
				RequestData:  data,
				Logger:       er.Logger,
			}
			return obj.UploadFile()
		case "create_wallet_history":
			obj := auth.RequestObj{
				Name:         name,
				Path:         fmt.Sprintf("%v/v2/create_wallet_history", config.Microservices.Auth),
				Method:       "POST",
				SuccessCode:  201,
				DecodeMethod: JsonDecodeMethod,
				RequestData:  data,
				Logger:       er.Logger,
			}
			return obj.CreateWalletHistory()
		case "create_wallet_transaction":
			obj := auth.RequestObj{
				Name:         name,
				Path:         fmt.Sprintf("%v/v2/create_wallet_transaction", config.Microservices.Auth),
				Method:       "POST",
				SuccessCode:  201,
				DecodeMethod: JsonDecodeMethod,
				RequestData:  data,
				Logger:       er.Logger,
			}
			return obj.CreateWalletTransaction()
		case "create_exchange_transaction":
			obj := transactions.RequestObj{
				Name:         name,
				Path:         fmt.Sprintf("%v/v2/create_exchange_transaction", config.Microservices.Transactions),
				Method:       "POST",
				SuccessCode:  201,
				DecodeMethod: JsonDecodeMethod,
				RequestData:  data,
				Logger:       er.Logger,
			}
			return obj.CreateExchangeTransaction()
		case "get_rate_by_id":
			obj := transactions.RequestObj{
				Name:         name,
				Path:         fmt.Sprintf("%v/v2/get_rate", config.Microservices.Transactions),
				Method:       "GET",
				SuccessCode:  200,
				DecodeMethod: JsonDecodeMethod,
				RequestData:  data,
				Logger:       er.Logger,
			}
			return obj.GetRateByID()
		case "get_bank":
			obj := auth.RequestObj{
				Name:         name,
				Path:         fmt.Sprintf("%v/v2/get_bank", config.Microservices.Auth),
				Method:       "POST",
				SuccessCode:  200,
				DecodeMethod: JsonDecodeMethod,
				RequestData:  data,
				Logger:       er.Logger,
			}
			return obj.GetBank()
		case "rave_init_transfer":
			obj := rave.RequestObj{
				Name:         name,
				Path:         fmt.Sprintf("%v/v3/transfers", config.Rave.BaseUrl),
				Method:       "POST",
				SuccessCode:  200,
				DecodeMethod: JsonDecodeMethod,
				RequestData:  data,
				Logger:       er.Logger,
			}
			return obj.RaveInitTransfer()
		case "monnify_init_transfer":
			obj := monnify.RequestObj{
				Name:         name,
				Path:         fmt.Sprintf("%v/v2/disbursements/single", config.Monnify.MonnifyEndpoint),
				Method:       "POST",
				SuccessCode:  200,
				DecodeMethod: JsonDecodeMethod,
				RequestData:  data,
				Logger:       er.Logger,
			}
			return obj.MonnifyInitTransfer()
		case "get_access_token_by_busines_id":
			obj := auth.RequestObj{
				Name:         name,
				Path:         fmt.Sprintf("%v/v2/get_access_token_by_busines_id", config.Microservices.Auth),
				Method:       "GET",
				SuccessCode:  200,
				DecodeMethod: JsonDecodeMethod,
				RequestData:  data,
				Logger:       er.Logger,
			}
			return obj.GetAccessTokenByBusinessID()
		case "check_verification":
			obj := verification.RequestObj{
				Name:         name,
				Path:         fmt.Sprintf("%v/v2/check_verification", config.Microservices.Verification),
				Method:       "POST",
				SuccessCode:  200,
				DecodeMethod: JsonDecodeMethod,
				RequestData:  data,
				Logger:       er.Logger,
			}
			return obj.CheckVerification()
		case "list_transactions":
			obj := transactions.RequestObj{
				Name:         name,
				Path:         fmt.Sprintf("%v/v2/list", config.Microservices.Transactions),
				Method:       "POST",
				SuccessCode:  200,
				DecodeMethod: JsonDecodeMethod,
				RequestData:  data,
				Logger:       er.Logger,
			}
			return obj.ListTransactions()
		case "termii_send_sms":
			obj := termii.RequestObj{
				Name:         name,
				Path:         fmt.Sprintf("%v/api/sms/send", config.Termii.BaseUrl),
				Method:       "POST",
				SuccessCode:  200,
				DecodeMethod: JsonDecodeMethod,
				RequestData:  data,
				Logger:       er.Logger,
			}
			return obj.TermiiSendSMS()
		default:
			return nil, fmt.Errorf("request not found")
		}

	} else {
		mer := mocks.ExternalRequest{Logger: er.Logger, Test: true}
		return mer.SendExternalRequest(name, data)
	}
}
