package notifications

import (
	"fmt"
	"strings"

	"github.com/vesicash/notifications-ms/external/external_models"
	"github.com/vesicash/notifications-ms/external/request"
	"github.com/vesicash/notifications-ms/pkg/repository/storage/postgresql"
	"github.com/vesicash/notifications-ms/services/send"
)

type TransactionDataObject struct {
	Buyer             external_models.User
	Seller            external_models.User
	Broker            external_models.User
	SellerBankDetails external_models.BankDetail
	payment           external_models.ListPayment
	Transaction       external_models.TransactionByID
	Business          external_models.BusinessProfile
}

type TransactionNotificationType1 struct {
	ExtReq              request.ExternalRequest
	Db                  postgresql.Databases
	EmailAddress        string
	TransactionObject   TransactionDataObject
	InstantescrowSource TransactionNotificationType1Data
	Transfer            TransactionNotificationType1Data
	Marketplace         TransactionNotificationType1Data
	SocialCommerce      TransactionNotificationType1Data
	Default             TransactionNotificationType1Data
	Data                map[string]interface{}
}

type TransactionNotificationType1Data struct {
	Subject              string
	PdfTemplatePath      string
	BasePdfTemplatePath  string
	PdfTemplateName      string
	BaseTemplateFileName string
	TemplateFileName     string
}

func getTransactionMessage(messageType string, data TransactionDataObject) string {
	var message string

	switch strings.ToLower(messageType) {
	case "transaction-sent":
		message = "Your escrow transaction has been sent and you will be notified when it has been paid for."
	case "transaction-received":
		message = "You have received a new Escrow transaction. Kindly check your email for full details."
	case "transaction-accepted":
		message = fmt.Sprintf("Your transaction (%v) on Vesicash Escrow has been accepted.", data.Transaction.TransactionID)
	case "transaction-rejected":
		message = fmt.Sprintf("Your transaction (%v) on Vesicash Escrow has been rejected . Kindly check your dashboard for full details.", data.Transaction.TransactionID)
	case "transaction-paid":
		message = fmt.Sprintf("Your transaction (%v) on Vesicash Escrow has been paid for. Please go ahead with the delivery.", data.Transaction.TransactionID)
	case "transaction-delivered":
		message = fmt.Sprintf("Did you receive a shipment for transaction (%v) on Vesicash?. Kindly check your email for full details", data.Transaction.TransactionID)
	case "transaction-delivered-accepted":
		message = fmt.Sprintf("Your transaction (%v) on Vesicash has been accepted. Seller will receive escrow funds shortly.", data.Transaction.TransactionID)
	case "transaction-delivered-rejected":
		message = fmt.Sprintf("Your transaction (%v) on Vesicash has been rejected by %v", data.Transaction.TransactionID, data.Buyer.EmailAddress)
	case "escrow-disbursed-seller":
		message = fmt.Sprintf("Dear %v, Vesicash has just disbursed funds for the transaction (%v) into your account.", thisOrThatStr(data.Seller.Firstname, data.Seller.EmailAddress), data.Transaction.TransactionID)
	case "escrow-disbursed-buyer":
		message = fmt.Sprintf("Vesicash Escrow has just disbursed funds for the transaction (%v).", data.Transaction.TransactionID)
	case "transaction-closed-seller":
		message = fmt.Sprintf("Your transaction (%v) has been fulfilled and will be closed shortly.'", data.Transaction.TransactionID)
	case "transaction-closed-buyer":
		message = fmt.Sprintf("Your transaction (%v) has been fulfilled and will be closed shortly.", data.Transaction.TransactionID)
	case "dispute-opened":
		message = fmt.Sprintf("A dispute has been opened by %v on your transaction - (%v). Kindly check your email for full details.", data.Buyer.EmailAddress, data.Transaction.TransactionID)
	case "due-date-extension":
		message = fmt.Sprintf("Dear %v, ' . %v . ' has extended the due date of your transaction (%v). Check your email for full details.", data.Seller.AccountID, data.Buyer.AccountID, data.Transaction.TransactionID)
	case "due-date-proposal":
		message = fmt.Sprintf("Dear %v, %v wants you to extend the due date for transaction (%v). Check your email for full details.", data.Buyer.EmailAddress, data.Seller.AccountID, data.Transaction.TransactionID)
	case "successful-refund":
		message = fmt.Sprintf("Dear %v, you have been refunded the sum of %v %v for the transaction (%v).", data.Buyer, data.Transaction.Currency, data.Transaction.Amount, data.Transaction.TransactionID)
	default:
		message = ""
	}

	return message
}

