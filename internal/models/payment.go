package models

type SendWalletFunded struct {
	AccountID     int     `json:"account_id"  validate:"required" pgvalidate:"exists=auth$users$account_id"`
	Amount        float64 `json:"amount"`
	Currency      string  `json:"currency"`
	TransactionID string  `json:"transaction_id" pgvalidate:"exists=transaction$transactions$transaction_id"`
}

type SendWalletDebited struct {
	AccountID     int     `json:"account_id"  validate:"required" pgvalidate:"exists=auth$users$account_id"`
	Amount        float64 `json:"amount"`
	Currency      string  `json:"currency"`
	TransactionID string  `json:"transaction_id" pgvalidate:"exists=transaction$transactions$transaction_id"`
}

type SendPaymentReceipt struct {
	Reference                 string  `json:"reference" validate:"required"`
	PaymentID                 string  `json:"payment_id"`
	TransactionType           string  `json:"transaction_type"`
	TransactionID             string  `json:"transaction_id" pgvalidate:"exists=transaction$transactions$transaction_id"`
	Buyer                     int     `json:"buyer" pgvalidate:"exists=auth$users$account_id"`
	Seller                    int     `json:"seller" pgvalidate:"exists=auth$users$account_id"`
	InspectionPeriodFormatted string  `json:"inspection_period_formatted"`
	ExpectedDelivery          string  `json:"expected_delivery"`
	Title                     string  `json:"title"`
	Currency                  string  `json:"currency"`
	Amount                    float64 `json:"amount"`
	EscrowCharge              float64 `json:"escrow_charge"`
	BrokerCharge              float64 `json:"broker_charge"`
}

type SendSuccessfulRefund struct {
	TransactionID string `json:"transaction_id"  validate:"required" pgvalidate:"exists=transaction$transactions$transaction_id"`
}
type SendBuyerDisbursementSuccessful struct {
	TransactionID string `json:"transaction_id"  validate:"required" pgvalidate:"exists=transaction$transactions$transaction_id"`
}
type SendSellerDisbursementSuccessful struct {
	TransactionID string `json:"transaction_id"  validate:"required" pgvalidate:"exists=transaction$transactions$transaction_id"`
}
