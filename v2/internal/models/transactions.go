package models

type SendNewTransaction struct {
	TransactionID string `json:"transaction_id"  validate:"required" pgvalidate:"exists=transaction$transactions$transaction_id"`
}

type SendTransactionAccepted struct {
	TransactionID string `json:"transaction_id"  validate:"required" pgvalidate:"exists=transaction$transactions$transaction_id"`
}
type SendTransactionRejected struct {
	TransactionID string `json:"transaction_id"  validate:"required" pgvalidate:"exists=transaction$transactions$transaction_id"`
}
