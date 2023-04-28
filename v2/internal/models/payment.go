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
