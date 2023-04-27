package external_models

type WalletBalance struct {
	ID        uint    `json:"id"`
	AccountID int     `json:"account_id"`
	Available float64 `json:"available"`
	CreatedAt string  `json:"created_at"`
	UpdatedAt string  `json:"updated_at"`
	Currency  string  `json:"currency"`
}
type WalletBalanceResponse struct {
	Status  string        `json:"status"`
	Code    int           `json:"code"`
	Message string        `json:"message"`
	Data    WalletBalance `json:"data"`
}

type GetWalletRequest struct {
	AccountID uint   `json:"account_id"`
	Currency  string `json:"currency"`
}
type CreateWalletRequest struct {
	AccountID uint    `json:"account_id"`
	Currency  string  `json:"currency"`
	Available float64 `json:"available"`
}
type UpdateWalletRequest struct {
	ID        uint    `json:"id"`
	Available float64 `json:"available"`
}

type WalletHistory struct {
	ID               uint    `json:"id"`
	AccountID        string  `json:"account_id"`
	Reference        string  `json:"reference"`
	Amount           float64 `json:"amount"`
	Currency         string  `json:"currency"`
	Type             string  `json:"type"`
	AvailableBalance float64 `json:"available_balance"`
	CreatedAt        string  `json:"created_at"`
	UpdatedAt        string  `json:"updated_at"`
	DeletedAt        string  `json:"deleted_at"`
}
type CreateWalletHistoryRequest struct {
	AccountID        int     `json:"account_id"`
	Reference        string  `json:"reference"`
	Amount           float64 `json:"amount"`
	Currency         string  `json:"currency"`
	Type             string  `json:"type"`
	AvailableBalance float64 `json:"available_balance"`
}
type WalletHistoryResponse struct {
	Status  string        `json:"status"`
	Code    int           `json:"code"`
	Message string        `json:"message"`
	Data    WalletHistory `json:"data"`
}

type WalletTransaction struct {
	ID                uint    `json:"id"`
	SenderAccountID   string  `json:"sender_account_id"`
	ReceiverAccountID string  `json:"receiver_account_id"`
	SenderAmount      float64 `json:"sender_amount"`
	ReceiverAmount    float64 `json:"receiver_amount"`
	SenderCurrency    string  `json:"sender_currency"`
	ReceiverCurrency  string  `json:"receiver_currency"`
	Approved          string  `json:"approved"`
	CreatedAt         string  `json:"created_at"`
	UpdatedAt         string  `json:"updated_at"`
	DeletedAt         string  `json:"deleted_at"`
	FirstApproval     bool    `json:"first_approval"`
	SecondApproval    bool    `json:"second_approval"`
}

type CreateWalletTransactionRequest struct {
	SenderAccountID   int     `json:"sender_account_id"`
	ReceiverAccountID int     `json:"receiver_account_id"`
	SenderAmount      float64 `json:"sender_amount"`
	ReceiverAmount    float64 `json:"receiver_amount"`
	SenderCurrency    string  `json:"sender_currency"`
	ReceiverCurrency  string  `json:"receiver_currency"`
	Approved          string  `json:"approved"`
	FirstApproval     bool    `json:"first_approval"`
	SecondApproval    *bool   `json:"second_approval"`
}
type WalletTransactionResponse struct {
	Status  string            `json:"status"`
	Code    int               `json:"code"`
	Message string            `json:"message"`
	Data    WalletTransaction `json:"data"`
}

type DebitWalletRequest struct {
	Amount        float64 `json:"amount"`
	Currency      string  `json:"currency"`
	BusinessID    int     `json:"business_id"`
	EscrowWallet  string  `json:"escrow_wallet"`
	TransactionID string  `json:"transaction_id"`
}

type CreditWalletRequest struct {
	Amount        float64 `json:"amount"`
	Currency      string  `json:"currency"`
	BusinessID    int     `json:"business_id"`
	IsRefund      bool    `json:"is_refund"`
	EscrowWallet  string  `json:"escrow_wallet"`
	TransactionID string  `json:"transaction_id"`
}

type WalletTransferRequest struct {
	SenderAccountID    int     `json:"sender_account_id"`
	RecipientAccountID int     `json:"recipient_account_id"`
	InitialAmount      float64 `json:"initial_amount"`
	FinalAmount        float64 `json:"final_amount"`
	RateID             int     `json:"rate_id"`
	SenderCurrency     string  `json:"sender_currency"`
	RecipientCurrency  string  `json:"recipient_currency"`
	TransactionID      string  `json:"transaction_id"`
	Refund             bool    `json:"refund"`
}
type WalletTransferResponse struct {
	Status  string `json:"status"`
	Code    int    `json:"code"`
	Message string `json:"message"`
}
