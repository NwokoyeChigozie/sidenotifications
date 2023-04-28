package external_models

type WalletFundedNotificationRequest struct {
	AccountID uint    `json:"account_id"`
	Amount    float64 `json:"amount"`
}

type WalletDebitNotificationRequest struct {
	AccountID     uint    `json:"account_id"`
	Amount        float64 `json:"amount"`
	TransactionID string  `json:"transaction_id"`
}
type PaymentInvoiceNotificationRequest struct {
	Reference                 string  `json:"reference"`
	PaymentID                 string  `json:"payment_id"`
	TransactionType           string  `json:"transaction_type"`
	TransactionID             string  `json:"transaction_id"`
	Buyer                     int     `json:"buyer"`
	Seller                    int     `json:"seller"`
	InspectionPeriodFormatted string  `json:"inspection_period_formatted"`
	ExpectedDelivery          string  `json:"expected_delivery"`
	Title                     string  `json:"title"`
	Currency                  string  `json:"currency"`
	Amount                    float64 `json:"amount"`
	EscrowCharge              float64 `json:"escrow_charge"`
	BrokerCharge              float64 `json:"broker_charge"`
}

type Payment struct {
	ID               int64   `json:"id"`
	PaymentID        string  `json:"payment_id"`
	TransactionID    string  `json:"transaction_id"`
	TotalAmount      float64 `json:"total_amount"`
	EscrowCharge     float64 `json:"escrow_charge"`
	IsPaid           bool    `json:"is_paid"`
	PaymentMadeAt    string  `json:"payment_made_at"`
	DeletedAt        string  `json:"deleted_at"`
	CreatedAt        string  `json:"created_at"`
	UpdatedAt        string  `json:"updated_at"`
	AccountID        int64   `json:"account_id"`
	BusinessID       int64   `json:"business_id"`
	Currency         string  `json:"currency"`
	ShippingFee      float64 `json:"shipping_fee"`
	DisburseCurrency string  `json:"disburse_currency"`
	PaymentType      string  `json:"payment_type"`
	BrokerCharge     float64 `json:"broker_charge"`
}

type CreatePaymentRequestWithToken struct {
	TransactionID string  `json:"transaction_id" `
	TotalAmount   float64 `json:"total_amount"`
	ShippingFee   float64 `json:"shipping_fee"`
	BrokerCharge  float64 `json:"broker_charge"`
	EscrowCharge  float64 `json:"escrow_charge"`
	Currency      string  `json:"currency"`
	Token         string  `json:"token"`
}

type CreatePaymentRequest struct {
	TransactionID string  `json:"transaction_id" `
	TotalAmount   float64 `json:"total_amount"`
	ShippingFee   float64 `json:"shipping_fee"`
	BrokerCharge  float64 `json:"broker_charge"`
	EscrowCharge  float64 `json:"escrow_charge"`
	Currency      string  `json:"currency"`
}

type CreatePaymentResponse struct {
	Status  string  `json:"status"`
	Code    int     `json:"code"`
	Message string  `json:"message"`
	Data    Payment `json:"data"`
}

type ListPayment struct {
	ID               int64   `gorm:"primary_key;AUTO_INCREMENT;column:id" json:"id"`
	PaymentID        string  `gorm:"column:payment_id" json:"payment_id"`
	TransactionID    string  `gorm:"column:transaction_id" json:"transaction_id"`
	TotalAmount      float64 `gorm:"column:total_amount" json:"total_amount"`
	EscrowCharge     float64 `gorm:"column:escrow_charge" json:"escrow_charge"`
	IsPaid           bool    `gorm:"column:is_paid" json:"is_paid"`
	PaymentMadeAt    string  `gorm:"column:payment_made_at" json:"payment_made_at"`
	DeletedAt        string  `gorm:"column:deleted_at" json:"deleted_at"`
	CreatedAt        string  `gorm:"column:created_at; autoCreateTime" json:"created_at"`
	UpdatedAt        string  `gorm:"column:updated_at; autoUpdateTime" json:"updated_at"`
	AccountID        int64   `gorm:"column:account_id" json:"account_id"`
	BusinessID       int64   `gorm:"column:business_id" json:"business_id"`
	Currency         string  `gorm:"column:currency" json:"currency"`
	ShippingFee      float64 `gorm:"column:shipping_fee" json:"shipping_fee"`
	DisburseCurrency string  `gorm:"column:disburse_currency" json:"disburse_currency"`
	PaymentType      string  `gorm:"column:payment_type" json:"payment_type"`
	BrokerCharge     float64 `gorm:"column:broker_charge" json:"broker_charge"`
	SummedAmount     float64 `gorm:"column:summed_amount" json:"summed_amount"`
}

type ListPaymentsResponse struct {
	Status  string                   `json:"status"`
	Code    int                      `json:"code"`
	Message string                   `json:"message"`
	Data    ListPaymentsResponseData `json:"data"`
}

type ListPaymentsResponseData struct {
	Transaction map[string]interface{} `json:"transaction"`
	Payment     ListPayment            `json:"payment"`
}
