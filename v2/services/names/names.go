package names

type NotificationName string

const (
	SendWelcomeMail         NotificationName = "send_welcome_mail"
	SendWelcomeSMS          NotificationName = "send_welcome_sms"
	SendOTP                 NotificationName = "send_otp"
	SendWelcomePasswordMail NotificationName = "send_welcome_password_mail"
)