func AddTransactionDataToMap(transactionObject TransactionDataObject, data map[string]interface{}) map[string]interface{} {
	data["transaction"] = transactionObject.Transaction
	data["buyer"] = transactionObject.Buyer
	data["seller"] = transactionObject.Seller
	data["broker_charge_bearer"] = transactionObject.Transaction.Parties["broker_charge_bearer"]
	data["shipping_charge_bearer"] = transactionObject.Transaction.Parties["shipping_charge_bearer"]

	return data
}

func GetTransactionObject(extReq request.ExternalRequest, transactionID string) (TransactionDataObject, error) {
	var (
		transactionObj = TransactionDataObject{}
		err            error
	)

	transactionObj.Transaction, err = ListTransactionsByID(extReq, transactionID)
	if err != nil {
		return transactionObj, err
	}

	transactionObj.payment, _ = ListPayment(extReq, transactionID)

	transactionObj.Business, err = GetBusinessProfileByAccountID(extReq, extReq.Logger, transactionObj.Transaction.BusinessID)
	if err != nil {
		return transactionObj, fmt.Errorf("transaction business owner has no business profile data, %v", err.Error())
	}

	buyerParty, ok := transactionObj.Transaction.Parties["buyer"]
	if !ok {
		return transactionObj, fmt.Errorf("transaction has no buyer party")
	}

	sellerParty, ok := transactionObj.Transaction.Parties["seller"]
	if !ok {
		return transactionObj, fmt.Errorf("transaction has no seller party")
	}

	transactionObj.Buyer, err = GetUserWithAccountID(extReq, buyerParty.AccountID)
	if err != nil {
		return transactionObj, err
	}

	transactionObj.Seller, err = GetUserWithAccountID(extReq, sellerParty.AccountID)
	if err != nil {
		return transactionObj, err
	}

	transactionObj.SellerBankDetails, _ = GetBankDetail(extReq, 0, sellerParty.AccountID, "", "")

	if strings.EqualFold(transactionObj.Transaction.Type, "broker") {
		broker := transactionObj.Transaction.Parties["broker"]
		transactionObj.Broker, _ = GetUserWithAccountID(extReq, broker.AccountID)
	}

	return transactionObj, nil
}

func (t *TransactionNotificationType1) sendTransactionNotificationType1Data() error {
	if strings.EqualFold(t.TransactionObject.Transaction.Source, "instantescrow") && t.InstantescrowSource.Subject != "" {
		return send.SendEmailWithAttachment(t.ExtReq, t.EmailAddress, t.InstantescrowSource.Subject, t.InstantescrowSource.TemplateFileName, t.InstantescrowSource.BaseTemplateFileName, t.Data, t.InstantescrowSource.PdfTemplatePath, t.InstantescrowSource.BasePdfTemplatePath, t.InstantescrowSource.PdfTemplateName)
	}
	if strings.EqualFold(t.TransactionObject.Transaction.Source, "transfer") && t.Transfer.Subject != "" {
		return send.SendEmailWithAttachment(t.ExtReq, t.EmailAddress, t.Transfer.Subject, t.Transfer.TemplateFileName, t.Transfer.BaseTemplateFileName, t.Data, t.Transfer.PdfTemplatePath, t.Transfer.BasePdfTemplatePath, t.Transfer.PdfTemplateName)
	}

	switch strings.ToLower(t.TransactionObject.Business.BusinessType) {
	case "marketplace":
		return send.SendEmailWithAttachment(t.ExtReq, t.EmailAddress, t.Marketplace.Subject, t.Marketplace.TemplateFileName, t.Marketplace.BaseTemplateFileName, t.Data, t.Marketplace.PdfTemplatePath, t.Marketplace.BasePdfTemplatePath, t.Marketplace.PdfTemplateName)
	case "social_commerce":
		return send.SendEmailWithAttachment(t.ExtReq, t.EmailAddress, t.SocialCommerce.Subject, t.SocialCommerce.TemplateFileName, t.SocialCommerce.BaseTemplateFileName, t.Data, t.SocialCommerce.PdfTemplatePath, t.SocialCommerce.BasePdfTemplatePath, t.SocialCommerce.PdfTemplateName)
	default:
		return send.SendEmailWithAttachment(t.ExtReq, t.EmailAddress, t.Default.Subject, t.Default.TemplateFileName, t.Default.BaseTemplateFileName, t.Data, t.Default.PdfTemplatePath, t.Default.BasePdfTemplatePath, t.Default.PdfTemplateName)
	}
}
