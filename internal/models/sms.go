package models

type SendSMSToPhone struct {
	AccountID   int    `json:"account_id"  validate:"required" pgvalidate:"exists=auth$users$account_id"`
	Message     string `json:"message"  validate:"required"`
	PhoneNumber string `json:"phone_number"`
}
