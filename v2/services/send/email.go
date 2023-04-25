package send

import (
	"fmt"
	"net/smtp"
	"strings"

	"github.com/jordan-wright/email"
	"github.com/vesicash/notifications-ms/v2/external/request"
	"github.com/vesicash/notifications-ms/v2/internal/config"
)

type EmailRequest struct {
	ExtReq  request.ExternalRequest
	To      []string `json:"to"`
	Subject string   `json:"subject"`
	Body    string   `json:"body"`
}

func NewEmailRequest(extReq request.ExternalRequest, to []string, subject, body string) *EmailRequest {
	return &EmailRequest{
		ExtReq:  extReq,
		To:      to,
		Subject: subject,
		Body:    body, //or parsed template
	}
}

func (e EmailRequest) validate() error {
	if e.Subject == "" {
		return fmt.Errorf("EMAIL::validate ==> subject is required")
	}
	if e.Body == "" {
		return fmt.Errorf("EMAIL::validate ==> body is required")
	}

	if e.To == nil {
		return fmt.Errorf("receiving email is empty")
	}

	for _, v := range e.To {
		if v == "" {
			return fmt.Errorf("receiving email is empty: %s", v)
		}

		if !strings.Contains(v, "@") {
			return fmt.Errorf("receiving email is invalid: %s", v)
		}
	}

	return nil
}

// Send sends out the body in template format
func (e *EmailRequest) Send() error {

	if err := e.validate(); err != nil {
		return err
	}

	// ctx, cancel := context.WithTimeout(context.Background(), time.Second*10)
	// defer cancel()

	// go e.sendEmailViaSMTP(request, done)
	err := e.sendEmailViaSMTP()

	if err != nil {
		e.ExtReq.Logger.Error("error sending email: ", err.Error())
		return err
	}
	return nil
}

func (e *EmailRequest) sendEmailViaSMTP() error {
	var (
		mailConfig = config.GetConfig().Mail
	)
	fmt.Println("connecting ...")
	auth := smtp.PlainAuth("", mailConfig.Username, mailConfig.Password, mailConfig.Host)
	fmt.Println("connected", auth)
	to := "To: " + strings.Join(e.To, ",") + "\r\n"
	from := "From: \"Vesicash\"\r\n"
	subject := "Subject: " + e.Subject + "\r\n"
	mime := "MIME-version: 1.0;\nContent-Type: text/html; charset=\"UTF-8\";\n\n"
	msg := []byte(to + from + subject + mime + "\r\n" + e.Body)
	addr := mailConfig.Host + ":" + mailConfig.Port

	fmt.Println("sending mail ...")
	if err := smtp.SendMail(addr, auth, mailConfig.Username, e.To, msg); err != nil {
		fmt.Println("SMTP ERROR MESSAGE", err)
		return err
	}

	fmt.Println("sent mail")

	return nil
}

func (e *EmailRequest) sendEmailViaSMTP2() error {
	var (
		mailConfig = config.GetConfig().Mail
	)
	em := email.NewEmail()
	em.From = "Vesicash <help@vesicash.com>"
	em.To = e.To
	em.Subject = e.Subject
	to := "To: " + strings.Join(e.To, ",") + "\r\n"
	from := "From: \"Vesicash\"\r\n"
	subject := "Subject: " + e.Subject + "\r\n"
	mime := "MIME-version: 1.0;\nContent-Type: text/html; charset=\"UTF-8\";\n\n"
	msg := []byte(to + from + subject + mime + "\r\n" + e.Body)
	em.Text = msg

	fmt.Println("connecting ...")
	auth := smtp.PlainAuth("", mailConfig.Username, mailConfig.Password, mailConfig.Host)
	fmt.Println("connected", auth)

	fmt.Println("sending mail ...")
	err := em.Send(fmt.Sprintf("%v:%v", mailConfig.Host, mailConfig.Port), auth)
	if err != nil {
		fmt.Println("SMTP ERROR MESSAGE", err)
		return err
	}
	fmt.Println("sent mail")

	return nil
}
