package external_models

type TermiiSendSMSRequest struct {
	From    string `json:"from"`
	Type    string `json:"type"`
	Channel string `json:"channel"`
	To      string `json:"to"`
	Sms     string `json:"sms"`
}

type TermiiSendSMSReponse struct {
	Code      string  `json:"code"`
	MessageID string  `json:"message_id"`
	Message   string  `json:"message"`
	Balance   float64 `json:"balance"`
	User      string  `json:"user"`
}
