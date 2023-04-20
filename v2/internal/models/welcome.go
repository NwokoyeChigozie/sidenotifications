package models

type SendWelcomeMail struct {
	AccountID int `json:"account_id"  validate:"required" pgvalidate:"exists=auth$users$account_id"`
}
