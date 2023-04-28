package send

import (
	"fmt"

	"github.com/vesicash/notifications-ms/external/external_models"
	"github.com/vesicash/notifications-ms/external/request"
)

type SMSRequest struct {
	ExtReq request.ExternalRequest
	Body   string `json:"body"`
	Media  string `json:"media"` //url to media
	To     string `json:"to"`    //phone number
}

func NewSMSRequest(extReq request.ExternalRequest, to, templateFileName string, templateData map[string]interface{}) (*SMSRequest, error) {
	body, err := ParseSMSTemplate(extReq, templateFileName, templateData)
	if err != nil {
		return &SMSRequest{}, err
	}
	return &SMSRequest{ExtReq: extReq, Body: body, To: to}, nil
}

func NewSimpleSMSRequest(extReq request.ExternalRequest, to, body string) *SMSRequest {
	return &SMSRequest{ExtReq: extReq, Body: body, To: to}
}

func SendSimpleSMS(extReq request.ExternalRequest, to, body string) error {
	err := NewSimpleSMSRequest(extReq, to, body).Send()
	if err != nil {
		return fmt.Errorf("error getting sms, %v", err)
	}

	return nil
}

func (s SMSRequest) validate() error {

	if s.Body == "" {
		return fmt.Errorf("SMS::validate ==> subject is required")
	}

	if s.To == "" {
		return fmt.Errorf("SMS::validate ==> receiving phone number is empty: %s", s.To)
	}
	return nil
}

// Send sends out sms text messages
func (s *SMSRequest) Send() error {

	if err := s.validate(); err != nil {
		return err
	}

	// ctx, cancel := context.WithTimeout(context.Background(), time.Second*10)
	// defer cancel()

	err := s.sendSMSViaTermii()
	if err != nil {
		s.ExtReq.Logger.Error("error sending sms: ", err.Error())
		return err
	}
	return nil

}

func (s *SMSRequest) sendSMSViaTermii() error {
	smsItf, err := s.ExtReq.SendExternalRequest(request.TermiiSendSMS, external_models.TermiiSendSMSRequest{
		From:    "Vesicash",
		Type:    "plain",
		Channel: "generic",
		To:      s.To,
		Sms:     s.Body,
	})
	if err != nil {
		return err
	}

	smsResponse, ok := smsItf.(external_models.TermiiSendSMSReponse)
	if !ok {
		return fmt.Errorf("response data format error")
	}

	s.ExtReq.Logger.Info("sms sent", smsResponse)
	return nil
}
