package models

type SendAuthorized struct {
	AccountID int    `json:"account_id"  validate:"required" pgvalidate:"exists=auth$users$account_id"`
	Ip        string `json:"ip" validate:"required"`
	Location  string `json:"location" validate:"required"`
	Device    string `json:"device" validate:"required"`
}

type SendAuthorization struct {
	AccountID int    `json:"account_id"  validate:"required" pgvalidate:"exists=auth$users$account_id"`
	Ip        string `json:"ip" validate:"required"`
	Token     string `json:"token" validate:"required"`
	Location  string `json:"location" validate:"required"`
	Device    string `json:"device" validate:"required"`
}
