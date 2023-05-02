package models

type SendOTP struct {
	AccountID int `json:"account_id"  validate:"required" pgvalidate:"exists=auth$users$account_id"`
	OtpToken  int `json:"otp_token"  validate:"required"`
}
