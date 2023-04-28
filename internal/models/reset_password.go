package models

type SendResetPassword struct {
	AccountID int `json:"account_id"  validate:"required" pgvalidate:"exists=auth$users$account_id"`
	Token     int `json:"token"  validate:"required"`
}
type SendResetPasswordDone struct {
	AccountID int `json:"account_id"  validate:"required" pgvalidate:"exists=auth$users$account_id"`
}
