package models

type SendEmailVerificationMail struct {
	EmailAddress string `json:"email_address"`
	AccountID    int    `json:"account_id"  validate:"required" pgvalidate:"exists=auth$users$account_id"`
	Code         uint   `json:"code" validate:"required"`
	Token        string `json:"token"`
}

type SendEmailVerifiedMail struct {
	AccountID int `json:"account_id"  validate:"required" pgvalidate:"exists=auth$users$account_id"`
}

type SendVerificationFailed struct {
	AccountID int    `json:"account_id"  validate:"required" pgvalidate:"exists=auth$users$account_id"`
	Type      string `json:"type"`
	Reason    string `json:"reason"`
}

type SendVerificationSuccessful struct {
	AccountID int    `json:"account_id"  validate:"required" pgvalidate:"exists=auth$users$account_id"`
	Type      string `json:"type"`
}
