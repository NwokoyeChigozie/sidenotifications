package names

type NotificationName string

const (
	SendWelcomeMail           NotificationName = "send_welcome_mail"
	SendWelcomeSMS            NotificationName = "send_welcome_sms"
	SendOTP                   NotificationName = "send_otp"
	SendWelcomePasswordMail   NotificationName = "send_welcome_password_mail"
	SendResetPasswordMail     NotificationName = "send_reset_password_mail"
	SendResetPasswordSMS      NotificationName = "send_reset_password_sms"
	SendResetPasswordDoneMail NotificationName = "send_reset_password_done_mail"
	SendResetPasswordDoneSMS  NotificationName = "send_reset_password_done_sms"
)
