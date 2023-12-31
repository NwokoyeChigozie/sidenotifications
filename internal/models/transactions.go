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
type SendTransactionDeliveredAndAccepted struct {
	TransactionID string `json:"transaction_id"  validate:"required" pgvalidate:"exists=transaction$transactions$transaction_id"`
}
type SendTransactionDeliveredAndRejected struct {
	TransactionID string `json:"transaction_id"  validate:"required" pgvalidate:"exists=transaction$transactions$transaction_id"`
}
type SendDisputeOpened struct {
	TransactionID string `json:"transaction_id"  validate:"required" pgvalidate:"exists=transaction$transactions$transaction_id"`
}
type SendTransactionDelivered struct {
	TransactionID string `json:"transaction_id"  validate:"required" pgvalidate:"exists=transaction$transactions$transaction_id"`
}
type SendDueDateProposal struct {
	TransactionID string `json:"transaction_id"  validate:"required" pgvalidate:"exists=transaction$transactions$transaction_id"`
}
type SendDueDateExtended struct {
	TransactionID string `json:"transaction_id"  validate:"required" pgvalidate:"exists=transaction$transactions$transaction_id"`
}
type SendTransactionPaid struct {
	TransactionID string `json:"transaction_id"  validate:"required" pgvalidate:"exists=transaction$transactions$transaction_id"`
}
type SendTransactionClosedBuyer struct {
	TransactionID string `json:"transaction_id"  validate:"required" pgvalidate:"exists=transaction$transactions$transaction_id"`
}
type SendTransactionClosedSeller struct {
	TransactionID string `json:"transaction_id"  validate:"required" pgvalidate:"exists=transaction$transactions$transaction_id"`
}
