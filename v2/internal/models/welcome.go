package models

type SendWelcomeMail struct {
	AccountID int `json:"account_id"  validate:"required" pgvalidate:"exists=auth$users$account_id"`
}

type SendWelcomeSMS struct {
	AccountID int `json:"account_id"  validate:"required" pgvalidate:"exists=auth$users$account_id"`
}
type SendWelcomePasswordMail struct {
	AccountID int `json:"account_id"  validate:"required" pgvalidate:"exists=auth$users$account_id"`
	Token     int `json:"token"  validate:"required"`
}
