package models

type SendEmailVerificationMail struct {
	EmailAddress string `json:"email_address"`
	AccountID    int    `json:"account_id"  validate:"required" pgvalidate:"exists=auth$users$account_id"`
	Code         uint   `json:"code" validate:"required"`
	Token        string `json:"token" validate:"required"`
}
type SendEmailVerifiedMail struct {
	AccountID int `json:"account_id"  validate:"required" pgvalidate:"exists=auth$users$account_id"`
}
